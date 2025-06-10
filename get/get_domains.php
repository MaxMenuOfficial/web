<?php
// File: get_domains.php

// 1. Inicia tu servicio y saca $domains y $restaurantId
require_once __DIR__ . '/../../config/menu-service.php';
require_once __DIR__ . '/get_restaurant_id.php';

global $domains, $restaurantId;

// 2. Cabecera JSON (para debug y, al final, la validación la hará antes de enviar datos)
header('Content-Type: application/json; charset=utf-8');

// 3. Asegúrate de que $domains e $restaurantId existen
if (empty($domains) || empty($restaurantId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Restaurante o dominios no configurados']);
    exit;
}

// 4. Filtra sólo los dominios de este restaurante
$filtered = array_filter($domains, fn($d)=>
    isset($d['restaurant_id'], $d['domain'])
    && $d['restaurant_id'] === $restaurantId
    && !empty($d['domain'])
);
$filtered = array_values($filtered);

// 5. Función de normalización
function normalizeHost(string $url): string {
    // Añadimos protocolo si falta
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    $host = parse_url($url, PHP_URL_HOST) ?: '';
    $host = strtolower(preg_replace('/^www\./', '', $host));
    return rtrim($host, '.');
}

// 6. Normaliza todos los hosts permitidos
$allowedHosts = array_map(fn($d)=> normalizeHost($d['domain']), $filtered);

// 7. Obtén y normaliza el origen de la petición
$origin       = $_SERVER['HTTP_ORIGIN']       ?? '';
$originHost   = parse_url($origin, PHP_URL_HOST) ?: '';
$normalized   = strtolower(preg_replace('/^www\./', '', $originHost));

// 8. Comprueba coincidencia exacta o subdominio
$authorized = false;
foreach ($allowedHosts as $host) {
    if ($normalized === $host || str_ends_with($normalized, '.' . $host)) {
        $authorized = true;
        break;
    }
}

if (!$authorized) {
    http_response_code(403);
    echo json_encode([
        'error'  => "Origen '{$originHost}' no autorizado",
        'detail' => [
            'allowed' => $allowedHosts,
            'got'     => $normalized
        ]
    ], JSON_PRETTY_PRINT);
    exit;
}

// 9. Si llegamos aquí, todo ok → devolvemos la lista (o quítalo si no quieres exponerla)
echo json_encode($filtered, JSON_PRETTY_PRINT);