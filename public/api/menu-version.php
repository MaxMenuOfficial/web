<?php
// File: /api/menu-version.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=31536000, immutable'); // ⚠️ NUEVO HEADER
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

$restaurantId = $_GET['id'] ?? null;
if (!$restaurantId) {
    http_response_code(400);
    echo json_encode(['error' => 'Restaurant ID requerido']);
    exit;
}

// ⚠️ Esto debe ser FALSE, para permitir lectura desde caché si existe
$force = false;

require_once __DIR__ . '/../../config/menu-service.php';

try {
    $svc  = new MenuService();
    $data = $svc->getRestaurantPublicData($restaurantId, $force);

    if (!$data || !isset($data['menu_version'])) {
        http_response_code(404);
        echo json_encode(['error' => 'Datos no encontrados']);
        exit;
    }

    echo json_encode(['version' => (int) $data['menu_version']]);
} catch (Throwable $e) {
    error_log("❌ menu-version.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno']);
}