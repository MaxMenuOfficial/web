<?php
// File: /var/www/html/config/menu-visibility.php

// 🚩 Validamos que los datos globales ya existen
global $restaurantData, $restaurantId;

if (!$restaurantId || empty($restaurantData['restaurant_id'])) {
    http_response_code(404); // 🚫 No encontrado
    echo json_encode(['error' => 'Restaurant not found']);
    exit;
}

// 🔒 Evaluamos flags de visibilidad
$isActive       = !empty($restaurantData['is_active']);
$isActiveForAll = !empty($restaurantData['is_active_for_all']);

// Caso inválido (ambos en 1)
if ($isActive && $isActiveForAll) {
    error_log("[MenuVisibility] ❌ Lógica inválida: is_active + is_active_for_all ambos en 1 para restaurant_id={$restaurantId}");
    http_response_code(500);
    echo "Este menú no está disponible.";
    exit;
}

// Caso menú oculto (ninguno activo)
if (!$isActive && !$isActiveForAll) {
    http_response_code(403);
    echo "<!DOCTYPE html>
    <html><head><title>Menú no disponible</title></head>
    <body style='text-align: center; padding: 100px 20px; font-family: sans-serif;'>
        <h1>Este menú no está disponible temporalmente.</h1>
        <p>Gracias por tu paciencia.</p>
    </body></html>";
    exit;
}

// ✅ Caso válido → menú visible
http_response_code(200);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['visible' => true, 'restaurant_id' => $restaurantId]);