<?php

// Verificamos que se haya pasado el parámetro 'id' vía GET
if (!empty($_GET['id'])) {
    $restaurantId = $_GET['id'];
}
// Sanitizamos el parámetro recibido
$restaurantId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

// Validar la sintaxis de $restaurantId (solo letras, números, guiones, guiones bajos y puntos)
if (!preg_match("/^[a-zA-Z0-9\-_\.]+$/", $restaurantId)) {
    die("Error: Formato de id no válido.");
}

// 🔍 Imprimir el ID para verificar si llega correctamente
error_log("✅ get_restaurant_id.php - ID recibido: $restaurantId");

// Ahora $restaurantId está validado y listo para usarse sin almacenarlo en sesión.

?>