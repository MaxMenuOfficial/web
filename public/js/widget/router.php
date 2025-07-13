<?php
// File: /js/widget/router.php

$restaurantId = $_GET['id'] ?? null;
if (!$restaurantId) {
    http_response_code(400);
    exit('Missing restaurantId');
}

// ⚙️ Carga configuración
require_once __DIR__ . '/../../../config/bootstrap.php'; // o como conectes Spanner

$db = new SpannerDB();
$row = $db->execute(
    'SELECT menu_version FROM restaurants WHERE restaurant_id = @id',
    ['parameters' => ['id' => $restaurantId]]
)->rows()->current();

$version = $row['menu_version'] ?? 'v1';

// ✅ Redirección temporal o permanente
header("Location: /js/widget/versions/widget.{$version}.js", true, 302); // Si estás seguro, usa 301
exit;

