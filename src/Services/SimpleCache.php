<?php

namespace App\Services;

use App\DB;

class SimpleCache
{
    private static function dir(): string
    {
        $dir = BASE_PATH . '/storage/cache';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        return $dir;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $file = self::dir() . '/' . self::hash($key) . '.cache';
        if (!is_file($file)) {
            return $default;
        }
        $raw = @file_get_contents($file);
        if ($raw === false) {
            return $default;
        }
        $data = @unserialize($raw);
        if (!is_array($data) || !isset($data['expires'], $data['value'])) {
            return $default;
        }
        if ($data['expires'] > 0 && $data['expires'] < time()) {
            @unlink($file);
            return $default;
        }

        return $data['value'];
    }

    public static function put(string $key, mixed $value, int $ttlSeconds = 60): void
    {
        $payload = serialize([
            'expires' => $ttlSeconds > 0 ? time() + $ttlSeconds : 0,
            'value' => $value,
        ]);
        @file_put_contents(self::dir() . '/' . self::hash($key) . '.cache', $payload, LOCK_EX);
    }

    public static function remember(string $key, int $ttlSeconds, callable $callback): mixed
    {
        $cached = self::get($key, null);
        if ($cached !== null) {
            return $cached;
        }
        $value = $callback();
        self::put($key, $value, $ttlSeconds);

        return $value;
    }

    public static function forget(string $key): void
    {
        $file = self::dir() . '/' . self::hash($key) . '.cache';
        if (is_file($file)) {
            @unlink($file);
        }
    }

    public static function flush(): void
    {
        foreach (glob(self::dir() . '/*.cache') ?: [] as $file) {
            @unlink($file);
        }
    }

    private static function hash(string $key): string
    {
        return hash('sha256', $key);
    }
}
