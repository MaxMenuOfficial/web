<?php
// Activar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el servicio de usuario
require_once __DIR__ . '/../../../config/usuario-service.php';

// Asegurar que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que hay un restaurante seleccionado en la sesión
if (!isset($_SESSION['restaurant_id'])) {
    die("❌ No hay restaurante seleccionado en la sesión.");
}
$restaurantId = $_SESSION['restaurant_id'];
// Función para obtener categorías y subcategorías de un restaurante usando usuario-service.php
// Se utiliza la nomenclatura exacta de la base de datos:
//   Categorías: category_id, restaurant_id, category_name, sort_order
//   Subcategorías: subcategory_id, category_id, restaurant_id, subcategory_name, sort_order
function obtenerCategoriasYSubcategorias($restaurantId) {
    try {
        // Instanciar el servicio de usuario
        $usuarioService = new UsuarioService();

        // Verificar que hay un usuario en sesión mediante su user_id
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("❌ No hay user_id en sesión.");
        }
        $userId = $_SESSION['user_id'];
      

        // Obtener datos completos del usuario usando getUserData()
        $datosUsuario = $usuarioService->getUserData($userId);
        if (!$datosUsuario) {
            throw new Exception("❌ No se encontraron datos del usuario.");
        }

        // Filtrar categorías del restaurante (se asume que se almacenan en la clave 'categories')
        $categorias = array_filter($datosUsuario['categories'], function ($categoria) use ($restaurantId) {
            return trim($categoria['restaurant_id']) === trim($restaurantId);
        });

        // Filtrar subcategorías del restaurante (se asume que se almacenan en la clave 'subcategories')
        $subcategorias = array_filter($datosUsuario['subcategories'], function ($subcategoria) use ($restaurantId) {
            return trim($subcategoria['restaurant_id']) === trim($restaurantId);
        });

        // Asignar cada subcategoría a su categoría correspondiente (comparando 'category_id')
        foreach ($categorias as &$categoria) {
            $categoria['subcategorias'] = array_values(array_filter($subcategorias, function ($subcategoria) use ($categoria) {
                return trim((string)$subcategoria['category_id']) === trim((string)$categoria['category_id']);
            }));
        }
        unset($categoria); // Evitar referencia residual

        // Ordenar las categorías por su nombre (usando 'category_name')
        usort($categorias, function ($a, $b) {
            return strcmp($a['category_name'], $b['category_name']);
        });


        return $categorias;
    } catch (Exception $e) {
        error_log('❌ Error al obtener categorías y subcategorías: ' . $e->getMessage());
        die("❌ Error: " . $e->getMessage());
    }
}

// Obtener categorías y subcategorías del restaurante en sesión
$categorias = obtenerCategoriasYSubcategorias($restaurantId);

?>