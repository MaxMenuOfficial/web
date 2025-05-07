<?php
// Incluir el servicio de menú que carga las variables globales
require_once __DIR__ . '/../config/menu-service.php';
// Incluir el script que obtiene y valida el restaurantId vía GET
require_once __DIR__ . '/get_restaurant_id.php';

// Alias: convertimos el snake_case de menu-service a camelCase
global $menu_colors;          // esta viene de menu-service.php
$menuColors = $menu_colors;   // ahora sí tenemos la variable que espera tu script

// Obtenemos el restaurantId desde el parámetro GET
$restaurantId = $_GET['id'] ?? null;
if (!$restaurantId) {
    error_log("⚠️ No se proporcionó el id en la URL.");
    die("❌ No se proporcionó el id en la URL.");
}

// 🔍 Depuración: Verificar que `$menuColors` se haya cargado correctamente
error_log("📌 Datos en menuColors: " . json_encode($menuColors));

// 📌 Valores por defecto en caso de que no se encuentren colores guardados
$colores = [
    'backgroundColor'  => '#ffffff',
    'titleColor'       => '#000000',
    'descriptionColor' => '#000000',
    'priceColor'       => '#000000',
    'iconColor'        => '#000000'
];

if (!empty($menuColors)) {
    // 🔍 Buscar los colores correspondientes al restaurantId dentro de `$menuColors`
    $coloresData = array_filter($menuColors, function ($color) use ($restaurantId) {
        return isset($color['restaurant_id']) && $color['restaurant_id'] === $restaurantId;
    });

    if (!empty($coloresData)) {
        // ✅ Convertir el array filtrado en un solo objeto y asignarlo a `$colores`
        $colores = array_values($coloresData)[0];
        error_log("✅ Colores encontrados para el restaurante $restaurantId: " . json_encode($colores));
    } else {
        error_log("⚠️ No se encontraron colores en menuColors para el restaurante $restaurantId.");
    }
} else {
    error_log("⚠️ menuColors está vacío o no se ha cargado.");
}

// Aseguramos que cada color tenga un valor predeterminado (evitando valores nulos)
$colores = [
    'backgroundColor'  => $colores['background_color']  ?? '#ffffff',
    'titleColor'       => $colores['title_color']       ?? '#000000',
    'descriptionColor' => $colores['description_color'] ?? '#000000',
    'priceColor'       => $colores['price_color']       ?? '#000000',
    'iconColor'        => $colores['icon_color']        ?? '#000000'
];
?>