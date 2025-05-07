<?php
// 📁 backend/php/get/get_languages.php

// Incluir el servicio de menú que carga las variables globales
require_once __DIR__ . '/../config/menu-service.php';
// Incluir el script que obtiene y valida el restaurantId vía GET
require_once __DIR__ . '/get_restaurant_id.php';

// Declarar las variables globales para usarlas en este script
global $languages, $restaurantData;

// Verificar que el restaurantId fue obtenido correctamente
if (!isset($restaurantId) || empty($restaurantId)) {
    die("Error: No se encontró el restaurantId.");
}

/**
 * Función para obtener los idiomas disponibles del restaurante
 */
function obtenerIdiomas($languages, $restaurantId) {
    if (!isset($languages) || !is_array($languages)) {
        error_log("⚠️ Variable global \$languages no está definida o no es un array.");
        return []; 
    }
    return array_filter($languages, function ($idioma) use ($restaurantId) {
        return isset($idioma['restaurant_id']) && $idioma['restaurant_id'] === $restaurantId;
    });
}

/**
 * Función para obtener el idioma principal del restaurante
 */
function obtenerIdiomaPrincipal($restaurantData) {
    if (!isset($restaurantData) || !is_array($restaurantData)) {
        error_log("⚠️ Variable global \$restaurantData no está definida o no es un array.");
        return null;
    }
    return $restaurantData['language_code'] ?? null;
}

// Obtener los idiomas disponibles y el idioma principal del restaurante
$idiomasDisponibles = obtenerIdiomas($languages, $restaurantId);
$idiomaPrincipal = obtenerIdiomaPrincipal($restaurantData);

// Construir lista de códigos de idiomas existentes
$codigosIdiomasExistentes = [];
if (is_array($idiomasDisponibles) && !empty($idiomasDisponibles)) {
    $codigosIdiomasExistentes = array_filter(
        array_column($idiomasDisponibles, 'language_code'),
        function($code) {
            return !empty($code);
        }
    );
}

// Asegurar que el idioma principal esté incluido en la lista
if ($idiomaPrincipal && !in_array($idiomaPrincipal, $codigosIdiomasExistentes)) {
    $codigosIdiomasExistentes[] = $idiomaPrincipal;
}

// (Opcional) Si deseas devolver la respuesta en JSON
// header('Content-Type: application/json');
// echo json_encode([
//     'idiomasDisponibles' => $idiomasDisponibles,
//     'idiomaPrincipal' => $idiomaPrincipal,
//     'codigosIdiomasExistentes' => $codigosIdiomasExistentes
// ], JSON_PRETTY_PRINT);
// exit;
?>