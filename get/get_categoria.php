<?php
// 📁 backend/php/show/show_categories.php

// Incluir el servicio de menú que carga las variables globales
require_once __DIR__ . '/../config/menu-service.php';
// Incluir el script que obtiene y valida el restaurantId vía GET
require_once __DIR__ . '/get_restaurant_id.php';

// Accedemos a la variable global $categories (cargada en menu-service.php)
global $categories;

// Verificamos que el restaurantId fue obtenido correctamente
if (!isset($restaurantId) || empty($restaurantId)) {
    echo "Error: Restaurante no seleccionado.";
    exit;
}

// Asegurar que la variable global $categories esté definida
if (!isset($categories) || !is_array($categories)) {
    $categories = []; // En caso de no estar inicializado, forzamos un array vacío
}

// Filtrar las categorías que pertenezcan al restaurante actual
$filteredCategories = array_filter($categories, function ($cat) use ($restaurantId) {
    return isset($cat['restaurant_id']) && $cat['restaurant_id'] === $restaurantId;
});

// Convertir a array indexado para evitar problemas con claves asociativas
$filteredCategories = array_values($filteredCategories);

// (Opcional) Puedes descomentar estas líneas para devolver la respuesta en JSON
// header('Content-Type: application/json');
// echo json_encode($filteredCategories, JSON_PRETTY_PRINT);
// exit;
?>