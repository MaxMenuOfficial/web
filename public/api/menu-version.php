<?php
// api/menu-version.php
header("Access-Control-Allow-Origin: *"); // o el origen que quieras
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: false');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ... aquí la lógica de tu endpoint ...
require_once __DIR__ . '/../../config/get_restaurant_id.php';
require_once __DIR__ . '/../../config/menu-service.php';
header('Content-Type: application/json');

// get_restaurant_id.php define y valida $restaurantId aquí
if (empty($restaurantId)) {
    http_response_code(400);
    echo json_encode(['error' => 'missing id']);
    exit;
}

$svc  = new MenuService();
$data = $svc->getRestaurantPublicData($restaurantId, true); // ← 🔥 importante


if (!$data || !isset($data['menu_version'])) {
    http_response_code(404);
    echo json_encode(['error' => 'not found']);
    exit;
}

$version = is_numeric($data['menu_version']) ? (int)$data['menu_version'] : 0;
echo json_encode(['version' => $version]);