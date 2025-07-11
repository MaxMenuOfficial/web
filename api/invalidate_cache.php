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

// 2️⃣ Validación de seguridad
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN') ?: '';
if (!hash_equals($expectedToken, $token) || $restaurantId === '') {
    error_log("❌ Invalid request — Token mismatch or missing restaurantId.");
    http_response_code(403);
    exit('Unauthorized');
}

// 3️⃣ Obtener datos desde Spanner (verificación indirecta)
try {
    $svc  = new MenuService();
    $data = $svc->getRestaurantPublicData($restaurantId, true); // Forzar fresh load

    if (!$data || !isset($data['menu_version'])) {
        throw new RuntimeException("No data or invalid menu_version for restaurantId={$restaurantId}");
    }

    error_log("📦 Datos válidos obtenidos desde Spanner — menu_version=" . $data['menu_version']);
} catch (Throwable $e) {
    error_log("❌ Spanner error: " . $e->getMessage());
    http_response_code(500);
    exit('Spanner Query Failed');
}

// 4️⃣ Purgar Cloudflare por prefijos
try {
    purgeCloudflareCacheForRestaurant($restaurantId);
    error_log("✅ Cloudflare purgado exitosamente — restaurantId={$restaurantId}");
} catch (Throwable $e) {
    error_log("❌ Error al purgar Cloudflare: " . $e->getMessage());
    http_response_code(500);
    exit('Cloudflare Purge Failed');
}

// 5️⃣ Final
http_response_code(200);
echo "✅ Cache purged for restaurantId={$restaurantId}";