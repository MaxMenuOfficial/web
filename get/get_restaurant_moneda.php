<?php
// 📁 backend/php/get/get_restaurant_info.php

// Incluir el servicio de menú que carga las variables globales
require_once __DIR__ . '/../config/menu-service.php';
// Incluir el script que obtiene y valida el restaurantId vía GET
require_once __DIR__ . '/get_restaurant_id.php';

// Accedemos a la variable global $restaurantData (cargada en menu-service.php)
global $restaurantData;

// Verificar que el restaurantId fue obtenido correctamente
if (!isset($restaurantId) || empty($restaurantId)) {
    die("Error: Restaurante no seleccionado.");
}

// ✅ Verificar si $restaurantData está definido y es un array
if (!isset($restaurantData) || !is_array($restaurantData)) {
    $restaurantData = []; // Inicializar vacío si no está definido
}

// ✅ Extraer la información relevante del restaurante
$restauranteInfo = [
    'moneda'          => $restaurantData['currency'] ?? null,
    'simbolo_moneda'  => $restaurantData['currency_symbol'] ?? null,
    'idioma_original' => $restaurantData['language_code'] ?? null,
];

// (Opcional) Si deseas devolver el resultado en JSON
// header('Content-Type: application/json');
// echo json_encode($restauranteInfo, JSON_PRETTY_PRINT);
// exit;
?>