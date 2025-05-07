<?php

// Incluir el servicio de menú que carga las variables globales
require_once __DIR__ . '/../config/menu-service.php';

// ✅ Accedemos a la variable global `$restaurantData` (cargada en `menu-service.php`)
global $restaurantData;

// ✅ Verificar que `$restaurantData` contiene datos
if (!isset($restaurantData) || !is_array($restaurantData) || empty($restaurantData)) {
    die("Error: No se pudo cargar la información del restaurante desde menu-service.php");
}

// ✅ Obtener el diseño de alérgenos (si está definido, sino, usar `1` como valor por defecto)
$diseñoAlergenos = $restaurantData['allergens_design'] ?? 1;

?>