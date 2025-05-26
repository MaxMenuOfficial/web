<?php
// File: public/api/invalidate_cache.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 📡 Devolver JSON siempre
header('Content-Type: application/json');

// ✅ Método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// ✅ Variables
$restaurantId = $_POST['restaurant_id'] ?? null;
$token        = $_POST['token'] ?? null;
$expectedToken = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN');

// 🧪 Depuración de variables
if (!$restaurantId || !$token || $token !== $expectedToken) {
    http_response_code(403);
    echo json_encode([
        'error' => 'Token inválido o datos incompletos',
        'token_enviado' => $token,
        'token_esperado' => $expectedToken,
        'restaurant_id' => $restaurantId
    ]);
    exit;
}

// 🧠 Captura de errores fatales
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        http_response_code(500);
        echo json_encode([
            'fatal' => true,
            'type' => $error['type'],
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ]);
    }
});

try {
    require_once __DIR__ . '/../../utils/cloudflare-utils.php';

    purgeCloudflareCacheForRestaurant($restaurantId);

    echo json_encode([
        'success' => true,
        'message' => "✅ Cache purgada para $restaurantId"
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Excepción capturada',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}