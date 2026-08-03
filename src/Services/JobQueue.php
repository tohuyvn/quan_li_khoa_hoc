<?php

namespace App\Services;

use App\DB;
use App\Mailer;
use App\OrderMailer;

class JobQueue
{
    public static function push(string $jobType, array $payload, string $queue = 'default', int $delaySeconds = 0): int
    {
        $db = DB::connection();
        $stmt = $db->prepare('
            INSERT INTO job_queue (queue, job_type, payload, available_at)
            VALUES (?, ?, ?, ?)
        ');
        $available = date('Y-m-d H:i:s', time() + max(0, $delaySeconds));
        $stmt->execute([$queue, $jobType, json_encode($payload, JSON_UNESCAPED_UNICODE), $available]);

        return (int) $db->lastInsertId();
    }

    public static function process(int $limit = 10): int
    {
        $db = DB::connection();
        $done = 0;

        for ($i = 0; $i < $limit; $i++) {
            $db->beginTransaction();
            try {
                $stmt = $db->query("
                    SELECT * FROM job_queue
                    WHERE completed_at IS NULL
                      AND available_at <= NOW()
                      AND (reserved_at IS NULL OR reserved_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE))
                    ORDER BY id ASC
                    LIMIT 1
                    FOR UPDATE
                ");
                $job = $stmt ? $stmt->fetch(\PDO::FETCH_ASSOC) : false;
                if (!$job) {
                    $db->commit();
                    break;
                }

                $db->prepare('UPDATE job_queue SET reserved_at = NOW(), attempts = attempts + 1 WHERE id = ?')
                    ->execute([(int) $job['id']]);
                $db->commit();

                try {
                    self::dispatch($job);
                    $db->prepare('UPDATE job_queue SET completed_at = NOW(), last_error = NULL WHERE id = ?')
                        ->execute([(int) $job['id']]);
                    $done++;
                } catch (\Throwable $e) {
                    $db->prepare('UPDATE job_queue SET reserved_at = NULL, last_error = ?, available_at = DATE_ADD(NOW(), INTERVAL 1 MINUTE) WHERE id = ?')
                        ->execute([mb_substr($e->getMessage(), 0, 500), (int) $job['id']]);
                }
            } catch (\Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                break;
            }
        }

        return $done;
    }

    private static function dispatch(array $job): void
    {
        $payload = json_decode($job['payload'] ?? '{}', true) ?: [];
        $type = $job['job_type'] ?? '';

        switch ($type) {
            case 'mail':
                Mailer::send(
                    (string) ($payload['to'] ?? ''),
                    (string) ($payload['subject'] ?? ''),
                    (string) ($payload['body'] ?? '')
                );
                break;
            case 'order_status_mail':
                if (!empty($payload['order'])) {
                    OrderMailer::sendStatusUpdate($payload['order']);
                }
                break;
            case 'order_notify':
                NotificationService::notifyOrderStatus(
                    (int) ($payload['order_id'] ?? 0),
                    (string) ($payload['status'] ?? '')
                );
                break;
            default:
                throw new \RuntimeException('Unknown job type: ' . $type);
        }
    }
}
