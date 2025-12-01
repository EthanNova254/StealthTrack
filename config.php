<?php
/**
 * Configuration & Database
 * File: config.php
 */

class Config {
    public static function get($key, $default = null) {
        $config = [
            'admin_password' => getenv('ADMIN_PASSWORD') ?: 'change_this_password_123',
            'api_key' => getenv('API_KEY') ?: 'your_secure_api_key_here',
            'ipinfo_token' => getenv('IPINFO_TOKEN') ?: '',
            'max_file_size' => 10485760,
            'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'pdf'],
            'location_retention_hours' => 60,
            'max_content_expiry_days' => 7,
            'items_per_page' => 50
        ];
        return $config[$key] ?? $default;
    }
}

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dbPath = __DIR__ . '/data/tracker.db';
        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) mkdir($dbDir, 0755, true);

        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initDatabase();
    }

    public static function getInstance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    private function initDatabase() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS locations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                slug TEXT NOT NULL UNIQUE,
                latitude REAL,
                longitude REAL,
                ip_address TEXT,
                user_agent TEXT,
                location_type TEXT DEFAULT 'gps',
                city TEXT,
                region TEXT,
                country TEXT,
                accuracy TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE INDEX IF NOT EXISTS idx_locations_slug ON locations(slug);
            CREATE INDEX IF NOT EXISTS idx_locations_created ON locations(created_at);
            CREATE INDEX IF NOT EXISTS idx_locations_type ON locations(location_type);
            
            CREATE TABLE IF NOT EXISTS content (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                slug TEXT NOT NULL UNIQUE,
                title TEXT NOT NULL,
                description TEXT,
                content_type TEXT NOT NULL,
                file_path TEXT,
                external_url TEXT,
                expires_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                view_count INTEGER DEFAULT 0
            );
            CREATE INDEX IF NOT EXISTS idx_content_slug ON content(slug);
            CREATE INDEX IF NOT EXISTS idx_content_expires ON content(expires_at);
            
            CREATE TABLE IF NOT EXISTS sessions (
                id TEXT PRIMARY KEY,
                data TEXT,
                expires_at INTEGER
            );
        ");
    }

    public function cleanup() {
        $retentionHours = Config::get('location_retention_hours');
        $this->pdo->exec("DELETE FROM locations WHERE created_at < datetime('now', '-{$retentionHours} hours')");
        $this->pdo->exec("DELETE FROM content WHERE expires_at IS NOT NULL AND expires_at < datetime('now')");
        $this->pdo->exec("DELETE FROM sessions WHERE expires_at < " . time());
    }
}
