<?php
// File: api/invalidate_cache.php

// 1️⃣ Logging & errores
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// 2️⃣ Captura segura
$restaurantId = trim($_POST['restaurant_id'] ?? '');
$token        = trim($_POST['token'] ?? '');

error_log("🔔 invalidate_cache.php called — restaurantId={$restaurantId}");

// 3️⃣ Validación
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN') ?: '';
if (!hash_equals($expectedToken, $token) || $restaurantId === '') {
    error_log("❌ Invalid call — restaurantId={$restaurantId}");
    http_response_code(403);
    exit('Unauthorized');
}

// 4️⃣ Carga dependencias
require __DIR__ . '/../config/menu-service.php';
require __DIR__ . '/../utils/cloudflare-utils.php';

// 5️⃣ Limpieza de caché in-memory
try {
    MenuService::clearMenuCache($restaurantId);
    error_log("✅ In-memory cache cleared — restaurantId={$restaurantId}");
} catch (Throwable $e) {
    error_log("❌ clearMenuCache failed: " . $e->getMessage());
    http_response_code(500);
    exit('Memory Cache Error');
}

// 6️⃣ Obtener versión del menú
try {
    $svc     = new MenuService();
    $data    = $svc->getRestaurantPublicData($restaurantId, true);
    $version = (int)($data['menu_version'] ?? 0);

    if ($version <= 0) {
        throw new RuntimeException("Invalid or missing menu_version for restaurantId={$restaurantId}");
    }

    error_log("📦 menu_version={$version} obtained for restaurantId={$restaurantId}");
} catch (Throwable $e) {
    error_log("❌ Failed to get menu_version: " . $e->getMessage());
    http_response_code(500);
    exit('Version Lookup Error');
}

// 7️⃣ Purgar Cloudflare
try {
    purgeCloudflareCacheForRestaurant($restaurantId, $version);
    error_log("✅ Cloudflare purged — restaurantId={$restaurantId} — v={$version}");
} catch (Throwable $e) {
    error_log("❌ purgeCloudflare failed: " . $e->getMessage());
    http_response_code(500);
    exit('Cloudflare Purge Error');
}

// 8️⃣ Respuesta
http_response_code(200);
echo 'OK';