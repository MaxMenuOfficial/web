<?php
// /utils/cloudflare-utils.php

/**
 * Purga la caché de Cloudflare para un restaurante dado usando su menu_version.
 *
 * @param string $restaurantId
 * @param int $version
 * @throws RuntimeException si falta alguna variable crítica o falla el request
 */

function purgeCloudflareCacheForRestaurant(string $restaurantId, int $version): void {
    $zoneId     = getenv('CLOUDFLARE_ZONE_ID');
    $apiToken   = getenv('CLOUDFLARE_API_TOKEN');
    $baseDomain = rtrim(getenv('CLOUDFLARE_MENU_DOMAIN'), '/');

    // 🔐 Validar variables de entorno
    if (!$zoneId || !$apiToken || !$baseDomain) {
        throw new RuntimeException("❌ Variables de entorno de Cloudflare no definidas correctamente.");
    }

    // 🔄 Construcción de URLs a purgar
    $files = [
        "$baseDomain/$restaurantId",                            // Página tipo Instagram
        "$baseDomain/menu-widget/$restaurantId?v=$version",     // Widget embebido con versión
    ];

    $payload = json_encode(['files' => $files]);

    // 🛰️ Enviar purga a Cloudflare
    $ch = curl_init("https://api.cloudflare.com/client/v4/zones/$zoneId/purge_cache");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer $apiToken",
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error || $status !== 200) {
        error_log("❌ Cloudflare purge failed for $restaurantId (HTTP $status): $error | response: $response");
        throw new RuntimeException("Cloudflare purge failed: $status");
    }

    error_log("✅ Cloudflare purge success for $restaurantId — version $version — files purged: " . implode(', ', $files));
}