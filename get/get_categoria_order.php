<?php

// ✅ Asegurar que usuario-service.php está cargado
require_once __DIR__ . '/../../../config/usuario-service.php';

// ✅ Obtener el ID del restaurante desde la sesión
$idRestaurante = $_SESSION['id_restaurante'] ?? null;

if (!$idRestaurante) {
    error_log("⚠️ Error: No se encontró ID de restaurante en la sesión.");
    $categoriasOrdenadas = [];
} else {
    // ✅ Filtrar categorías del restaurante actual en memoria
    $categoriasOrdenadas = array_filter($categorias, function ($categoria) use ($idRestaurante) {
        return $categoria['id_restaurante'] === $idRestaurante;
    });

    // ✅ Ordenar por el campo `orden`
    usort($categoriasOrdenadas, function ($a, $b) {
        return $a['orden'] <=> $b['orden'];
    });

    // ✅ Reindexar el array después de filtrar y ordenar
    $categoriasOrdenadas = array_values($categoriasOrdenadas);
}

// Ahora `$categoriasOrdenadas` contiene todas las categorías de este restaurante, ordenadas correctamente.
?>