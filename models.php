<?php
/**
 * Data Models
 * File: models.php
 */

class Location {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($slug, $latitude, $longitude, $ipAddress, $userAgent, $locationType = 'gps', $city = null, $region = null, $country = null, $accuracy = null) {
        $stmt = $this->db->prepare("INSERT INTO locations (slug, latitude, longitude, ip_address, user_agent, location_type, city, region, country, accuracy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$slug, $latitude, $longitude, $ipAddress, $userAgent, $locationType, $city, $region, $country, $accuracy]);
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM locations ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll();
    }

    public function getCount() {
        return $this->db->query("SELECT COUNT(*) FROM locations")->fetchColumn();
    }

    public function getStats() {
        $total = $this->getCount();
        $last24h = $this->db->query("SELECT COUNT(*) FROM locations WHERE created_at > datetime('now', '-24 hours')")->fetchColumn();
        $gpsCount = $this->db->query("SELECT COUNT(*) FROM locations WHERE location_type = 'gps'")->fetchColumn();
        $ipCount = $this->db->query("SELECT COUNT(*) FROM locations WHERE location_type = 'ip'")->fetchColumn();
        return ['total' => $total, 'last_24h' => $last24h, 'gps_locations' => $gpsCount, 'ip_locations' => $ipCount];
    }
}

class Content {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($slug, $title, $description, $contentType, $filePath, $externalUrl, $expiresAt) {
        $stmt = $this->db->prepare("INSERT INTO content (slug, title, description, content_type, file_path, external_url, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$slug, $title, $description, $contentType, $filePath, $externalUrl, $expiresAt]);
    }

    public function getBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM content WHERE slug = ? AND (expires_at IS NULL OR expires_at > datetime('now'))");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM content ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll();
    }

    public function incrementView($slug) {
        $stmt = $this->db->prepare("UPDATE content SET view_count = view_count + 1 WHERE slug = ?");
        return $stmt->execute([$slug]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("SELECT file_path FROM content WHERE id = ?");
        $stmt->execute([$id]);
        $content = $stmt->fetch();
        if ($content && $content['file_path']) {
            $filePath = __DIR__ . '/uploads/' . $content['file_path'];
            if (file_exists($filePath)) unlink($filePath);
        }
        $stmt = $this->db->prepare("DELETE FROM content WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCount() {
        return $this->db->query("SELECT COUNT(*) FROM content")->fetchColumn();
    }
}
