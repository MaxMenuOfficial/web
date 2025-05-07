<?php

// ✅ Asegurar que usuario-service.php está cargado
require_once __DIR__ . '/../config/menu-service.php';

// Se asume que la variable de sesión y la variable global $languages ya están definidas por usuario-service.php
$restaurantId = $_SESSION['restaurant_id'] ?? null;

if (!$restaurantId) {
    error_log("⚠️ Error: No se encontró ID de restaurante en la sesión.");
    $idiomasDisponibles = [];
} else {
    // Filtrar idiomas disponibles para el restaurante actual usando la variable global $languages
    $idiomasDisponibles = array_filter($languages, function ($language) use ($restaurantId) {
        return $language['restaurant_id'] === $restaurantId;
    });

    // Reindexar el array para evitar problemas con claves asociativas
    $idiomasDisponibles = array_values($idiomasDisponibles);
}
?>