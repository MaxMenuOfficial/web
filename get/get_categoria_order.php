<?php
// 📁 backend/php/get/get_category_order.php

// Incluir el servicio de menú que carga las variables globales (entre ellas $categories)
require_once __DIR__ . '/../config/menu-service.php';
// Incluir el script que obtiene y valida el restaurantId vía GET
require_once __DIR__ . '/get_restaurant_id.php';

// Accedemos a la variable global $categories
global $categories;

// Validación de restaurantId
if (!isset($restaurantId) || empty($restaurantId)) {
    error_log("⚠️ No se recibió restaurantId correctamente.");
    exit;
}

// Asegurar que $categories esté definida como array
if (!isset($categories) || !is_array($categories)) {
    $categories = [];
}

// Filtrar las categorías que pertenecen al restaurante actual
$filteredCategories = array_filter($categories, function ($cat) use ($restaurantId) {
    return isset($cat['restaurant_id']) && $cat['restaurant_id'] === $restaurantId;
});

// Ordenar por el campo `orden`
usort($filteredCategories, function ($a, $b) {
    return $a['orden'] <=> $b['orden'];
});

// Reindexar array
$filteredCategories = array_values($filteredCategories);

// (Opcional) Devolver en JSON para frontend o pruebas
// header('Content-Type: application/json');
// echo json_encode($filteredCategories, JSON_PRETTY_PRINT);
// exit;