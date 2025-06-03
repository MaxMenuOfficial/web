<?php
require_once __DIR__ . '/../config/get_restaurant_id.php';
require_once __DIR__ . '/../config/menu-service.php';

header('Content-Type: application/json');

// get_restaurant_id.php define y valida $restaurantId aquí
if (empty($restaurantId)) {
    http_response_code(400);
    echo json_encode(['error' => 'missing id']);
    exit;
}

$svc  = new MenuService();
$data = $svc->getRestaurantPublicData($restaurantId);

if (!$data || !isset($data['menu_version'])) {
    http_response_code(404);
    echo json_encode(['error' => 'not found']);
    exit;
}

$version = is_numeric($data['menu_version']) ? (int)$data['menu_version'] : 0;
header('Content-Type: application/json');
echo json_encode(['version' => $version]);        // SOLO esto en la respuesta
error_log("🚩 menu-version para $restaurantId: $version"); // Esto SOLO a logs

