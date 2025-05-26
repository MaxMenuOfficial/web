<?php
// public/api/invalidate_cache.php
// -----------------------------------------
// 💡  ESTE SCRIPT:
//   1. Valida método, token y cabecera interna
//   2. Purga caché Cloudflare (solo las URLs del restaurante)
//   3. Devuelve JSON
//   4. Loguea *todo* a stderr para que Cloud Run lo capture
// -----------------------------------------

declare(strict_types=1);

// ──────────────────────────────────────────
// 1.  Configuración de debugging/logs
// ──────────────────────────────────────────
ini_set('display_errors', '0');                  // No mostrar al cliente
ini_set('log_errors', '1');
ini_set('error_log', 'php://stderr');            // Cloud Run captura stderr
error_reporting(E_ALL);

// Canal propio para medir memoria/tiempos
function log_dbg(string $msg): void {
    error_log('[invalidate_cache] ' . $msg);
}

// ──────────────────────────────────────────
// 2.  Validaciones básicas
// ──────────────────────────────────────────
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

if (($_SERVER['HTTP_X_INTERNAL_REQUEST'] ?? '') !== 'MaxMenuManage') {
    http_response_code(403);
    echo json_encode(['error' => 'Cabecera interna faltante']);
    exit;
}

$restaurantId  = $_POST['restaurant_id'] ?? '';
$token         = $_POST['token']         ?? '';
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN') ?: '';

if ($restaurantId === '' || $token !== $expectedToken) {
    http_response_code(403);
    echo json_encode(['error' => 'Token inválido o datos incompletos']);
    exit;
}

// ──────────────────────────────────────────
// 3.  Purga Cloudflare
// ──────────────────────────────────────────
require_once __DIR__ . '/../../utils/cloudflare-utils.php';

try {
    log_dbg("Purga solicitada para $restaurantId");
    purgeCloudflareCacheForRestaurant($restaurantId);
    log_dbg("Purga OK para $restaurantId · memoria=".memory_get_peak_usage(true)." bytes");

    echo json_encode(['success' => true, 'restaurant_id' => $restaurantId]);
} catch (Throwable $e) {
    // Capturamos **todo**: Exception y Error (OOM, etc.)
    log_dbg('🔥 EXCEPCIÓN: '.$e->getMessage().' · Trace: '.$e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['error' => 'Fallo interno en la invalidación']);
}
