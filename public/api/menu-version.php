<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=31536000, immutable');   // 1 año en edge
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

/* ── Obtener ID + versión desde la ruta ─────────────────────────────── */
$uri = $_SERVER['REQUEST_URI'];
if (preg_match('#/api/menu-version/([A-Za-z0-9_-]+)/([0-9]+)#', $uri, $m)) {
    [$_, $restaurantId, $pathVersion] = $m;
} elseif (preg_match('#/api/menu-version/([A-Za-z0-9_-]+)#', $uri, $m)) {
    [$_, $restaurantId] = $m;
    $pathVersion = null;
} else {
    $restaurantId = $_GET['id'] ?? null;
    $pathVersion  = $_GET['v']  ?? null;
}
if (!$restaurantId) {
    http_response_code(400);
    exit(json_encode(['error' => 'Restaurant ID requerido']));
}

/* ── Cargar datos (en caché de memoria) ─────────────────────────────── */
require_once __DIR__.'/../../config/menu-service.php';
$svc   = new MenuService();
$data  = $svc->getRestaurantPublicData($restaurantId, false);
if (!$data || !isset($data['menu_version'])) {
    http_response_code(404);
    exit(json_encode(['error' => 'No encontrado']));
}
$currVersion = (int)$data['menu_version'];

/* ── Si la versión del path existe y coincide, responde 304 ─────────── */
if ($pathVersion !== null && (int)$pathVersion === $currVersion) {
    http_response_code(304);   // Not Modified
    exit;                      // sin cuerpo
}

/* ── Responder la versión actual ────────────────────────────────────── */
echo json_encode(['version' => $currVersion]);

