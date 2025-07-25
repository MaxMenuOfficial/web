<?php


// ✅ Accedemos a la variable global `$restaurantData` (cargada en `menu-service.php`)
global $restaurantData;

// ✅ Obtener el diseño de alérgenos (si está definido, sino, usar `1` como valor por defecto)
$diseñoAlergenos = $restaurantData['allergens_design'] ?? 1;

?>