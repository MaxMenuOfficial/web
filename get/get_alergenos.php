<?php

// Incluir el script que obtiene y valida el restaurantId vía GET (sin usar la sesión)
require_once __DIR__ . '/get_restaurant_id.php';
// Incluir el servicio de menú que carga las variables globales
require_once __DIR__ . '/../config/menu-service.php';

// ✅ Accedemos a la variable global `$restaurantData` (cargada en `menu-service.php`)
global $restaurantData;

// ✅ Obtener el diseño de alérgenos (si está definido, sino, usar `1` como valor por defecto)
$diseñoAlergenos = $restaurantData['allergens_design'] ?? 1;

?>