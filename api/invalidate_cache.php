<?php
// api/invalidate_cache.php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// 1️⃣ Captura parámetros
$restaurantId = $_POST['restaurant_id'] ?? '';
$token        = $_POST['token'] ?? '';
error_log("🔔 invalidate_cache.php llamado — rid={$restaurantId}");

// 2️⃣ Seguridad interna (token protegido)
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN') ?: '';
if (!hash_equals($expectedToken, $token) || empty($restaurantId)) {
    error_log("❌ Llamada no autorizada — rid={$restaurantId}");
    http_response_code(403);
    exit('Unauthorized');
}

// 3️⃣ Cargar dependencias y cargar datos en memoria
require_once __DIR__ . '/../config/get_restaurant_id.php';
require_once __DIR__ . '/../config/menu-service.php';

// 4️⃣ Limpiar caché en memoria (importante antes de recalcular)
try {
    MenuService::clearMenuCache($restaurantId);
    error_log("✅ Caché en memoria limpiada — rid={$restaurantId}");
} catch (Throwable $e) {
    error_log("❌ Error limpiando caché local: " . $e->getMessage());
    http_response_code(500);
    exit('Error limpiando caché local');
}

// 5️⃣ Incluir script que define $menu_version desde memoria
require_once __DIR__ . '/../get/get_menu_version.php'; // define $menu_version

global $menu_version;

if (!isset($menu_version) || !$menu_version) {
    error_log("❌ menu_version no disponible — rid={$restaurantId}");
    http_response_code(500);
    exit('menu_version no disponible');
}

error_log("📌 menu_version usada para purga: $menu_version");

// 6️⃣ Cargar utilidades de purga y ejecutar
require_once __DIR__ . '/../utils/cloudflare-utils.php';

try {
    purgeCloudflareCacheForRestaurant($restaurantId, $menu_version);
} catch (Throwable $e) {
    error_log("❌ Error purgando Cloudflare: " . $e->getMessage());
    http_response_code(500);
    exit('Cloudflare Purge Error');
}
// 7️⃣ Respuesta OK
http_response_code(200);
echo 'OK';