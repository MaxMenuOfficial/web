<?php
// 📁 backend/php/show/show_daily_menu.php

// Incluir el servicio de menú que carga las variables globales
require_once __DIR__ . '/../config/menu-service.php';
// Incluir el script que obtiene y valida el restaurantId vía GET
require_once __DIR__ . '/get_restaurant_id.php';

// Accedemos a la variable global $dailyMenu (cargada en menu-service.php)
global $daily_menu;
$dailyMenu = $daily_menu;

// Verificar que el restaurantId fue obtenido correctamente
if (!isset($restaurantId) || empty($restaurantId)) {
    echo "Error: Restaurante no seleccionado.";
    exit;
}

// Asegurar que la variable global $dailyMenu esté definida y sea un array
if (!isset($dailyMenu) || !is_array($dailyMenu)) {
    $dailyMenu = []; // En caso de no estar inicializado, forzamos un array vacío
}

// Filtrar los registros de daily_menu que pertenecen al restaurante actual
$filteredDailyMenu = array_filter($dailyMenu, function ($menu) use ($restaurantId) {
    return isset($menu['restaurant_id']) && $menu['restaurant_id'] === $restaurantId;
});

// Convertir a array indexado para evitar problemas con claves asociativas
$filteredDailyMenu = array_values($filteredDailyMenu);

// (Opcional) Si deseas devolver la respuesta en JSON
// header('Content-Type: application/json');
// echo json_encode($filteredDailyMenu, JSON_PRETTY_PRINT);
// exit;
?>