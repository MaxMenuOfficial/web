<?php
//--------------------------------------------------
// 🔒 Utilidad: obtiene env-var o lanza excepción
//--------------------------------------------------
function envOrFail(string $key): string
{
    if (empty($_ENV[$key])) {
        throw new RuntimeException("Missing env var: $key");
    }
    return $_ENV[$key];
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

//--------------------------------------------------
// ✅ VALIDACIÓN DE PARÁMETRO
//--------------------------------------------------
$restaurantId = $_GET['id'] ?? null;
if (!$restaurantId || !preg_match('/^[a-zA-Z0-9_-]+$/', $restaurantId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetro inválido']);
    exit;
}

//--------------------------------------------------
// ✅ CONSULTA DIRECTA A SPANNER  (solo menu_version)
//--------------------------------------------------
require_once __DIR__ . '/../config/bootstrap.php';   // carga Dotenv + autoload

use Google\Cloud\Spanner\SpannerClient;

try {
    $spanner = new SpannerClient([
        'projectId' => envOrFail('DB_PROJECT_ID'),
    ]);

    $database = $spanner->connect(
        envOrFail('DB_INSTANCE_ID'),
        envOrFail('DB_DATABASE_ID')
    );

    $sql    = 'SELECT menu_version FROM restaurants WHERE restaurant_id = @restaurantId';
    $params = ['restaurantId' => $restaurantId];
    $types  = ['restaurantId' => 'STRING'];

    $result = $database->execute($sql, [
        'parameters'      => $params,
        'parameterTypes'  => $types
    ]);

    foreach ($result as $row) {
        echo json_encode(['version' => (int) $row['menu_version']]);
        exit;    // éxito → salimos
    }

    // restaurante no encontrado
    http_response_code(404);
    echo json_encode(['error' => 'Restaurante no encontrado']);

} catch (Throwable $e) {
    error_log("❌ menu-version.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
}