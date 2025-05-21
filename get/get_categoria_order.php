<?php
// ✅ get_category_order.php – versión final, adaptable a todos los entornos

require_once __DIR__ . '/../config/menu-service.php';  // Carga $categories
require_once __DIR__ . '/get_restaurant_id.php';       // Carga $restaurantId desde GET, SESSION o fallback

global $categories;

if (!isset($categories) || !is_array($categories)) {
    $categories = [];
}

// 🔍 Filtrar por restaurant_id
$filteredCategories = array_filter($categories, function ($cat) use ($restaurantId) {
    return isset($cat['restaurant_id']) && $cat['restaurant_id'] === $restaurantId;
});

// 🔁 Ordenar por sort_order
usort($filteredCategories, function ($a, $b) {
    $orderA = isset($a['sort_order']) ? (int)$a['sort_order'] : PHP_INT_MAX;
    $orderB = isset($b['sort_order']) ? (int)$b['sort_order'] : PHP_INT_MAX;
    return $orderA <=> $orderB;
});

// 🧩 Reindexar y dejar en variable global esperada por la vista
$estructuraMenu = array_values($filteredCategories);

// (Opcional) JSON para debug
// header('Content-Type: application/json');
// echo json_encode($estructuraMenu, JSON_PRETTY_PRINT); exit;