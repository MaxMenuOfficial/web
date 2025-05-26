<?php
// File: menu.maxmenu.com/api/invalidate_cache.php

ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/invalidate_cache.log'); // ✔️ Cloud Run compatible

require_once __DIR__ . '/../../config/menu-service.php';
require_once __DIR__ . '/../../utils/cloudflare-utils.php';

header('Content-Type: application/json');

// ✅ Solo POST permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    error_log("🚫 [405] Método no permitido: " . $_SERVER['REQUEST_METHOD']);
    exit;
}

// ✅ Parámetros esperados
$restaurantId = $_POST['restaurant_id'] ?? null;
$token = $_POST['token'] ?? null;
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN');

// ❌ Validación de token y parámetro
if (!$restaurantId || !$token || $token !== $expectedToken) {
    http_response_code(403);
    echo json_encode(['error' => 'Token inválido o faltan parámetros']);
    error_log("🚫 [403] Token inválido o parámetros faltantes. ID: $restaurantId, Token: $token");
    exit;
}

try {
    // 🧠 Invalidar caché en memoria
    MenuService::clearMenuCache($restaurantId);

    // ☁️ Invalidar caché en Cloudflare
    purgeCloudflareCacheForRestaurant($restaurantId);

    // ✅ Éxito
    echo json_encode([
        'status' => 'ok',
        'message' => "Caché invalidada para restaurante $restaurantId"
    ]);
    error_log("✅ Caché invalidada correctamente para $restaurantId");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno al invalidar caché']);
    error_log("🔥 [500] Error al invalidar caché para $restaurantId: " . $e->getMessage());
    exit;
}