<?php
/**
 * Application Routes - Part 1
 * File: routes.php
 */

// Homepage
$router->get('/', function() {
    include 'views/home.php';
});

// Tracking page
$router->get('/track/{slug}', function($slug) {
    include 'views/track.php';
});

// API: Track location
$router->post('/api/track', function() {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['slug'])) Utils::jsonResponse(['error' => 'Missing slug'], 400);
    
    $slug = Utils::sanitizeInput($data['slug']);
    $location = new Location();
    $ipAddress = Utils::getClientIp();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    if (isset($data['latitude'], $data['longitude'])) {
        $lat = floatval($data['latitude']);
        $lon = floatval($data['longitude']);
        if (Utils::validateCoordinates($lat, $lon)) {
            try {
                $location->create($slug, $lat, $lon, $ipAddress, $userAgent, 'gps', null, null, null, 'high');
                Utils::jsonResponse(['success' => true, 'message' => 'Location tracked (GPS)', 'type' => 'gps']);
            } catch (Exception $e) {
                Utils::jsonResponse(['error' => 'Failed to save location'], 500);
            }
        }
    }
    
    $geoData = Utils::getIpGeolocation($ipAddress);
    if ($geoData) {
        try {
            $location->create($slug, $geoData['latitude'], $geoData['longitude'], $ipAddress, $userAgent, 'ip', $geoData['city'], $geoData['region'], $geoData['country'], $geoData['accuracy']);
            Utils::jsonResponse(['success' => true, 'message' => 'Location tracked (IP-based)', 'type' => 'ip', 'city' => $geoData['city'], 'country' => $geoData['country']]);
        } catch (Exception $e) {
            Utils::jsonResponse(['error' => 'Failed to save location'], 500);
        }
    }
    
    Utils::jsonResponse(['error' => 'Could not determine location'], 400);
});

// Content page
$router->get('/content/{slug}', function($slug) {
    $content = new Content();
    $item = $content->getBySlug($slug);
    if (!$item) {
        http_response_code(404);
        include 'views/404.php';
        return;
    }
    $content->incrementView($slug);
    include 'views/content.php';
});

// Serve files
$router->get('/serve/{filename}', function($filename) {
    $filePath = __DIR__ . '/uploads/' . basename($filename);
    if (!file_exists($filePath)) {
        http_response_code(404);
        echo "File not found";
        return;
    }
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $mimeType = Utils::getMimeType($extension);
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: public, max-age=86400');
    readfile($filePath);
});

// Admin routes
$router->get('/manage', function() {
    Auth::requireLogin();
    include 'views/admin/dashboard.php';
});

$router->get('/manage/login', function() {
    if (Auth::isLoggedIn()) {
        header('Location: /manage');
        exit;
    }
    include 'views/admin/login.php';
});

$router->post('/manage/login', function() {
    $password = $_POST['password'] ?? '';
    if (Auth::login($password)) {
        header('Location: /manage');
    } else {
        Auth::startSession();
        $_SESSION['error'] = 'Invalid password';
        header('Location: /manage/login');
    }
    exit;
});

$router->get('/manage/logout', function() {
    Auth::logout();
    header('Location: /manage/login');
    exit;
});

$router->post('/manage/content/create', function() {
    Auth::requireLogin();
    Auth::startSession();
    $slug = Utils::generateSlug();
    $title = Utils::sanitizeInput($_POST['title']);
    $description = Utils::sanitizeInput($_POST['description'] ?? '');
    $contentType = $_POST['content_type'];
    $expiryDays = min(intval($_POST['expiry_days'] ?? 7), Config::get('max_content_expiry_days'));
    $filePath = null;
    $externalUrl = null;
    try {
        if ($contentType === 'file' && isset($_FILES['file'])) {
            $filePath = Utils::uploadFile($_FILES['file']);
        } elseif ($contentType === 'link') {
            $externalUrl = filter_var($_POST['url'], FILTER_VALIDATE_URL);
            if (!$externalUrl) throw new Exception('Invalid URL');
        }
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryDays} days"));
        $content = new Content();
        $content->create($slug, $title, $description, $contentType, $filePath, $externalUrl, $expiresAt);
        $_SESSION['success'] = "Content created! Link: /content/{$slug}";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    header('Location: /manage');
    exit;
});

$router->post('/manage/content/delete/{id}', function($id) {
    Auth::requireLogin();
    $content = new Content();
    $content->delete($id);
    header('Location: /manage');
    exit;
});

// API routes
$router->get('/api/locations', function() {
    $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? '';
    if (!Auth::verifyApiKey($apiKey)) Utils::jsonResponse(['error' => 'Invalid API key'], 401);
    $location = new Location();
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = Config::get('items_per_page');
    $offset = ($page - 1) * $limit;
    $locations = $location->getAll($limit, $offset);
    $total = $location->getCount();
    Utils::jsonResponse(['data' => $locations, 'page' => $page, 'total' => $total, 'pages' => ceil($total / $limit)]);
});

$router->get('/api/content', function() {
    $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? '';
    if (!Auth::verifyApiKey($apiKey)) Utils::jsonResponse(['error' => 'Invalid API key'], 401);
    $content = new Content();
    $items = $content->getAll();
    Utils::jsonResponse(['data' => $items]);
});

$router->get('/api/stats', function() {
    $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? '';
    if (!Auth::verifyApiKey($apiKey)) Utils::jsonResponse(['error' => 'Invalid API key'], 401);
    $location = new Location();
    $content = new Content();
    Utils::jsonResponse(['locations' => $location->getStats(), 'content_items' => $content->getCount()]);
});

$router->get('/health', function() {
    Utils::jsonResponse(['status' => 'healthy', 'timestamp' => time()]);
});
