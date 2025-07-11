<?php
// File: utils/cloudflare-utils.php
function purgeCloudflareCacheForRestaurant(string $restaurantId): void
{
    $zoneId    = getenv('CLOUDFLARE_ZONE_ID');
    $apiToken  = getenv('CLOUDFLARE_API_TOKEN');
    $baseUrl   = rtrim(getenv('CLOUDFLARE_MENU_DOMAIN'), '/');

    if (!$zoneId || !$apiToken || !$restaurantId || !$baseUrl) {
        throw new RuntimeException("❌ Faltan datos esenciales para purgar caché de Cloudflare.");
    }

    $endpoint = "https://api.cloudflare.com/client/v4/zones/{$zoneId}/purge_cache";

    $prefixes = [
        "{$baseUrl}/widget/{$restaurantId}",
        "{$baseUrl}/{$restaurantId}"
    ];

    $payload = json_encode([
        'prefixes' => $prefixes
    ]);

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer {$apiToken}",
            "Content-Type: application/json"
        ],
        CURLOPT_TIMEOUT        => 8
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("❌ Cloudflare purge failed — HTTP $httpCode — $curlErr — Payload: $payload — Resp: $response");
        throw new RuntimeException("Falló la purga de Cloudflare para restaurante {$restaurantId}");
    }

    $data = json_decode($response, true);
    if (!isset($data['success']) || $data['success'] !== true) {
        error_log("⚠️ Cloudflare purge no fue exitosa: " . $response);
        throw new RuntimeException("Cloudflare respondió con error al purgar el caché del restaurante {$restaurantId}");
    }

    error_log("✅ Cloudflare purgado correctamente para: " . implode(', ', $prefixes));
}