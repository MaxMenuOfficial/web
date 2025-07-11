<?php
// 📁 api/invalidate_cache.php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../config/menu-service.php';
require __DIR__ . '/../utils/cloudflare-utils.php';

// 1️⃣ Captura de parámetros
$restaurantId = trim($_POST['restaurant_id'] ?? '');
$token        = trim($_POST['token'] ?? '');

error_log("🔔 invalidate_cache.php called — restaurantId={$restaurantId}");

// 2️⃣ Validación básica
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN') ?: '';
if (!hash_equals($expectedToken, $token) || $restaurantId === '') {
    error_log("❌ Invalid call — restaurantId={$restaurantId}");
    http_response_code(403);
    exit('Unauthorized');
}

// 3️⃣ Obtener versión anterior
try {
    $svc         = new MenuService();
    $oldData     = $svc->getRestaurantPublicData($restaurantId, false); // No forzar refresh
    $oldVersion  = (int)($oldData['menu_version'] ?? 0);

    if ($oldVersion <= 0) {
        throw new RuntimeException("Invalid menu_version before update for restaurantId={$restaurantId}");
    }

    error_log("📦 Version anterior: {$oldVersion} — restaurantId={$restaurantId}");
} catch (Throwable $e) {
    error_log("❌ Error obteniendo versión anterior: " . $e->getMessage());
    http_response_code(500);
    exit('Failed to get previous version');
}

// 4️⃣ Purgar la versión anterior del cache de Cloudflare
try {
    purgeCloudflareCacheForRestaurant($restaurantId, $oldVersion);
    error_log("✅ Cloudflare purged old version — restaurantId={$restaurantId} — v={$oldVersion}");
} catch (Throwable $e) {
    error_log("❌ purgeCloudflare (old version) failed: " . $e->getMessage());
    http_response_code(500);
    exit('Cloudflare Purge Error - Old Version');
}

// 5️⃣ Generar nueva versión (timestamp)
$newVersion = time();

// 6️⃣ Actualizar Spanner con la nueva versión
try {
    $svc->updateMenuVersion($restaurantId, $newVersion);
    error_log("✅ Nueva versión {$newVersion} actualizada en Spanner — restaurantId={$restaurantId}");
} catch (Throwable $e) {
    error_log("❌ Error actualizando nueva versión en Spanner: " . $e->getMessage());
    http_response_code(500);
    exit('Spanner Update Error');
}

// 7️⃣ Purgar la nueva versión (por si Cloudflare cacheó por anticipación)
try {
    purgeCloudflareCacheForRestaurant($restaurantId, $newVersion);
    error_log("✅ Cloudflare purged new version — restaurantId={$restaurantId} — v={$newVersion}");
} catch (Throwable $e) {
    error_log("❌ purgeCloudflare (new version) failed: " . $e->getMessage());
    // Nota: no detenemos el flujo, ya se purgó la anterior
}

// 8️⃣ Final
http_response_code(200);
echo 'OK';