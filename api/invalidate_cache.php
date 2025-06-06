<?php
// api/invalidate_cache.php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// 1️⃣ Captura de parámetros
$rid   = $_POST['restaurant_id'] ?? '';
$token = $_POST['token'] ?? '';
error_log("🔔 invalidate_cache.php called — rid={$rid}");

// 2️⃣ Seguridad
$expected = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN') ?: '';
if (!hash_equals($expected, $token) || empty($rid)) {
    error_log("❌ Invalid call — rid={$rid}");
    http_response_code(403);
    exit;
}

// 3️⃣ Cargar servicios
require __DIR__ . '/../config/menu-service.php';
require __DIR__ . '/../utils/cloudflare-utils.php';

// 4️⃣ Borrar caché en memoria
try {
    MenuService::clearMenuCache($rid);
    error_log("✅ Mem cache cleared — rid={$rid}");
} catch (Throwable $e) {
    error_log("❌ clearMenuCache failed: " . $e->getMessage());
    http_response_code(500);
    exit('Memory Cache Error');
}

// 5️⃣ Obtener versión actual del menú
$version = null;

try {
    $menuData = (new MenuService())->getRestaurantPublicData($rid, true);
    if (isset($menuData['menu_version'])) {
        $version = (int)$menuData['menu_version'];
        error_log("🔁 menu_version=$version para purge");
    } else {
        throw new RuntimeException('menu_version no disponible');
    }
} catch (Throwable $e) {
    error_log("❌ Error obteniendo menu_version: " . $e->getMessage());
    http_response_code(500);
    exit('Version Lookup Error');
}

// 6️⃣ Purgar Cloudflare
try {
    purgeCloudflareCacheForRestaurant($rid, $version);
    error_log("✅ Cloudflare purged — rid={$rid} — v={$version}");
} catch (Throwable $e) {
    error_log("❌ purgeCloudflare failed: " . $e->getMessage());
    http_response_code(500);
    exit('Cloudflare Purge Error');
}

// 7️⃣ Éxito
http_response_code(200);
echo 'OK';