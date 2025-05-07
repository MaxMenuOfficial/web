<?php
// 📁 backend/php/get/get_logos.php

// Incluir el servicio que carga la información completa del restaurante y sus relaciones
require_once __DIR__ . '/../../config/menu-service.php';
// Incluir el script que obtiene y valida el restaurantId vía GET
require_once __DIR__ . '/get_restaurant_id.php';

// Declarar la variable global $logos (ya cargada en menu-service.php)
global $logos;

// Se espera que $restaurantId esté definido en show_get_restaurant_id.php
if (isset($restaurantId) && !empty($restaurantId)) {
    // Filtrar los logos específicos del restaurantId obtenido vía GET
    $logos = array_filter($logos, function ($logo) use ($restaurantId) {
        return isset($logo['restaurant_id']) && $logo['restaurant_id'] === $restaurantId;
    });

    // Convertir a array indexado para evitar problemas con claves asociativas
    $logos = array_values($logos);

    error_log("📌 Logos encontrados para restaurante $restaurantId: " . print_r($logos, true));
} else {
    $logos = [];
    error_log("⚠️ No se proporcionó un restaurantId válido.");
}

// (Opcional) Descomenta las siguientes líneas si deseas devolver el resultado en JSON
// header('Content-Type: application/json');
// echo json_encode($logos, JSON_PRETTY_PRINT);
// exit;
?>