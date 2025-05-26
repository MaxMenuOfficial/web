<?php
// File: menu.maxmenu.com/api/invalidate_cache.php

require_once __DIR__ . '/../config/menu-service.php';
require_once __DIR__ . '/../utils/cloudflare-utils.php';

header('Content-Type: application/json');

// ✅ Solo se permite POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// ✅ Verificar cabecera personalizada
$internalHeader = $_SERVER['HTTP_X_INTERNAL_REQUEST'] ?? null;
if ($internalHeader !== 'MaxMenuManage') {
    http_response_code(403);
    echo json_encode(['error' => 'Origen no autorizado']);
    exit;
}

// ✅ Verificar token y restaurant_id
$restaurantId = $_POST['restaurant_id'] ?? null;
$token = $_POST['token'] ?? null;
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN');

if (!$restaurantId || $token !== $expectedToken) {
    http_response_code(403);
    echo json_encode(['error' => 'Token inválido']);
    exit;
}

// 🧠 Invalidar caché en memoria
MenuService::clearMenuCache($restaurantId);

// 🚀 Invalidar caché en Cloudflare
purgeCloudflareCacheForRestaurant($restaurantId);

// ✅ Éxito
echo json_encode([
    'status' => 'ok',
    'message' => "Caché invalidada para restaurante $restaurantId"
]);
exit;