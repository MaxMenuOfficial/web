<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Cache para 1 año en Cloudflare Edge y navegador
header('Cache-Control: public, max-age=31536000, immutable');

// Last-Modified para ayudar con cache validation
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

require_once __DIR__ . '/../../config/menu-service.php';

$restaurantId = $_GET['id'] ?? null;
if (!$restaurantId) {
    http_response_code(400);
    echo json_encode(['error' => 'Restaurant ID requerido']);
    exit;
}

try {
    $svc  = new MenuService();
    // No forzar la recarga para aprovechar caché en memoria
    $data = $svc->getRestaurantPublicData($restaurantId, false);

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