<?php
/**
 * Authentication System
 * File: auth.php
 */

class Auth {
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            session_start();
        }
    }

    public static function login($password) {
        if ($password === Config::get('admin_password')) {
            self::startSession();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
            return true;
        }
        return false;
    }

    public static function logout() {
        self::startSession();
        session_destroy();
    }

    public static function isLoggedIn() {
        self::startSession();
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            if (time() - $_SESSION['login_time'] > 3600) {
                self::logout();
                return false;
            }
            return true;
        }
        return false;
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: /manage/login');
            exit;
        }
    }

    public static function verifyApiKey($key) {
        return $key === Config::get('api_key');
    }
}
