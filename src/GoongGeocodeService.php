<?php

namespace App;

class GoongGeocodeService
{
    /**
     * Cache geocoded address results during request execution
     */
    private static array $cache = [];

    public static function geocode(string $address): ?array
    {
        $address = trim($address);
        if ($address === '') {
            return null;
        }

        $cacheKey = md5(mb_strtolower($address, 'UTF-8'));
        if (array_key_exists($cacheKey, self::$cache)) {
            return self::$cache[$cacheKey];
        }

        $key = config('goong.api_key');
        if (empty($key)) {
            self::$cache[$cacheKey] = null;
            return null;
        }

        $baseUrl = config('goong.geocode_url');
        if (empty($baseUrl)) {
            self::$cache[$cacheKey] = null;
            return null;
        }

        $url = $baseUrl . '?address=' . urlencode($address) . '&api_key=' . $key;
        
        try {
            $response = false;

            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL            => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT        => 4,
                    CURLOPT_CONNECTTIMEOUT => 2,
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
                $response = curl_exec($ch);
                curl_close($ch);
            } else {
                $context = stream_context_create([
                    'http' => [
                        'timeout'       => 4,
                        'ignore_errors' => true
                    ]
                ]);
                $response = @file_get_contents($url, false, $context);
            }

            if ($response === false) {
                self::$cache[$cacheKey] = null;
                return null;
            }

            $res = json_decode($response, true);
            $loc = $res['results'][0]['geometry']['location'] ?? null;
            
            $result = ($loc && isset($loc['lat'], $loc['lng'])) 
                ? ['lat' => (float)$loc['lat'], 'lng' => (float)$loc['lng']] 
                : null;

            self::$cache[$cacheKey] = $result;
            return $result;
        } catch (\Throwable $e) {
            self::$cache[$cacheKey] = null;
            return null;
        }
    }
}

