<?php
// File: utils/cloudflare-utils.php

function purgeCloudflareCacheForRestaurant(string $restaurantId, int $version): void {
    $zoneId   = getenv('CLOUDFLARE_ZONE_ID');
    $apiToken = getenv('CLOUDFLARE_API_TOKEN');
    $base     = rtrim(getenv('CLOUDFLARE_MENU_DOMAIN'), '/');

    if (!$zoneId || !$apiToken || !$base) {
        error_log("❌ Cloudflare purge skipped: missing env vars.");
        return;
    }

    $files = [
        "$base/menu-widget?id={$restaurantId}&v={$version}",
        "$base/menu-widget.php?id={$restaurantId}&v={$version}",
        "$base/$restaurantId",
      ];

    $payload = json_encode(['files' => $files]);

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
        error_log("❌ Cloudflare purge failed ($status): $error | response: $response");
    } else {
        error_log("✅ Cloudflare purge success for: " . implode(', ', $files));
    }
}