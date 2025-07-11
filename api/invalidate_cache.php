<?php
// File: api/invalidate_cache.php
// 1️⃣ Configuración de logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// 2️⃣ Captura de parámetros POST
$restaurantId = $_POST['restaurant_id'] ?? '';
$token = $_POST['token'] ?? '';

error_log("🔔 invalidate_cache.php called — restaurantId={$restaurantId}");

// 3️⃣ Validación de seguridad
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN') ?: '';
if (!hash_equals($expectedToken, $token) || $restaurantId === '') {
    error_log("❌ Invalid call — restaurantId={$restaurantId}");
    http_response_code(403);
    exit('Unauthorized');
}

// 4️⃣ Carga de dependencias
require __DIR__ . '/../config/menu-service.php';
require __DIR__ . '/../utils/cloudflare-utils.php';

// 5️⃣ Limpieza de caché in-memory de MenuService
try {
    MenuService::clearMenuCache($restaurantId);
    error_log("✅ In-memory cache cleared — restaurantId={$restaurantId}");
} catch (Throwable $e) {
    error_log("❌ clearMenuCache failed: " . $e->getMessage());
    http_response_code(500);
    exit('Memory Cache Error');
}

// 6️⃣ Obtener la versión actual para purga
try {
    $svc  = new MenuService();
    // force = true para recarga directa desde Spanner sin usar cache local
    $data = $svc->getRestaurantPublicData($restaurantId, true);

    if (!$data || !isset($data['menu_version'])) {
        throw new RuntimeException("menu_version not found for restaurantId={$restaurantId}");
    }

    $version = (int)$data['menu_version'];
    error_log("📦 menu_version={$version} obtained for restaurantId={$restaurantId}");
} catch (Throwable $e) {
    error_log("❌ Failed to get menu_version: " . $e->getMessage());
    http_response_code(500);
    exit('Version Lookup Error');
}

// 7️⃣ Purga en Cloudflare usando la versión exacta
try {
    purgeCloudflareCacheForRestaurant($restaurantId, $version);
    error_log("✅ Cloudflare purged — restaurantId={$restaurantId} — v={$version}");
} catch (Throwable $e) {
    error_log("❌ purgeCloudflare failed: " . $e->getMessage());
    http_response_code(500);
    exit('Cloudflare Purge Error');
}

// 8️⃣ Respuesta de éxito
http_response_code(200);
echo 'OK';