<?php
// File: utils/cloudflare-utils.php

function purgeCloudflareCacheForRestaurant(string $restaurantId, int $version): void {
    $zoneId   = getenv('CLOUDFLARE_ZONE_ID');
    $apiToken = getenv('CLOUDFLARE_API_TOKEN');

    if (!$zoneId || !$apiToken) {
        error_log("❌ Cloudflare purge skipped: missing env vars.");
        return;
    }

    // Payload para purga total
    $payload = json_encode(['purge_everything' => true]);

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
        error_log("❌ Cloudflare full purge failed ($status): $error | response: $response");
    } else {
        error_log("✅ Cloudflare full purge success.");
    }
}