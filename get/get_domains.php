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
    exit("Access denied: this domain is not authorized to view this menu. Please register it in your MaxMenu dashboard.");
}

// ---------------------------------------------------------
// 🌍 Captura y normalización del origen
// ---------------------------------------------------------
$origin = $_SERVER['HTTP_ORIGIN'] ?? null;
$originHost = $origin ? parse_url($origin, PHP_URL_HOST) : null;
$normalizedOriginHost = $originHost ? preg_replace('/^www\./', '', $originHost) : null;

// En producción, bloquear si no hay ORIGIN
if (!$originHost) {
    if ($_ENV['APP_ENV'] !== 'development') {
        http_response_code(403);
        exit('Missing origin header.');
    } else {
        // En entorno local, permitir origen falso para test
        $origin = 'http://localhost';
        $originHost = 'localhost';
        $normalizedOriginHost = 'localhost';
    }
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
    $raw = trim($d['domain']);
    // Si no tiene protocolo, añadimos temporalmente https:// para parse_url
    if (!preg_match('#^https?://#i', $raw)) {
        $raw = 'https://' . $raw;
    }

    $host = parse_url($raw, PHP_URL_HOST);
    return $host ? preg_replace('/^www\./', '', strtolower($host)) : '';
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