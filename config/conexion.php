<?php
// File: config/conexion.php

require_once __DIR__ . '/../vendor/autoload.php';

use Google\Cloud\Spanner\SpannerClient;
use Google\Cloud\PubSub\PubSubClient;

final class Conexion
{
    private static ?\Google\Cloud\Spanner\Database $spanner = null;
    private static ?PubSubClient $pubsub = null;

    public static function spanner(): \Google\Cloud\Spanner\Database
    {
        if (self::$spanner === null) {
            error_log('🐞 [DEBUG] Conexion::spanner() inicializando conexión...');

            // 🔐 Obtener la ruta del archivo de credenciales (en orden de prioridad)
            $creds = $_ENV['CLOUD_SPANNER_CREDENTIALS']
                ?? getenv('GOOGLE_APPLICATION_CREDENTIALS')
                ?? '/etc/secrets/spanner/key.json'; // Fallback en Cloud Run

            // ✅ Variables de entorno necesarias
            $projectId  = $_ENV['DB_PROJECT_ID']    ?? '';
            $instanceId = $_ENV['DB_INSTANCE_ID']   ?? '';
            $databaseId = $_ENV['DB_DATABASE_ID']   ?? '';

            // 📛 Validación crítica
            foreach (['DB_PROJECT_ID', 'DB_INSTANCE_ID', 'DB_DATABASE_ID'] as $key) {
                if (empty($_ENV[$key])) {
                    error_log("❌ ENV missing: $key");
                    header("Location: /error?message=missing_env_$key");
                    exit;
                }
            }

            // ❌ Verificación de credenciales
            if (!file_exists($creds)) {
                error_log("❌ [ERROR] Spanner credentials not found at {$creds}");
                throw new \RuntimeException("Spanner credentials not found at: {$creds}");
            }

            error_log("🔑 Usando credenciales de Spanner desde: {$creds}");

            // 🔗 Inicializar Spanner client
            $client = new SpannerClient([
                'projectId'   => $projectId,
                'keyFilePath' => $creds,
            ]);

            self::$spanner = $client->connect($instanceId, $databaseId);

            error_log('✅ [DEBUG] Conexión a Spanner establecida (singleton)');
        }

        return self::$spanner;
    }

    public static function pubsub(): PubSubClient
    {
        if (self::$pubsub === null) {
            error_log('🐞 [DEBUG] Conexion::pubsub() inicializando conexión...');

            $creds = $_ENV['CLOUD_PUBSUB_CREDENTIALS']
                ?? getenv('GOOGLE_APPLICATION_CREDENTIALS')
                ?? '/etc/secrets/pubsub/key.json'; // Por si usas secretos separados

            $projectId = $_ENV['PUBSUB_PROJECT_ID'] ?? ($_ENV['DB_PROJECT_ID'] ?? '');

            if (!file_exists($creds)) {
                error_log("❌ [ERROR] Pub/Sub credentials not found at {$creds}");
                throw new \RuntimeException("Pub/Sub credentials not found at: {$creds}");
            }

            if (!$projectId) {
                error_log('❌ [ERROR] PUBSUB_PROJECT_ID or DB_PROJECT_ID not defined');
                throw new \RuntimeException('Pub/Sub projectId missing');
            }

            error_log("🔑 Usando credenciales de Pub/Sub desde: {$creds}");

            self::$pubsub = new PubSubClient([
                'projectId'   => $projectId,
                'keyFilePath' => $creds,
            ]);

            error_log('✅ [DEBUG] Conexión a Pub/Sub establecida (singleton)');
        }

        return self::$pubsub;
    }
}
