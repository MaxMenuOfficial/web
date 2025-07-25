<?php
// 📁 backend/php/get/get_languages_available.php

// Accedemos a la variable global $languages (cargada en menu-service.php)
global $languages;

// Verificar que el restaurantId fue obtenido correctamente
if (!isset($restaurantId) || empty($restaurantId)) {
    error_log("⚠️ Error: No se encontró ID de restaurante en la URL.");
    $idiomasDisponibles = [];
} else {
    // Filtrar idiomas disponibles para el restaurante actual
    $idiomasDisponibles = array_filter($languages, function ($language) use ($restaurantId) {
        return isset($language['restaurant_id']) && $language['restaurant_id'] === $restaurantId;
    });

    // Reindexar el array para evitar problemas con claves asociativas
    $idiomasDisponibles = array_values($idiomasDisponibles);
}

// (Opcional) Si deseas devolver la respuesta en JSON
// header('Content-Type: application/json');
// echo json_encode($idiomasDisponibles, JSON_PRETTY_PRINT);
// exit;
?>