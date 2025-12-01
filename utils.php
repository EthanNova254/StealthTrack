<?php
/**
 * Utility Functions
 * File: utils.php
 */

class Utils {
    public static function generateSlug($length = 8) {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $slug = '';
        for ($i = 0; $i < $length; $i++) {
            $slug .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $slug;
    }

    public static function sanitizeInput($data) {
        if (is_array($data)) return array_map([self::class, 'sanitizeInput'], $data);
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    public static function validateCoordinates($lat, $lon) {
        return is_numeric($lat) && is_numeric($lon) && $lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180;
    }

    public static function getClientIp() {
        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = explode(',', $_SERVER[$header])[0];
                return filter_var(trim($ip), FILTER_VALIDATE_IP) ?: '0.0.0.0';
            }
        }
        return '0.0.0.0';
    }

    public static function getIpGeolocation($ip) {
        $token = Config::get('ipinfo_token');
        if ($token) {
            try {
                $url = "https://ipinfo.io/{$ip}?token={$token}";
                $response = @file_get_contents($url);
                if ($response) {
                    $data = json_decode($response, true);
                    if (isset($data['loc'])) {
                        list($lat, $lon) = explode(',', $data['loc']);
                        return ['latitude' => floatval($lat), 'longitude' => floatval($lon), 'city' => $data['city'] ?? null, 'region' => $data['region'] ?? null, 'country' => $data['country'] ?? null, 'accuracy' => 'city'];
                    }
                }
            } catch (Exception $e) {}
        }
        try {
            $url = "http://ip-api.com/json/{$ip}";
            $response = @file_get_contents($url);
            if ($response) {
                $data = json_decode($response, true);
                if ($data['status'] === 'success') {
                    return ['latitude' => floatval($data['lat']), 'longitude' => floatval($data['lon']), 'city' => $data['city'] ?? null, 'region' => $data['regionName'] ?? null, 'country' => $data['country'] ?? null, 'accuracy' => 'city'];
                }
            }
        } catch (Exception $e) {}
        return null;
    }

    public static function uploadFile($file) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = Config::get('allowed_extensions');
        if (!in_array($extension, $allowedExtensions)) throw new Exception('Invalid file type');
        if ($file['size'] > Config::get('max_file_size')) throw new Exception('File too large');
        $fileName = self::generateSlug(16) . '.' . $extension;
        $filePath = $uploadDir . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $filePath)) throw new Exception('Upload failed');
        return $fileName;
    }

    public static function getMimeType($extension) {
        $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'pdf' => 'application/pdf'];
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    public static function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
