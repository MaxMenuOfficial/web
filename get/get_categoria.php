<?php
// Accedemos a la variable global $categories (cargada en menu-service.php)
global $categories;

$restaurantId = $_SESSION['restaurant_id'] ?? null;

if (!$userId || !$restaurantId) {
    echo "❌ Error: Usuario o restaurante no seleccionado.";
    exit;
}

// ✅ Aseguramos que $categories es un array
if (!is_array($categories)) {
    $categories = [];
}

// 🔍 Filtrar solo las categorías del restaurante actual
$filteredCategories = array_filter($categories, function ($cat) use ($restaurantId) {
    return isset($cat['restaurant_id']) && $cat['restaurant_id'] === $restaurantId;
});

// 🔢 Ordenar las categorías por sort_order (ASC)
usort($filteredCategories, function ($a, $b) {
    $orderA = isset($a['sort_order']) ? (int)$a['sort_order'] : 0;
    $orderB = isset($b['sort_order']) ? (int)$b['sort_order'] : 0;
    return $orderA <=> $orderB;
});

// ✅ Ahora $filteredCategories contiene las categorías del restaurante actual, ordenadas por sort_order