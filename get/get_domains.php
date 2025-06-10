<?php
// ---------------------------------------------------------
// 🔗 Inicialización de entorno y obtención de restaurantId
// ---------------------------------------------------------
require_once __DIR__ . '/get_restaurant_id.php';          // define global $restaurantId
require_once __DIR__ . '/../config/menu-service.php';     // inicializa global $domains

global $domains, $restaurantId;

// ---------------------------------------------------------
// 🔐 Validaciones mínimas
// ---------------------------------------------------------
if (empty($domains) || empty($restaurantId)) {
    http_response_code(400);
    exit("Access denied: this domain is not authorized to view this menu. Please register it in your MaxMenu dashboard.");
}

// ---------------------------------------------------------
// 🌍 Captura y normalización del Origin
// ---------------------------------------------------------
$origin      = $_SERVER['HTTP_ORIGIN'] ?? '';
$originHost  = $origin ? parse_url($origin, PHP_URL_HOST) : '';
$normalized  = $originHost ? preg_replace('/^www\./', '', strtolower($originHost)) : '';

// Si no viene Origin o es localhost → denegar
if (!$originHost || $normalized === 'localhost') {
    http_response_code(403);
    exit('Forbidden: invalid origin.');
}

// ---------------------------------------------------------
// 🧼 Preparar lista de hosts autorizados
// ---------------------------------------------------------
$restaurantDomains = array_filter($domains, fn($d) =>
    isset($d['restaurant_id'], $d['domain']) &&
    $d['restaurant_id'] === $restaurantId &&
    !empty($d['domain'])
);

$allowedHosts = array_map(function($raw){
    $u = trim($raw['domain']);
    if (!preg_match('#^https?://#i', $u)) {
        $u = 'https://' . $u;
    }
    $h = parse_url($u, PHP_URL_HOST);
    return $h ? preg_replace('/^www\./','', strtolower($h)) : '';
}, $restaurantDomains);

// ---------------------------------------------------------
// ⚖️ Verificar que el Origin esté en la lista o sea subdominio
// ---------------------------------------------------------
$authorized = false;
foreach ($allowedHosts as $host) {
    if ($normalized === $host || str_ends_with($normalized, '.' . $host)) {
        $authorized = true;
        break;
    }
}

if (!$authorized) {
    http_response_code(403);
    exit("Forbidden: origin '{$normalized}' not authorized.");
}

// ---------------------------------------------------------
// ✅ Configurar CORS para dominios autorizados
// ---------------------------------------------------------
header("Access-Control-Allow-Origin: {$origin}");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Responder al preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Aquí sigue la lógica de tu widget…