<?php
// File: backend/php/conexion.php

// Carga de Composer y variables de entorno
require_once __DIR__ . '/../vendor/autoload.php';


use Google\Cloud\Spanner\SpannerClient;
use Google\Cloud\PubSub\PubSubClient;

/**
 * Clase singleton para gestionar conexiones a Spanner y Pub/Sub
 * Debug mode: muestra logs detallados de inicialización y reutilización.
 */
final class Conexion
{
    private static ?\Google\Cloud\Spanner\Database $spanner = null;
    private static ?PubSubClient $pubsub = null;

    /**
     * Obtener instancia de Spanner (singleton)
     * @return \Google\Cloud\Spanner\Database
     * @throws \RuntimeException
     */
    public static function spanner(): \Google\Cloud\Spanner\Database
    {
        if (self::$spanner === null) {
            error_log('🐞 [DEBUG] Conexion::spanner() inicializando conexión...');

            // Credenciales y configuración
            $creds      = $_ENV['CLOUD_SPANNER_CREDENTIALS'] ?? '';
            $projectId  = $_ENV['DB_PROJECT_ID']        ?? '';
            $instanceId = $_ENV['DB_INSTANCE_ID']       ?? '';
            $databaseId = $_ENV['DB_DATABASE_ID']       ?? '';


            foreach (['CLOUD_SPANNER_CREDENTIALS', 'DB_PROJECT_ID', 'DB_INSTANCE_ID', 'DB_DATABASE_ID'] as $key) {
                if (empty($_ENV[$key])) {
                    error_log("❌ ENV missing: $key");
                    header("Location: /error?message=missing_env_$key");
                    exit;
                }
            }
            

            if (!$creds || !file_exists($creds)) {
                error_log("❌ [ERROR] Spanner credentials not found at {$creds}");
                throw new \RuntimeException("Spanner credentials not found at {$creds}");
            }
            if (!$projectId || !$instanceId || !$databaseId) {
                error_log('❌ [ERROR] Spanner configuration missing (DB_PROJECT_ID, DB_INSTANCE_ID, DB_DATABASE_ID)');
                throw new \RuntimeException('Spanner configuration incomplete');
            }

            // Inicializar cliente
            $client = new SpannerClient([
                'projectId'   => $projectId,
                'keyFilePath' => $creds,
            ]);
            self::$spanner = $client->connect($instanceId, $databaseId);

            error_log('✅ [DEBUG] Conexión a Spanner establecida (singleton)');
        } else {
            error_log('🐞 [DEBUG] Conexion::spanner() reutilizando conexión existente');
        }

        return self::$spanner;
    }

    

    /**
     * Obtener instancia de Pub/Sub (singleton)
     * @return PubSubClient
     * @throws \RuntimeException
     */
    public static function pubsub(): PubSubClient
    {
        if (self::$pubsub === null) {
            error_log('🐞 [DEBUG] Conexion::pubsub() inicializando conexión...');

            // Credenciales y configuración
            $creds     = $_ENV['CLOUD_PUBSUB_CREDENTIALS'] ?? '';
            $projectId = $_ENV['PUBSUB_PROJECT_ID']    ?? ($_ENV['DB_PROJECT_ID'] ?? '');

            if (!$creds || !file_exists($creds)) {
                error_log("❌ [ERROR] Pub/Sub credentials not found at {$creds}");
                throw new \RuntimeException("Pub/Sub credentials not found at {$creds}");
            }
            if (!$projectId) {
                error_log('❌ [ERROR] PUBSUB_PROJECT_ID or DB_PROJECT_ID not defined');
                throw new \RuntimeException('Pub/Sub projectId missing');
            }

            self::$pubsub = new PubSubClient([
                'projectId'   => $projectId,
                'keyFilePath' => $creds,
            ]);

            error_log('✅ [DEBUG] Conexión a Pub/Sub establecida (singleton)');
        } else {
            error_log('🐞 [DEBUG] Conexion::pubsub() reutilizando conexión existente');
        }

        return self::$pubsub;
    }
    
}
