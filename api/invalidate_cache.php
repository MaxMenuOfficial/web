<?php
// File: api/invalidate_cache.php

// Logging de errores (silencioso en pantalla, activo en logs)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Captura de parámetros
$rid   = $_POST['restaurant_id'] ?? '';
$token = $_POST['token']         ?? '';

// Log de entrada
error_log("🔔 invalidate_cache.php called — rid={$rid}");

// Validación redundante de seguridad
$expected = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN') ?: '';
if (!hash_equals($expected, $token) || $rid === '') {
    error_log("❌ Invalid call — rid={$rid}");
    http_response_code(403);
    exit('Invalid Token or Missing ID');
}

// Cargar servicios necesarios
require __DIR__ . '/../config/menu-service.php';
require __DIR__ . '/../utils/cloudflare-utils.php';

try {
    // 🔁 Limpiar caché en memoria
    MenuService::clearMenuCache($rid);
    error_log("✅ Mem cache cleared — rid={$rid}");

    // 🔎 Obtener versión actual del menú (fresh)
    $svc = new MenuService();
    $data = $svc->getRestaurantPublicData($rid, true);

    if (empty($data['menu_version'])) {
        throw new Exception("menu_version missing for $rid");
    }

    $version = (int) $data['menu_version'];
    error_log("📦 menu_version = $version — rid={$rid}");

    // ☁️ Purgar caché de Cloudflare con la versión exacta
    purgeCloudflareCacheForRestaurant($rid, $version);
    error_log("✅ Cloudflare purged — rid={$rid} — v={$version}");

    // ✅ Éxito
    http_response_code(200);
    echo 'OK';

} catch (Throwable $e) {
    error_log("❌ Fatal error: " . $e->getMessage());
    http_response_code(500);
    exit('Internal Error');
}