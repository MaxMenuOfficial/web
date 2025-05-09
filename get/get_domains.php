<?php
// get_domains

// 1) Cargar el servicio que inicializa $domains y $restaurantId
require_once __DIR__ . '/../config/menu-service.php';
require_once __DIR__ . '/get_restaurant_id.php';

// 2) Asegurarnos de que la variable global $domains exista
global $domains;
if (!isset($domains) || !is_array($domains)) {
    $domains = [];
}

// 3) Verificar que tengamos un restaurante
if (!isset($restaurantId) || empty($restaurantId)) {
   
    exit;
}

// 4) Filtrar sólo los dominios de este restaurante
$filteredDomains = array_filter($domains, function ($d) use ($restaurantId) {
    return isset($d['restaurant_id']) && $d['restaurant_id'] === $restaurantId;
});
$filteredDomains = array_values($filteredDomains);

// 5) Devolver la respuesta (como JSON y también un print_r para debug)
header('Content-Type: application/json; charset=utf-8');
echo json_encode($filteredDomains, JSON_PRETTY_PRINT);
