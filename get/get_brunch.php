<?php


// Accedemos a la variable global $brunches (cargada en menu-service.php)
global $brunches;

// Verificar que el restaurantId fue obtenido correctamente
if (!isset($restaurantId) || empty($restaurantId)) {
   
    exit;
}

// Asegurar que la variable global $brunches esté definida y sea un array
if (!isset($brunches) || !is_array($brunches)) {
    $brunches = []; // En caso de no estar inicializado, forzamos un array vacío
}

// Filtrar los brunches que pertenezcan al restaurante actual
$filteredBrunches = array_filter($brunches, function ($brunch) use ($restaurantId) {
    return isset($brunch['restaurant_id']) && $brunch['restaurant_id'] === $restaurantId;
});

// Convertir a array indexado para evitar problemas con claves asociativas
$filteredBrunches = array_values($filteredBrunches);

// (Opcional) Si deseas devolver la respuesta en JSON
// header('Content-Type: application/json');
// echo json_encode($filteredBrunches, JSON_PRETTY_PRINT);
// exit;
?>