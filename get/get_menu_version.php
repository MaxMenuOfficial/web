<?php
// 📁 backend/php/get/get_menu_version.php

// Incluir el script que obtiene y valida el restaurantId vía GET (sin usar la sesión)
require_once __DIR__ . '/get_restaurant_id.php';

// Incluir el servicio que carga toda la información del restaurante y sus relaciones
require_once __DIR__ . '/../config/menu-service.php';

// Asegurarnos de que $restaurantId esté definido correctamente
if (!isset($restaurantId) || empty($restaurantId)) {
    error_log("⚠️ No se proporcionó un restaurantId válido.");
    http_response_code(400);
    echo json_encode(['error' => 'restaurantId requerido']);
    exit;
}

// Acceder a la variable global $restaurants cargada desde menu-service.php
global $restaurants;

if (!isset($restaurants) || !is_array($restaurants)) {
    error_log("❌ La variable global \$restaurants no está disponible o no es un array.");
    http_response_code(500);
    echo json_encode(['error' => 'Error interno al cargar restaurantes']);
    exit;
}

// Buscar el restaurante correspondiente por ID
$currentRestaurant = array_filter($restaurants, function ($r) use ($restaurantId) {
    return isset($r['restaurant_id']) && $r['restaurant_id'] === $restaurantId;
});

$currentRestaurant = array_values($currentRestaurant)[0] ?? null;

if (!$currentRestaurant || !isset($currentRestaurant['menu_version'])) {
    error_log("❌ No se encontró el restaurante o el campo menu_version para $restaurantId.");
    http_response_code(404);
    echo json_encode(['error' => 'menu_version no disponible']);
    exit;
}

// Obtener la versión del menú y prepararla para su uso
$menu_version = (int)$currentRestaurant['menu_version'];

error_log("📌 menu_version encontrado para restaurante $restaurantId: $menu_version");

// (Opcional) Descomenta para devolver como JSON
// header('Content-Type: application/json');
// echo json_encode(['menu_version' => $menu_version]);
// exit;