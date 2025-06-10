<?php
// ---------------------------------------------------------
// 🔗 Inicialización de entorno y obtención de restaurantId
// ---------------------------------------------------------
require_once __DIR__ . '/get_restaurant_id.php';          // define global $restaurantId
require_once __DIR__ . '/../config/menu-service.php';     // inicializa global $domains

global $restaurantId;

// ---------------------------------------------------------
// ✅ Configurar CORS para cualquier dominio (unrestricted CORS)
// ---------------------------------------------------------

// Si quieres permitir CUALQUIER dominio (estándar para widgets públicos):
header("Access-Control-Allow-Origin: *"); // <- Permite absolutamente cualquier origen

// Si quieres permitir solo HTTPS y evitar localhost, puedes añadir un filtro:
// $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
// if (stripos($origin, 'localhost') === false && stripos($origin, '127.0.0.1') === false) {
//     header("Access-Control-Allow-Origin: $origin");
// }

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: false'); // Debe ser false si Allow-Origin: *

// Responder al preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Aquí sigue la lógica de tu widget…