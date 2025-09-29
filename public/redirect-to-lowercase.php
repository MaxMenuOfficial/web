<?php
$uri = $_SERVER['REQUEST_URI'];
$lowerUri = strtolower($uri);

if ($uri !== $lowerUri) {
    header("Location: https://menu.maxmenu.com$lowerUri", true, 301);
    exit;
}

// Si ya está en minúsculas pero no existe, manda 404
http_response_code(404);
echo "Página no encontrada.";
exit;