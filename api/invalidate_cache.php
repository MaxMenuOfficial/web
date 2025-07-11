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

// 2️⃣ Validación de token y parámetros
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN') ?: '';
if (!hash_equals($expectedToken, $token) || $restaurantId === '') {
    error_log("❌ Unauthorized or missing restaurantId — restaurantId={$restaurantId}");
    http_response_code(403);
    exit('Unauthorized');
}

// 3️⃣ Consultar la versión ACTUAL desde Spanner
try {
    $svc        = new MenuService();
    $data       = $svc->getRestaurantPublicData($restaurantId, true); // Forzar consulta
    $newVersion = (int)($data['menu_version'] ?? 0);

    if ($newVersion <= 0) {
        throw new RuntimeException("❌ Versión inválida para purgado — restaurantId={$restaurantId}");
    }

    error_log("📦 Versión actual obtenida: v{$newVersion} — restaurantId={$restaurantId}");
} catch (Throwable $e) {
    error_log("❌ Error al obtener versión actual de Spanner: " . $e->getMessage());
    http_response_code(500);
    exit('Spanner Query Failed');
}

// 4️⃣ Calcular versión anterior: nueva - 1
$oldVersion = $newVersion - 1;

// 5️⃣ Ejecutar purga de ambas versiones
try {
    purgeCloudflareCacheForRestaurant($restaurantId, $newVersion);
    purgeCloudflareCacheForRestaurant($restaurantId, $oldVersion);
    error_log("✅ Cloudflare purgado para restaurantId={$restaurantId} — vOld={$oldVersion} | vNew={$newVersion}");
} catch (Throwable $e) {
    error_log("❌ Error al purgar Cloudflare: " . $e->getMessage());
    http_response_code(500);
    exit('Cloudflare Purge Failed');
}

// 6️⃣ Fin exitoso
http_response_code(200);
echo "✅ Cache purged for restaurantId={$restaurantId}, versions={$oldVersion},{$newVersion}";