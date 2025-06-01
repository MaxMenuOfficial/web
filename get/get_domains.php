<?php
require_once __DIR__ . '/get_restaurant_id.php';          // define global $restaurantId
require_once __DIR__ . '/../config/menu-service.php';     // inicializa global $domains

global $domains, $restaurantId;

if (empty($domains) || empty($restaurantId)) {
    http_response_code(400);
    exit("Access denied: this domain is not authorized to view this menu.");
}

// --------------------
// 🧠 Captura del origin
// --------------------
$origin      = $_SERVER['HTTP_ORIGIN'] ?? '';
$originHost  = $origin ? parse_url($origin, PHP_URL_HOST) : '';
$normalized  = $originHost ? preg_replace('/^www\./', '', strtolower($originHost)) : '';

// ----------------------------
// 🔓 Detectar acceso legítimo
// ----------------------------
$isCloudflare = !isset($_SERVER['HTTP_ORIGIN']) && ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? false);

// ------------------------------
// 🧼 Lista de dominios registrados
// ------------------------------
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

// ------------------------------
// ⚖️ Verificación del dominio
// ------------------------------
$authorized = false;

foreach ($allowedHosts as $host) {
    if ($normalized === $host || str_ends_with($normalized, '.' . $host)) {
        $authorized = true;
        break;
    }
}

// ------------------------------
// 🧩 Responder o bloquear
// ------------------------------
if (!$isCloudflare && !$authorized) {
    http_response_code(403);
    exit("Forbidden: origin '{$normalized}' not authorized.");
}

// ------------------------------
// ✅ CORS para navegadores
// ------------------------------
if (!$isCloudflare) {
    header("Access-Control-Allow-Origin: {$origin}");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
}

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}