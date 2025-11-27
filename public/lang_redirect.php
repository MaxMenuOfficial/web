<?php
/**
 * lang_redirect.php
 * -----------------
 * Maneja la detección y carga del idioma.
 */

// (Opcional) Mostrar errores en modo desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Idioma por defecto (si no se detecta nada, se usará 'es').
 */
$defaultLang = 'es';

/**
 * Función para detectar el idioma del navegador (sin región).
 * Por ejemplo, "es-ES" => "es".
 */
function detectBrowserLanguage() {
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        return strtolower(substr($languages[0], 0, 2));
    }
    return null;
}
// Tomar el idioma desde:
// 1) $_GET['lang'] (si .htaccess reescribió /en/... a ?lang=en)
// 2) $_COOKIE['user_lang']
// 3) Idioma del navegador
// 4) Idioma por defecto
$lang = $_GET['lang']
    ?? ($_COOKIE['user_lang']
    ?? detectBrowserLanguage()
    ?? $defaultLang);

// Normalizar a minúsculas y quedarnos solo con 2 letras
$lang = strtolower(substr($lang, 0, 2));

/**
 * Lista de idiomas válidos.
 * Puedes agregar o quitar según tus necesidades.
 */
$validLanguages = ['es', 'en', 'it', 'de', 'fr', 'pt'];

// Si el $lang no está en la lista, usar el default
if (!in_array($lang, $validLanguages)) {
    $lang = $defaultLang;
}

/**
 * Si la cookie está vacía o es distinta al idioma actual,
 * la actualizamos (por ejemplo, un año de duración).
 */
if (!isset($_COOKIE['user_lang']) || $_COOKIE['user_lang'] !== $lang) {
    setcookie('user_lang', $lang, time() + (365 * 24 * 60 * 60), '/');
}

/**
 * Cargar traducciones de la carpeta /resources/lang/<lang>/*.php
 */
$langFolderPath = __DIR__ . "/resources/lang/$lang";

if (!is_dir($langFolderPath)) {
    die("Error: No se encontró la carpeta de traducciones para el idioma '$lang'.");
}

// Array donde se combinarán todas las traducciones
$translations = [];

// Recorrer todos los archivos .php dentro de /resources/lang/<lang>/
foreach (scandir($langFolderPath) as $file) {
    if (substr($file, -4) === '.php') {
        $filePath = $langFolderPath . '/' . $file;
        $arrayFromFile = include($filePath); // Cada archivo debe retornar un array
        $translations = array_merge($translations, $arrayFromFile);
    }
}

// $translations queda listo para usar en tu HTML/JS.
