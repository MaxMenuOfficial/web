<?php

/**
 * bootstrap.php
 * Carga configuración, entorno, constantes globales, y conecta a Spanner/PubSub.
 */

// -------------------------------------------------------------------------
// Evitar ejecución múltiple del bootstrap
// -------------------------------------------------------------------------
if (defined('BOOTSTRAP_LOADED')) {
    return;
}
define('BOOTSTRAP_LOADED', true);

// -------------------------------------------------------------------------
// Definir el directorio raíz del proyecto
// -------------------------------------------------------------------------
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', realpath(__DIR__ . '/..'));
}

// -------------------------------------------------------------------------
// Cargar Composer Autoload
// -------------------------------------------------------------------------
require_once PROJECT_ROOT . '/vendor/autoload.php';

// -------------------------------------------------------------------------
// Cargar variables de entorno SOLO si existe .env (local)
// -------------------------------------------------------------------------
if (getenv('APP_ENV') === 'development' && file_exists(PROJECT_ROOT . '/.env')) {
    // Solo en desarrollo se carga .env
    $dotenv = Dotenv\Dotenv::createImmutable(PROJECT_ROOT);
    $dotenv->safeLoad();
}

// -------------------------------------------------------------------------
// Configuración de Entorno
// -------------------------------------------------------------------------
$appEnv = $_ENV['APP_ENV'] ?? 'production';
$appEnvPublic = $_ENV['APP_ENV_PUBLIC'] ?? 'production';

// URLs y Paths base
define('BASE_URL', $appEnv === 'production' ? ($_ENV['PROD_BASE_URL'] ?? '') : ($_ENV['BASE_URL'] ?? ''));
define('BASE_PATH', $appEnv === 'production' ? ($_ENV['PROD_BASE_PATH'] ?? '') : ($_ENV['BASE_PATH'] ?? ''));

define('BASE_URL_PUBLIC', $appEnvPublic === 'production' ? ($_ENV['PROD_BASE_URL_PUBLIC'] ?? '') : ($_ENV['BASE_URL_PUBLIC'] ?? ''));
define('BASE_PATH_PUBLIC', $appEnvPublic === 'production' ? ($_ENV['PROD_BASE_PATH_PUBLIC'] ?? '') : ($_ENV['BASE_PATH_PUBLIC'] ?? ''));

// Stripe Keys
define('STRIPE_API_KEY', $appEnv === 'production' ? ($_ENV['STRIPE_API_KEY_LIVE'] ?? '') : ($_ENV['STRIPE_API_KEY_TEST'] ?? ''));
define('STRIPE_API_KEY_PUBLIC', $appEnv === 'production' ? ($_ENV['STRIPE_API_KEY_PUBLIC_LIVE'] ?? '') : ($_ENV['STRIPE_API_KEY_PUBLIC_TEST'] ?? ''));
define('STRIPE_WEBHOOK_SECRET', $appEnv === 'production' ? ($_ENV['STRIPE_WEBHOOK_SECRET_PROD'] ?? '') : ($_ENV['STRIPE_WEBHOOK_SECRET_DEV'] ?? ''));

// -------------------------------------------------------------------------
// Mostrar errores solo en desarrollo
// -------------------------------------------------------------------------
if ($appEnv === 'development' || $appEnvPublic === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// -------------------------------------------------------------------------
// Cargar conexión a Spanner y Pub/Sub (o solo a Spanner si has migrado)
// -------------------------------------------------------------------------
require_once PROJECT_ROOT . '/config/conexion.php';