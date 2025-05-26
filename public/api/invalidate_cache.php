<?php
// File: public/api/invalidate_cache.php

/**********************************************************************
 * DIAGNÓSTICO INTENSIVO
 *********************************************************************/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Helper: escribe en stderr + incluye uso de memoria.
 */
function debug($msg, $ctx = [])
{
    $mem = round(memory_get_usage() / 1024 / 1024, 1) . ' MiB';
    $ctxStr = $ctx ? ' | ' . json_encode($ctx, JSON_UNESCAPED_UNICODE) : '';
    error_log('[invalidate_cache] ' . $msg . " | mem={$mem}{$ctxStr}");
}

header('Content-Type: application/json');
debug('🔔 Nuevo request', ['method' => $_SERVER['REQUEST_METHOD'] ?? '?']);

/**********************************************************************
 * 1️⃣  Validar método
 *********************************************************************/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debug('⛔ Método no permitido');
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

/**********************************************************************
 * 2️⃣  Leer parámetros
 *********************************************************************/
$restaurantId = $_POST['restaurant_id'] ?? null;
$token        = $_POST['token']         ?? null;
$header       = $_SERVER['HTTP_X_INTERNAL_REQUEST'] ?? '';

debug('📥 Params recibidos', compact('restaurantId', 'token', 'header'));

/**********************************************************************
 * 3️⃣  Verificar cabecera interna (opcional)
 *********************************************************************/
if ($header !== 'MaxMenuManage') {
    debug('🚫 Cabecera X-Internal-Request inválida');
    http_response_code(403);
    echo json_encode(['error' => 'Cabecera incorrecta']);
    exit;
}

/**********************************************************************
 * 4️⃣  Verificar token
 *********************************************************************/
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN');
if (!$restaurantId || !$token || $token !== $expectedToken) {
    debug('🚫 Token inválido o faltan datos');
    http_response_code(403);
    echo json_encode(['error' => 'Token inválido o datos incompletos']);
    exit;
}

/**********************************************************************
 * 5️⃣  Purga de Cloudflare
 *********************************************************************/
try {
    require_once __DIR__ . '/../../utils/cloudflare-utils.php';
} catch (Throwable $e) {
    debug('💥 No se pudo cargar cloudflare-utils', ['error' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['error' => 'Dependencia no encontrada']);
    exit;
}

try {
    debug('🚀 Llamando a purgeCloudflareCacheForRestaurant');
    purgeCloudflareCacheForRestaurant($restaurantId);
    debug('✅ Purga completada');

    echo json_encode([
        'success' => true,
        'message' => "Cache purgada para $restaurantId"
    ]);
} catch (Throwable $e) {
    debug('💥 Excepción al purgar', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    http_response_code(500);
    echo json_encode(['error' => 'Fallo al purgar caché', 'detail' => $e->getMessage()]);
}