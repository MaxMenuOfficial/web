<?php
// Si no hay ningún parámetro relevante (ej. `id`, `restaurantId`, etc.), redirige
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

if ($requestUri === '/' || $requestUri === '') {
    header('Location: https://maxmenu.com');
    exit;
}

?>