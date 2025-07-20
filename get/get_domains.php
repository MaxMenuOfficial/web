<?php
// ---------------------------------------------------------
// 🔗 Inicialización de entorno y obtención de restaurantId
// ---------------------------------------------------------
require_once __DIR__ . '/../config/menu-service.php';     // inicializa global $domains

// ✅ Solo continuar si $domains está disponible
if (!isset($domains) || !is_array($domains) || empty($domains)) {
    http_response_code(403);
    echo json_encode(['error' => 'Restaurant domains not loaded']);
    exit;
}

// 🧠 Validar dominio de origen contra los dominios autorizados en la tabla restaurant_domains
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($origin) {
    // Normaliza el dominio de origen (extrae solo host y elimina www.)
    $originHost = parse_url($origin, PHP_URL_HOST);
    $originHost = preg_replace('/^www\./', '', $originHost);

    // Extrae todos los dominios registrados para ese restaurante y normaliza
    $allowedDomains = array_map(function ($d) {
        return preg_replace('/^www\./', '', $d['domain'] ?? '');
    }, $domains);

    // Validación estricta
    if (!in_array($originHost, $allowedDomains, true)) {
        http_response_code(403); // ❌ Acceso denegado
        echo json_encode(['error' => 'Origin not authorized']);
        exit;
    }

    // 🟢 Dominio autorizado → permitir CORS
    header("Access-Control-Allow-Origin: $origin");
}