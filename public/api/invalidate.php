<?php
// public/api/invalidate.php
// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Validar token
$token    = $_POST['token']          ?? '';
$expected = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN') ?: '';
if (!hash_equals($expected, $token)) {
    http_response_code(403);
    exit('Access Denied');
}

// Validar restaurant_id
$rid = $_POST['restaurant_id'] ?? '';
if (empty($rid)) {
    http_response_code(400);
    exit('restaurant_id required');
}

// Pasar todo a la implementación interna
// Reiniciar POST para que el siguiente script la lea
$_POST['restaurant_id'] = $rid;
$_POST['token']         = $token;

// Ejecutar script protegido
require __DIR__ . '/../../api/invalidate_cache.php';

// Al llegar aquí, la implementación interna ya habrá enviado respuesta o código
// Opcionalmente puedes imprimir un mensaje final:
// exit('Done');