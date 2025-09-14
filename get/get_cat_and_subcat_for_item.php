<?php

// Verificamos que se haya obtenido el restaurantId
if (!isset($restaurantId) || empty($restaurantId)) {
    die("❌ No se proporcionó el restaurantId.");
}

/**
 * Función para obtener la estructura del menú (categorías, subcategorías e ítems)
 * utilizando las variables globales definidas en menu-service.php.
 *
 * @param string $restaurantId El ID del restaurante a filtrar.
 * @return array Estructura del menú filtrada.
 */
function obtenerEstructuraMenu($restaurantId) {
    // Accedemos a las variables globales que contienen los datos precargados
    global $categories, $subcategories, $items;

    // Filtrar las categorías que correspondan al restaurantId
    $categorias = array_filter($categories, function ($categoria) use ($restaurantId) {
        return trim($categoria['restaurant_id']) === trim($restaurantId);
    });

    // Filtrar las subcategorías para el restaurantId
    $subcategorias = array_filter($subcategories, function ($subcategoria) use ($restaurantId) {
        return trim($subcategoria['restaurant_id']) === trim($restaurantId);
    });

    // Filtrar los ítems para el restaurantId
    $itemsFiltrados = array_filter($items, function ($item) use ($restaurantId) {
        return trim($item['restaurant_id']) === trim($restaurantId);
    });

    // Asociar los ítems a sus subcategorías correspondientes
    foreach ($subcategorias as &$subcategoria) {
        $subcategoria['items'] = array_values(array_filter($itemsFiltrados, function ($item) use ($subcategoria) {
            return trim((string)$item['subcategory_id']) === trim((string)$subcategoria['subcategory_id']);
        }));

        // Ordenar ítems dentro de la subcategoría por sort_order
        usort($subcategoria['items'], function ($a, $b) {
            $ordenA = $a['sort_order'] ?? PHP_INT_MAX;
            $ordenB = $b['sort_order'] ?? PHP_INT_MAX;
            return $ordenA <=> $ordenB;
        });
    }
    unset($subcategoria);

    // Separar los ítems que no tienen subcategoría (campo subcategory_id vacío o nulo)
    $itemsSinSubcategoria = array_filter($itemsFiltrados, function ($item) {
        return empty($item['subcategory_id']);
    });

    // Asociar las subcategorías (y los ítems sin subcategoría) a su categoría correspondiente
    foreach ($categorias as &$categoria) {
        // Subcategorías para esta categoría
        $categoria['subcategorias'] = array_values(array_filter($subcategorias, function ($subcategoria) use ($categoria) {
            return trim((string)$subcategoria['category_id']) === trim((string)$categoria['category_id']);
        }));

        // Ordenar subcategorías por sort_order
        usort($categoria['subcategorias'], function ($a, $b) {
            $ordenA = $a['sort_order'] ?? PHP_INT_MAX;
            $ordenB = $b['sort_order'] ?? PHP_INT_MAX;
            return $ordenA <=> $ordenB;
        });

        // Ítems sin subcategoría para esta categoría
        $categoria['items'] = array_values(array_filter($itemsSinSubcategoria, function ($item) use ($categoria) {
            return trim((string)$item['category_id']) === trim((string)$categoria['category_id']);
        }));

        // Ordenar ítems sin subcategoría por sort_order
        usort($categoria['items'], function ($a, $b) {
            $ordenA = $a['sort_order'] ?? PHP_INT_MAX;
            $ordenB = $b['sort_order'] ?? PHP_INT_MAX;
            return $ordenA <=> $ordenB;
        });
    }
    unset($categoria);

    // Ordenar las categorías por sort_order
    usort($categorias, function ($a, $b) {
        $ordenA = $a['sort_order'] ?? PHP_INT_MAX;
        $ordenB = $b['sort_order'] ?? PHP_INT_MAX;
        return $ordenA <=> $ordenB;
    });

    return $categorias;
}

// Obtener la estructura completa del menú usando el restaurantId obtenido vía GET
$estructuraMenu = obtenerEstructuraMenu($restaurantId);