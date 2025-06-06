<?php
// api/menu-version.php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: false');

$method = $_SERVER['REQUEST_METHOD'];
if (!in_array($method, ['GET','POST','OPTIONS'])) {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/get_restaurant_id.php';
require_once __DIR__ . '/../../config/menu-service.php';

if (empty($restaurantId)) {
    http_response_code(400);
    echo json_encode(['error' => 'missing id']);
    exit;
}

try {
    $svc = new MenuService();
    $data = $svc->getRestaurantPublicData($restaurantId, true);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
    error_log("Error in menu-version.php: " . $e->getMessage());
    exit;
}

if (!$data || !isset($data['menu_version'])) {
    http_response_code(404);
    echo json_encode(['error' => 'not found']);
    exit;
}

$version = (int) $data['menu_version'];
echo json_encode(['version' => $version]);

?>