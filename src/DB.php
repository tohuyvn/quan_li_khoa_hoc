<?php

namespace App;

use PDO;
use PDOException;
use Throwable;

class DB
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        if (!extension_loaded('pdo_mysql')) {
            throw new \RuntimeException(
                'Thiếu PHP extension pdo_mysql. Hãy chạy server bằng XAMPP PHP: '
                . '/opt/lampp/bin/php -S 127.0.0.1:8000 -t public public/router.php'
            );
        }

        $host = config('database.host', '127.0.0.1');
        $port = (int) config('database.port', 3306);
        $name = config('database.name', 'shop_hades');
        $user = config('database.user', 'root');
        $pass = config('database.password', '');
        $charset = config('database.charset', 'utf8mb4');
        $socket = config('database.socket', '');
        $connectTimeout = max(1, (int) config('database.connect_timeout', 3));
        $retries = max(1, (int) config('database.connect_retries', 2));
        $retryDelayMs = max(0, (int) config('database.retry_delay_ms', 150));

        try {
            self::$pdo = self::connectDatabase($host, $port, $name, $user, $pass, $charset, $socket, $connectTimeout, $retries, $retryDelayMs);
        } catch (PDOException $e) {
            if (!str_contains($e->getMessage(), 'Unknown database')) {
                throw $e;
            }

            $tmp = self::connectServer($host, $port, $user, $pass, $charset, $socket, $connectTimeout, $retries, $retryDelayMs);
            $tmp->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            self::$pdo = self::connectDatabase($host, $port, $name, $user, $pass, $charset, $socket, $connectTimeout, $retries, $retryDelayMs);
        }

        return self::$pdo;
    }

    public static function isAvailable(): bool
    {
        try {
            self::connection()->query('SELECT 1');
            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public static function isConnectivityError(Throwable $e): bool
    {
        $message = $e->getMessage();

        return str_contains($message, 'SQLSTATE[HY000] [2002]')
            || str_contains($message, 'Connection refused')
            || str_contains($message, 'No such file or directory')
            || str_contains($message, 'Connection timed out');
    }

    public static function health(): array
    {
        $host = (string) config('database.host', '127.0.0.1');
        $port = (int) config('database.port', 3306);
        $database = (string) config('database.name', 'shop_hades');

        try {
            self::connection()->query('SELECT 1');

            return [
                'ok' => true,
                'database' => $database,
                'host' => $host,
                'port' => $port,
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'database' => $database,
                'host' => $host,
                'port' => $port,
                'error' => $e->getMessage(),
            ];
        }
    }

    private static function connectDatabase(
        string $host,
        int $port,
        string $name,
        string $user,
        string $pass,
        string $charset,
        string $socket,
        int $connectTimeout,
        int $retries,
        int $retryDelayMs
    ): PDO {
        $attempts = self::connectionTargets($host, $port, $name, $charset, $socket);

        return self::connectWithFallback($attempts, $user, $pass, $connectTimeout, $retries, $retryDelayMs);
    }

    private static function connectServer(
        string $host,
        int $port,
        string $user,
        string $pass,
        string $charset,
        string $socket,
        int $connectTimeout,
        int $retries,
        int $retryDelayMs
    ): PDO {
        $targets = [];

        if ($socket !== '') {
            $targets[] = "mysql:unix_socket={$socket};charset={$charset}";
        }

        $targets[] = "mysql:host={$host};port={$port};charset={$charset}";

        if ($host === 'localhost') {
            $targets[] = "mysql:host=127.0.0.1;port={$port};charset={$charset}";
        } elseif ($host === '127.0.0.1') {
            $targets[] = "mysql:host=localhost;port={$port};charset={$charset}";
        }

        return self::connectWithFallback(array_values(array_unique($targets)), $user, $pass, $connectTimeout, $retries, $retryDelayMs);
    }

    private static function connectionTargets(string $host, int $port, string $name, string $charset, string $socket): array
    {
        $targets = [];

        if ($socket !== '') {
            $targets[] = "mysql:unix_socket={$socket};dbname={$name};charset={$charset}";
        }

        $targets[] = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        if ($host === 'localhost') {
            $targets[] = "mysql:host=127.0.0.1;port={$port};dbname={$name};charset={$charset}";
        } elseif ($host === '127.0.0.1') {
            $targets[] = "mysql:host=localhost;port={$port};dbname={$name};charset={$charset}";
        }

        return array_values(array_unique($targets));
    }

    private static function connectWithFallback(
        array $targets,
        string $user,
        string $pass,
        int $connectTimeout,
        int $retries,
        int $retryDelayMs
    ): PDO {
        $lastException = null;

        foreach ($targets as $dsn) {
            for ($attempt = 1; $attempt <= $retries; $attempt++) {
                try {
                    return new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_TIMEOUT => $connectTimeout,
                    ]);
                } catch (PDOException $e) {
                    $lastException = $e;

                    if ($attempt < $retries) {
                        usleep($retryDelayMs * 1000);
                    }
                }
            }
        }

        throw $lastException ?? new PDOException('Unable to connect to database.');
    }
}
