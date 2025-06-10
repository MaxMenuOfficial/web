<?php
// ----------------------------------------
// ✅ CABECERAS CONTROLADAS Y CLARAS
// ----------------------------------------
header_remove('Cache-Control');
header_remove('Expires');
header_remove('Pragma');

header('Content-Type: application/json');
header('Cache-Control: public, max-age=86400, stale-while-revalidate=3600, stale-if-error=300');
header('Access-Control-Allow-Origin: *');

// ----------------------------------------
// ✅ VALIDACIÓN DE PARÁMETRO
// ----------------------------------------
$restaurantId = $_GET['id'] ?? null;
if (!$restaurantId || !preg_match('/^[a-zA-Z0-9_-]+$/', $restaurantId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetro inválido']);
    exit;
}

// ----------------------------------------
// ✅ CONSULTA DIRECTA A SPANNER
// Solo extraemos `menu_version` y nada más
// ----------------------------------------
require_once __DIR__ . '/../config/bootstrap.php'; // Solo carga conexión

use Google\Cloud\Spanner\SpannerClient;

try {
    $spanner = new SpannerClient([
        'projectId' => $_ENV['DB_PROJECT_ID'],
    ]);

    $database = $spanner->connect($_ENV['DB_INSTANCE'], $_ENV['DB_DATABASE']);

    $sql = 'SELECT menu_version FROM restaurants WHERE restaurant_id = @restaurantId';
    $params = ['restaurantId' => $restaurantId];
    $types = ['restaurantId' => 'STRING'];

    $result = $database->execute($sql, ['parameters' => $params, 'parameterTypes' => $types]);

    foreach ($result as $row) {
        echo json_encode(['version' => (int)$row['menu_version']]);
        exit;
    }

    // No encontrado
    http_response_code(404);
    echo json_encode(['error' => 'Restaurante no encontrado']);
} catch (Throwable $e) {
    error_log("❌ menu-version.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
}