<?php

namespace App;

class OllamaClient
{
    public static function isEnabled(): bool
    {
        return (bool) config('ollama.enabled', false);
    }

    public static function isAvailable(): bool
    {
        if (!self::isEnabled()) {
            return false;
        }

        $url = rtrim((string) config('ollama.url', 'http://127.0.0.1:11434'), '/');
        $ch = curl_init($url . '/api/tags');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_CONNECTTIMEOUT => 2,
        ]);
        curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    /**
     * @param array<int, array{role: string, content: string}> $messages
     */
    public static function chat(array $messages, string $systemPrompt = ''): string
    {
        if (!self::isEnabled()) {
            return '';
        }

        $url = rtrim((string) config('ollama.url', 'http://127.0.0.1:11434'), '/');
        $payload = [
            'model' => (string) config('ollama.model', 'qwen2.5:7b'),
            'messages' => $messages,
            'stream' => false,
            'options' => [
                'temperature' => (float) config('ollama.temperature', 0.6),
                'num_predict' => (int) config('ollama.max_tokens', 500),
            ],
        ];

        if ($systemPrompt !== '') {
            array_unshift($payload['messages'], [
                'role' => 'system',
                'content' => $systemPrompt,
            ]);
        }

        $ch = curl_init($url . '/api/chat');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => (int) config('ollama.timeout', 30),
        ]);
        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            $reply = trim($data['message']['content'] ?? '');
            if ($reply !== '') {
                return $reply;
            }
        }

        self::log($httpCode, $response ?: $error);

        return '';
    }

    private static function log(int $httpCode, string $message): void
    {
        $logDir = BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
        file_put_contents(
            $logDir . '/ollama.log',
            sprintf("[%s] HTTP %d | %s\n", date('Y-m-d H:i:s'), $httpCode, mb_substr($message, 0, 500)),
            FILE_APPEND
        );
    }
}
