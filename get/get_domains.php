<?php

// ---------------------------------------------------------
// 🔗 Inicialización del entorno simbiótico de seguridad
// ---------------------------------------------------------
require_once __DIR__ . '/../config/menu-service.php';
require_once __DIR__ . '/get_restaurant_id.php';

global $domains, $restaurantId;

// ---------------------------------------------------------
// 🔐 Validaciones estructurales mínimas
// ---------------------------------------------------------
if (empty($domains) || empty($restaurantId)) {
    http_response_code(400);
    exit('Missing restaurant context.');
}

// ---------------------------------------------------------
// 🌍 Captura y normalización del origen
// ---------------------------------------------------------
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$originHost = parse_url($origin, PHP_URL_HOST);
$normalizedOriginHost = preg_replace('/^www\./', '', $originHost);

// Si no hay ORIGIN, bloquear (solo se permite desde navegador con Origin)
if (!$originHost) {
    http_response_code(403);
    exit('Missing origin header.');
}

// ---------------------------------------------------------
// 🧼 Extraer dominios registrados del restaurante actual
// ---------------------------------------------------------
$restaurantDomains = array_filter($domains, function ($d) use ($restaurantId) {
    return isset($d['restaurant_id'], $d['domain']) &&
           $d['restaurant_id'] === $restaurantId &&
           !empty($d['domain']);
});

$allowedHosts = array_map(function ($d) {
    return preg_replace('/^www\./', '', parse_url($d['domain'], PHP_URL_HOST));
}, $restaurantDomains);

// ---------------------------------------------------------
// ⚖️ Verificación exacta o subdominio autorizado
// ---------------------------------------------------------
$authorized = false;

foreach ($allowedHosts as $allowedHost) {
    if (
        $normalizedOriginHost === $allowedHost ||
        str_ends_with($normalizedOriginHost, '.' . $allowedHost)
    ) {
        $authorized = true;
        break;
    }
}

if (!$authorized) {
    http_response_code(403);
    exit("Access denied: origin '$normalizedOriginHost' not authorized.");
}

// ---------------------------------------------------------
// ✅ Configuración de CORS permitida
// ---------------------------------------------------------
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Responder a preflight directamente
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}