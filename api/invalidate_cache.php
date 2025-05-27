<?php
// File: menu.maxmenu.com/api/invalidate_cache.php

// 🔥 Mostrar todos los errores en pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('html_errors', 1);
ini_set('log_errors', 0);
error_reporting(E_ALL);

// 🧠 Forzar salida HTML para ver errores + estructura limpia
header('Content-Type: text/html; charset=utf-8');

echo "<h2>🧪 Diagnóstico de entrada</h2>";

// ✅ Mostrar todo el contexto del entorno actual
echo "<pre>";
echo "📝 \$_POST:\n"; print_r($_POST);
echo "\n🌐 \$_GET:\n"; print_r($_GET);
echo "\n🧠 \$_SERVER:\n"; print_r([
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? '',
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '',
    'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? '',
]);
echo "\n🌍 \$_ENV:\n"; print_r($_ENV);
echo "</pre>";

// ✅ Cargar lógica
require_once __DIR__ . '/../../config/menu-service.php';
require_once __DIR__ . '/../../utils/cloudflare-utils.php';

echo "<h2>🧪 Verificando método</h2>";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "<p style='color:red'>❌ Método no permitido. Solo se permite POST.</p>";
    exit;
}

// 🔐 Captura de parámetros
$restaurantId   = $_POST['restaurant_id'] ?? null;
$token          = $_POST['token'] ?? null;
$expectedToken  = getenv('INTERNAL_CACHE_INVALIDATION_TOKEN');

echo "<h2>🔐 Parámetros capturados</h2>";
echo "<pre>";
echo "restaurant_id: " . htmlspecialchars($restaurantId) . "\n";
echo "token enviado: " . htmlspecialchars($token) . "\n";
echo "token esperado (.env): " . htmlspecialchars($expectedToken) . "\n";
echo "</pre>";

// ❌ Validación crítica
if (!$restaurantId || !$token || !$expectedToken || $token !== $expectedToken) {
    http_response_code(403);
    echo "<p style='color:red'>🚫 Token inválido o parámetros faltantes.</p>";
    exit;
}

echo "<h2>🧠 Ejecutando invalidación de caché</h2>";

try {
    MenuService::clearMenuCache($restaurantId);
    echo "<p style='color:green'>✅ Caché de memoria invalidada para $restaurantId</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>❌ Error en clearMenuCache: " . $e->getMessage() . "</p>";
}

try {
    purgeCloudflareCacheForRestaurant($restaurantId);
    echo "<p style='color:green'>✅ Cloudflare purgado para $restaurantId</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>❌ Error en purgeCloudflareCacheForRestaurant: " . $e->getMessage() . "</p>";
}

?>

<hr>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Diagnóstico Final</title>
</head>
<body>
  <h1>🧬 Diagnóstico completado — scdvsfbgnrh</h1>
</body>
</html>