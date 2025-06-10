<?php
// ============================
// 🔐 CABECERAS HTTP ESTRICTAS PARA API JSON
// ============================

// MIME + Seguridad
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// CORS (acceso solo a dominios permitidos)
require_once __DIR__ . '/../../get/get_domains.php'; // 👈 usa tu mismo validador que en el widget

// Cacheo (controlado por menu_version, se cachea forever si no cambia)
header('Cache-Control: public, max-age=31536000, immutable');
// Opcional: indicar que el contenido puede ser cacheado por CDN según origin
header('Vary: Origin, Accept-Encoding');

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