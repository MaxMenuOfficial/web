<?php

// Incluir el servicio que carga los datos completos del restaurante y sus relaciones.
require_once __DIR__ . '/../config/menu-service.php';
// Incluir el archivo que obtiene y valida el id del restaurante (estándar: ?id=...)
require_once __DIR__ . '/get_restaurant_id.php';

// Especificamos el Content-Type para HTML (o JSON, según necesidad)
if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
}

// Verificar que se haya recibido el parámetro "id" (estándar en la aplicación)
if (empty($restaurantId)) {
  
}

// --- Construir el array global de traducciones ---
// Se espera que en $languages (cargado en menu-service.php) estén los idiomas disponibles
$allTranslations = [];
foreach ($languages as $lang) {
    if (!isset($lang['language_id'])) {
        continue;
    }
    $languageId = $lang['language_id'];
    // Para cada idioma, se genera la estructura de traducciones
    $allTranslations[$languageId] = [
        'categories' => getTranslatedCategoriesAndItems($languageId)
    ];
}

// --- Procesar el idioma seleccionado ---
// Se toma el id del idioma enviado por POST o se usa el que está en la sesión (valor por defecto: 'default')
$idIdioma = filter_input(INPUT_POST, 'id_idioma', FILTER_SANITIZE_NUMBER_INT) ?: ($_SESSION['id_idioma'] ?? 'default');
$_SESSION['id_idioma'] = $idIdioma;

// --- Obtener el language_code a partir del array $languages ---
$languageCode = null;
foreach ($languages as $language) {
    if (isset($language['language_id']) && $language['language_id'] == $idIdioma) {
        $languageCode = $language['language_code'] ?? null;
        break;
    }
}

// --- Array de banderas (para idiomas disponibles) ---
$banderas = [
    'en' => 'https://menu.maxmenu.com/menu/img/flags/flag-england.png',
    'es' => 'https://menu.maxmenu.com/menu/img/flags/flag-spain.png',
    'fr' => 'https://menu.maxmenu.com/menu/img/flags/flag-france.png',
    'de' => 'https://menu.maxmenu.com/menu/img/flags/flag-germany.png',
    'it' => 'https://menu.maxmenu.com/menu/img/flags/flag-italy.png',
    'pt' => 'https://menu.maxmenu.com/menu/img/flags/flag-portugal.png',
    'ru' => 'https://menu.maxmenu.com/menu/img/flags/flag-russia.png',
    'nl' => 'https://menu.maxmenu.com/menu/img/flags/flag-netherlands.png',
    'pl' => 'https://menu.maxmenu.com/menu/img/flags/flag-poland.png',
    'sv' => 'https://menu.maxmenu.com/menu/img/flags/flag-sweden.png',
    'zh' => 'https://menu.maxmenu.com/menu/img/flags/flag-china.png',
    'ca' => 'https://menu.maxmenu.com/menu/img/flags/flag-catalonia.png',
    'ro' => 'https://menu.maxmenu.com/menu/img/flags/flag-romania.png',
    'ar' => 'https://menu.maxmenu.com/menu/img/flags/flag-arabe.png'
];

// Del array devuelto por menu-service.php
$originalLanguageName = $restaurantData['original_language'] ?? ''; // p. ej. "Español"
$originalLanguageCode = $restaurantData['language_code'] ?? '';     // p. ej. "es"
// --- Extra: Obtener la bandera del idioma original del restaurante ---
$banderaUrlOriginal = $banderas[$originalLanguageCode] ?? 'menu/img/flags/default.png';


// Ahora sí, manejar la selección del idioma
if ($originalLanguageCode) {
    $_SESSION['language_selected'] = $originalLanguageCode;
    $_SESSION['flag_selected'] = $banderas[$originalLanguageCode] ?? 'menu/img/flags/flag-spain.png';
} else {
    $_SESSION['flag_selected'] = $banderaUrlOriginal;
}

// --- Extraer las traducciones del idioma seleccionado ---
$translatedCategories = isset($allTranslations[$idIdioma])
    ? $allTranslations[$idIdioma]['categories']
    : [];


/**
 * Función que construye las traducciones de un idioma específico (categorías, subcategorías, ítems)
 * utilizando las variables globales cargadas desde el servicio (menu-service.php).
 */
function getTranslatedCategoriesAndItems($idIdioma) {
    global $categories, $subcategories, $items;
    global $category_translations, $subcategory_translations, $item_translations;

    $translatedCategories = [];

    foreach ($categories as $category) {
        $originalCategoryName = isset($category['category_name']) ? $category['category_name'] : 'Unnamed';
        $translatedCategoryName = $originalCategoryName;

        // Buscar traducción para la categoría
        foreach ($category_translations as $translation) {
            if (isset($translation['category_id']) && $translation['category_id'] == $category['category_id'] &&
                isset($translation['language_id']) && $translation['language_id'] == $idIdioma) {
                $translatedCategoryName = $translation['translated_category_name'] ?? $originalCategoryName;
                break;
            }
        }

        // Procesar los ítems pertenecientes a la categoría
        $translatedItems = [];
        foreach ($items as $item) {
            if (isset($item['category_id']) && $item['category_id'] == $category['category_id'] &&
                (!isset($item['is_visible']) || $item['is_visible'] == true)) {

                $originalTitle       = isset($item['title']) ? $item['title'] : '';
                $originalDescription = isset($item['description']) ? $item['description'] : '';
                $translatedTitle       = $originalTitle;
                $translatedDescription = $originalDescription;

                // Buscar traducción para el ítem
                foreach ($item_translations as $translation) {
                    if (isset($translation['item_id']) && $translation['item_id'] == $item['item_id'] &&
                        isset($translation['language_id']) && $translation['language_id'] == $idIdioma) {
                        $translatedTitle       = $translation['translated_title'] ?? $originalTitle;
                        $translatedDescription = $translation['translated_description'] ?? $originalDescription;
                        break;
                    }
                }

                $translatedItems[] = [
                    'item_id'                => $item['item_id'],
                    'translated_title'       => $translatedTitle,
                    'translated_description' => $translatedDescription
                ];
            }
        }

        // Procesar las subcategorías pertenecientes a la categoría
        $translatedSubcategories = [];
        foreach ($subcategories as $subcategory) {
            if (isset($subcategory['category_id']) && $subcategory['category_id'] == $category['category_id']) {
                $originalSubcatName = isset($subcategory['subcategory_name']) ? $subcategory['subcategory_name'] : 'Unnamed';
                $translatedSubcatName = $originalSubcatName;

                // Buscar traducción para la subcategoría
                foreach ($subcategory_translations as $subTranslation) {
                    if (isset($subTranslation['subcategory_id']) && $subTranslation['subcategory_id'] == $subcategory['subcategory_id'] &&
                        isset($subTranslation['language_id']) && $subTranslation['language_id'] == $idIdioma) {
                        $translatedSubcatName = $subTranslation['translated_subcategory_name'] ?? $originalSubcatName;
                        break;
                    }
                }

                $translatedSubcategories[] = [
                    'subcategory_id'              => $subcategory['subcategory_id'],
                    'translated_subcategory_name' => $translatedSubcatName
                ];
            }
        }

        $translatedCategories[] = [
            'category_id'              => $category['category_id'],
            'translated_category_name' => $translatedCategoryName,
            'items'                    => $translatedItems,
            'subcategories'            => $translatedSubcategories
        ];
    }

    return $translatedCategories;
}

// --- Extra: Obtener la bandera del idioma original del restaurante ---

?>