<?php
// File: utils/cloudflare-utils.php

function purgeCloudflareCacheForRestaurant(string $restaurantId): void {
    $zoneId   = getenv('CLOUDFLARE_ZONE_ID');
    $apiToken = getenv('CLOUDFLARE_API_TOKEN');

    if (!$zoneId || !$apiToken) {
        error_log("❌ Cloudflare purge skipped: missing env vars.");
        return;
    }

    // Construir las URLs a purgar para ese restaurante
    $baseUrl = 'https://menu.maxmenu.com';
    $urls = [
        "$baseUrl/menu-widget.php?id=$restaurantId",
        "$baseUrl/$restaurantId",
    ];

    // Construir payload
    $payload = json_encode(['files' => $urls]);

    // Ejecutar petición a Cloudflare
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

    // Logging preciso y legible
    if ($error || $status !== 200) {
        error_log("❌ Cloudflare purge FAILED [$status] for $restaurantId | $error | $response");
    } else {
        error_log("✅ Cloudflare purge OK [$status] for $restaurantId → " . implode(', ', $urls));
    }
}