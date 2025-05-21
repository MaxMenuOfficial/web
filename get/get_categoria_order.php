<?php
// 📁 backend/php/get/get_category_order.php

// Incluir el servicio de menú que carga las variables globales (entre ellas $categories)
require_once __DIR__ . '/../config/menu-service.php';
// Incluir el script que obtiene y valida el restaurantId vía GET
require_once __DIR__ . '/get_restaurant_id.php';

// Asegurar que $categories esté definido
global $categories;

if (!isset($categories) || !is_array($categories)) {
    $categories = [];
}

// Filtrar categorías del restaurante actual
$filteredCategories = array_filter($categories, function ($cat) use ($restaurantId) {
    return isset($cat['restaurant_id']) && $cat['restaurant_id'] === $restaurantId;
});

// Ordenar por sort_order (anteriormente 'orden')
usort($filteredCategories, function ($a, $b) {
    $ordenA = $a['sort_order'] ?? PHP_INT_MAX;
    $ordenB = $b['sort_order'] ?? PHP_INT_MAX;
    return $ordenA <=> $ordenB;
});


$filteredCategories = array_values($filteredCategories);
$estructuraMenu = $filteredCategories;

if (empty($estructuraMenu)) {
    error_log("⚠️ No se encontraron categorías para restaurantId: $restaurantId");
}

// (Opcional) Devolver en JSON para frontend o pruebas
// header('Content-Type: application/json');
// echo json_encode($filteredCategories, JSON_PRETTY_PRINT);
// exit;