<?php
// 📁 backend/php/get/get_currency.php
// Incluir el script que obtiene y valida el restaurantId vía GET (sin usar la sesión)

// Accedemos a la variable global $restaurantData (cargada en menu-service.php)
global $restaurantData;

// ✅ Variables iniciales de moneda
$simbolo_moneda = '';
$moneda = '';

// ✅ Verificar si $restaurantData está definido y es un array
if (isset($restaurantData) && is_array($restaurantData)) {
    $simbolo_moneda = $restaurantData['currency_symbol'] ?? '';  // ✅ Usando el nombre correcto
    $moneda = $restaurantData['currency'] ?? '';  // ✅ Usando el nombre correcto
}

// ✅ Lista de monedas donde el símbolo va después del precio
$monedasSimboloDespues = ['EUR', 'CHF', 'NOK', 'SEK', 'DKK', 'PLN', 'RUB'];

/**
 * ✅ Función para mostrar precios con el símbolo en la posición correcta
 */
function mostrarPrecio($precio, $simbolo, $moneda) {
    global $monedasSimboloDespues;

    if (in_array($moneda, $monedasSimboloDespues)) {
        return safe_output($precio) . ' ' . safe_output($simbolo);
    } else {
        return safe_output($simbolo) . ' ' . safe_output($precio);
    }
}

/**
 * ✅ Función para evitar inyecciones XSS al imprimir valores
 */
function safe_output($value) {
    return $value !== null ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : '';
}

// (Opcional) Si deseas devolver el resultado en JSON
// header('Content-Type: application/json');
// echo json_encode([
//     'moneda' => $moneda,
//     'simbolo_moneda' => $simbolo_moneda
// ], JSON_PRETTY_PRINT);
// exit;
?>