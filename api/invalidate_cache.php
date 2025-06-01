<?php
// api/invalidate_cache.php

// Activa logging de errores (no muestra en pantalla)
ini_set('display_errors', 0);
ini_set('log_errors',     1);
error_reporting(E_ALL);

// Captura parámetros
$rid   = $_POST['restaurant_id'] ?? '';
$token = $_POST['token']          ?? '';

// Log de entrada
error_log("🔔 invalidate_cache.php called — rid={$rid}");

// Validación redundante para defensa en profundidad
$expected = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN') ?: '';
if (!hash_equals($expected, $token) || $rid === '') {
    error_log("❌ Invalid call — rid={$rid}");
    http_response_code(403);
    exit;
}


// Carga de servicios
require __DIR__ . '/../config/menu-service.php';
require __DIR__ . '/../utils/cloudflare-utils.php';

// Limpiar caché en memoria
try {
    MenuService::clearMenuCache($rid);
    error_log("✅ Mem cache cleared — rid={$rid}");
} catch (Throwable $e) {
    error_log("❌ clearMenuCache failed: " . $e->getMessage());
    http_response_code(500);
    exit('Memory Cache Error');
}

// Purga Cloudflare
try {
    purgeCloudflareCacheForRestaurant($rid);
    error_log("✅ Cloudflare purged — rid={$rid}");
} catch (Throwable $e) {
    error_log("❌ purgeCloudflare failed: " . $e->getMessage());
    http_response_code(500);
    exit('Cloudflare Purge Error');
}


// Éxito
http_response_code(200);
echo 'OK';