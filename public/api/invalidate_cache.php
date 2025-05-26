<?php
// File: public/api/invalidate_cache.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// ✅ Verificación de método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// ✅ Obtener variables
$restaurantId = $_POST['restaurant_id'] ?? null;
$token        = $_POST['token'] ?? null;

// ✅ Verificar token interno
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN');
if (!$restaurantId || !$token || $token !== $expectedToken) {
    http_response_code(403);
    echo json_encode(['error' => 'Token inválido o datos incompletos']);
    exit;
}

// ✅ Ejecutar purga de Cloudflare
require_once __DIR__ . '/../../utils/cloudflare-utils.php';

purgeCloudflareCacheForRestaurant($restaurantId);

echo json_encode(['success' => true, 'message' => "Cache purgada para $restaurantId"]);
exit;