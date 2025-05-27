<?php
// File: /var/www/html/config/menu-service.php

require_once __DIR__ . '/conexion.php';

/**
 * Servicio de menú: carga de datos públicos de restaurante y sus recursos asociados.
 */
if (!class_exists('MenuService')) {
    class MenuService
    {
        /** @var \Google\Cloud\Spanner\Database */
        private $database;
        /** @var array<string,array> */
        private static array $cache = [];

        public function __construct()
        {
            global $database;
            if (empty($database)) {
                try {
                    $database = Conexion::spanner();
                } catch (\Exception $e) {
                    throw new \RuntimeException('Conexión a Spanner fallida: ' . $e->getMessage());
                }
            }
            $this->database = $database;
        }

        public static function clearCache(string $restaurantId): void
        {
            unset(self::$cache[$restaurantId]);
        }

        public static function clearMenuCache(string $restaurantId): void
        {
            self::clearCache($restaurantId);
        }

        /**
         * Obtiene todos los datos públicos del restaurante con caching in-memory
         * @param string $restaurantId
         * @return array|null
         */
        public function getRestaurantPublicData(string $restaurantId): ?array
        {
            if (isset(self::$cache[$restaurantId])) {
                return self::$cache[$restaurantId];
            }

            $sql = <<<'SQL'
WITH restaurant_data AS (
  SELECT *
    FROM restaurants
   WHERE restaurant_id = @restaurant_id
   LIMIT 1
)
SELECT
  r.*,
  ARRAY(
    SELECT AS STRUCT c.*
      FROM categories c
     WHERE c.restaurant_id = r.restaurant_id
  ) AS categories,
  ARRAY(
    SELECT AS STRUCT s.*
      FROM subcategories s
     WHERE s.restaurant_id = r.restaurant_id
  ) AS subcategories,
  ARRAY(
    SELECT AS STRUCT i.*
      FROM items i
     WHERE i.restaurant_id = r.restaurant_id
  ) AS items,
  ARRAY(
    SELECT AS STRUCT l.*
      FROM logos l
     WHERE l.restaurant_id = r.restaurant_id
  ) AS logos,
  ARRAY(
    SELECT AS STRUCT p.*
      FROM platforms p
     WHERE p.restaurant_id = r.restaurant_id
  ) AS platforms,
  ARRAY(
    SELECT AS STRUCT lang.*
      FROM languages lang
     WHERE lang.restaurant_id = r.restaurant_id
  ) AS languages,
  ARRAY(
    SELECT AS STRUCT ct.*
      FROM category_translations ct
     WHERE ct.restaurant_id = r.restaurant_id
  ) AS category_translations,
  ARRAY(
    SELECT AS STRUCT st.*
      FROM subcategory_translations st
     WHERE st.restaurant_id = r.restaurant_id
  ) AS subcategory_translations,
  ARRAY(
    SELECT AS STRUCT it.*
      FROM item_translations it
     WHERE it.restaurant_id = r.restaurant_id
  ) AS item_translations,
  ARRAY(
    SELECT AS STRUCT sup.*
      FROM item_supplements sup
     WHERE sup.restaurant_id = r.restaurant_id
  ) AS item_supplements,
  ARRAY(
    SELECT AS STRUCT b.*
      FROM brunch b
     WHERE b.restaurant_id = r.restaurant_id
  ) AS brunches,
  ARRAY(
    SELECT AS STRUCT dm.*
      FROM daily_menu dm
     WHERE dm.restaurant_id = r.restaurant_id
  ) AS daily_menu,
  ARRAY(
    SELECT AS STRUCT mc.*
      FROM menu_colors mc
     WHERE mc.restaurant_id = r.restaurant_id
  ) AS menu_colors,
  ARRAY(
    SELECT AS STRUCT rd.*
      FROM restaurant_domains rd
     WHERE rd.restaurant_id = r.restaurant_id
  ) AS domains
FROM restaurant_data r;
SQL;
            try {
                $result = $this->database->execute($sql, [
                    'parameters' => ['restaurant_id' => $restaurantId]
                ]);
                $rows = iterator_to_array($result->rows());
                $data = $rows[0] ?? null;
                return self::$cache[$restaurantId] = $data;
            } catch (\Exception $e) {
                error_log('❌ MenuService::getRestaurantPublicData error: ' . $e->getMessage());
                return null;
            }
        }
    }
}

// ---------------------------------------------------------------------------
// Inicialización de presentación: sólo si no es llamada de invalidador de cache
// ---------------------------------------------------------------------------
$uri = $_SERVER['REQUEST_URI'] ?? '';
$isCacheInvalidator = str_contains($uri, '/api/invalidate');
if (!$isCacheInvalidator) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    global 
        $restaurantId, $restaurantData,
        $categories, $subcategories, $items, $logos,
        $platforms, $languages, $category_translations,
        $subcategory_translations, $item_translations,
        $item_supplements, $brunches, $daily_menu,
        $menu_colors, $domains;

    $restaurantId   = $_GET['id'] ?? null;
    $restaurantData = null;
    $categories     = [];
    $subcategories  = [];
    $items          = [];
    $logos          = [];
    $platforms      = [];
    $languages      = [];
    $category_translations    = [];
    $subcategory_translations = [];
    $item_translations        = [];
    $item_supplements         = [];
    $brunches      = [];
    $daily_menu    = [];
    $menu_colors   = [];
    $domains       = [];

    if ($restaurantId) {
        $svc  = new MenuService();
        $data = $svc->getRestaurantPublicData($restaurantId);

        if (!$data || !isset($data['restaurant_id'])) {
            header('Location: https://maxmenu.com');
            exit;
        }

        $restaurantData           = $data;
        $categories               = $data['categories']               ?? [];
        $subcategories            = $data['subcategories']            ?? [];
        $items                    = $data['items']                    ?? [];
        $logos                    = $data['logos']                    ?? [];
        $platforms                = $data['platforms']                ?? [];
        $languages                = $data['languages']                ?? [];
        $category_translations    = $data['category_translations']    ?? [];
        $subcategory_translations = $data['subcategory_translations'] ?? [];
        $item_translations        = $data['item_translations']        ?? [];
        $item_supplements         = $data['item_supplements']         ?? [];
        $brunches                 = $data['brunches']                 ?? [];
        $daily_menu               = $data['daily_menu']               ?? [];
        $menu_colors              = $data['menu_colors']              ?? [];
        $domains                  = $data['domains']                  ?? [];
    } else {
        header('Location: https://maxmenu.com');
        exit;
    }
}