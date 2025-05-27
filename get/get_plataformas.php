<?php
// 📁 backend/php/get/get_platforms.php
// Incluir el script que obtiene y valida el restaurantId vía GET (sin usar la sesión)
require_once __DIR__ . '/get_restaurant_id.php';

// Incluir el servicio de menú que carga las variables globales
require_once __DIR__ . '/../config/menu-service.php';


// Accedemos a la variable global $platforms (cargada en menu-service.php)
global $platforms;

// Verificar que el restaurantId fue obtenido correctamente
if (!isset($restaurantId) || empty($restaurantId)) {
    die("Error: Restaurante no seleccionado.");
}

// ✅ Asegurar que la variable global $platforms está definida y es un array
if (!isset($platforms) || !is_array($platforms)) {
    $platforms = []; // Inicializar vacío si no está definida
}

/**
 * ✅ Función para obtener plataformas disponibles para el restaurante actual
 */
function obtenerPlataformas($platforms, $restaurantId) {
    $plataformasExistentes = [];
    foreach ($platforms as $platform) {
        if (isset($platform['restaurant_id']) && $platform['restaurant_id'] === $restaurantId) {
            $plataformasExistentes[strtolower($platform['platform_name'])] = $platform;
        }
    }
    return $plataformasExistentes;
}

// 🔥 Filtrar plataformas para el restaurante actual
$plataformasExistentes = obtenerPlataformas($platforms, $restaurantId);

// (Opcional) Si deseas devolver el resultado en JSON
// header('Content-Type: application/json');
// echo json_encode($plataformasExistentes, JSON_PRETTY_PRINT);
// exit;
?>