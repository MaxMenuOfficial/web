<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "📍 invalidate_cache.php START\n";

require_once __DIR__ . '/../../config/menu-service.php';
require_once __DIR__ . '/../../utils/cloudflare-utils.php';

header('Content-Type: application/json');

echo "✅ Dependencias cargadas correctamente\n";

// ✅ Solo se permite POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// ✅ Verificar cabecera personalizada
$internalHeader = $_SERVER['HTTP_X_INTERNAL_REQUEST'] ?? null;
if ($internalHeader !== 'MaxMenuManage') {
    http_response_code(403);
    echo json_encode(['error' => 'Origen no autorizado']);
    exit;
}

// ✅ Verificar token y restaurant_id
$restaurantId = $_POST['restaurant_id'] ?? null;
$token = $_POST['token'] ?? null;
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN');

echo "🔐 Token recibido: $token\n";
echo "🔐 Token esperado: $expectedToken\n";
echo "🍽️ Restaurante: $restaurantId\n";

if (!$restaurantId || $token !== $expectedToken) {
    http_response_code(403);
    echo json_encode(['error' => 'Token inválido']);
    exit;
}

// 🧠 Invalidar caché en memoria
MenuService::clearMenuCache($restaurantId);
echo "🧠 Caché en memoria limpiada.\n";

// 🚀 Invalidar caché en Cloudflare
purgeCloudflareCacheForRestaurant($restaurantId);
echo "🚀 Caché Cloudflare invalidada\n";

// ✅ Éxito
echo json_encode([
    'status' => 'ok',
    'message' => "Caché invalidada para restaurante $restaurantId"
]);
exit;