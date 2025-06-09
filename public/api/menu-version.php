<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header_remove('Set-Cookie');                 // ⚠️ evita romper el caché

/* ── Parsear la ruta -------------------------------------------------- */
$uri = $_SERVER['REQUEST_URI'];
if (preg_match('#^/api/menu-version/([A-Za-z0-9_-]+)/([0-9]+)#', $uri, $m)) {
    [$_, $restaurantId, $pathVersion] = $m;
    $pathVersion = (int)$pathVersion;
} elseif (preg_match('#^/api/menu-version/([A-Za-z0-9_-]+)#', $uri, $m)) {
    [$_, $restaurantId] = $m;
    $pathVersion = null;
} else {
    $restaurantId = $_GET['id'] ?? null;
    $pathVersion  = isset($_GET['v']) ? (int)$_GET['v'] : null;
}
if (!$restaurantId) {
    http_response_code(400);
    exit(json_encode(['error' => 'Restaurant ID requerido']));
}

/* ── Cargar la versión ------------------------------------------------ */
require_once __DIR__.'/../../config/menu-service.php';
$svc  = new MenuService();
$data = $svc->getRestaurantPublicData($restaurantId, false);

if (!$data || !isset($data['menu_version'])) {
    http_response_code(404);
    exit(json_encode(['error' => 'No encontrado']));
}
$currVersion = (int)$data['menu_version'];

/* ── Cabeceras de caché (después de conocer la versión) --------------- */
header('Cache-Control: public, max-age=31536000, immutable');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', $data['updated_at'] ?? time()).' GMT'); // usa el timestamp real si lo tienes

/* ── 304 si coincide la versión en la URL ----------------------------- */
if ($pathVersion !== null && $pathVersion === $currVersion) {
    http_response_code(304);
    header('Content-Length: 0');
    exit;
}

/* ── Responder JSON --------------------------------------------------- */
echo json_encode(['version' => $currVersion]);