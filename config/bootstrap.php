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
if (file_exists(PROJECT_ROOT . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(PROJECT_ROOT);
    $dotenv->safeLoad();
}
// -------------------------------------------------------------------------
// Configuración de Entorno
// -------------------------------------------------------------------------
// -------------------------------------------------------------------------
// Mostrar errores solo en desarrollo
// -------------------------------------------------------------------------
// Cargar conexión a Spanner y Pub/Sub (o solo a Spanner si has migrado)
// -------------------------------------------------------------------------
require_once PROJECT_ROOT . '/config/conexion.php';
