<?php
// File: /var/www/html/config/menu-visibility.php

global $restaurantData, $restaurantId;

// 🔒 Evaluamos flags de visibilidad
$isActive       = !empty($restaurantData['is_active']);
$isActiveForAll = !empty($restaurantData['is_active_for_all']);

// Caso menú oculto (ninguno activo)
if (!$isActive && !$isActiveForAll) {
    echo "<!DOCTYPE html>
    <html><head><title>Menú no disponible</title></head>
    <body style='text-align: center; padding: 100px 20px; font-family: sans-serif;'>
        <h1>Este menú no está disponible temporalmente.</h1>
        <p>Gracias por tu paciencia.</p>
    </body></html>";
    exit;
}


// ✅ Caso válido → sigue el flujo normal y se muestra el menú