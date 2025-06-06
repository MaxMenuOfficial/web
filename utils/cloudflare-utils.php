<?php

function purgeCloudflareCacheForRestaurant(string $restaurantId, int $menuVersion): void {
    $zoneId     = getenv('CLOUDFLARE_ZONE_ID');
    $apiToken   = getenv('CLOUDFLARE_API_TOKEN');
    $baseDomain = rtrim(getenv('CLOUDFLARE_MENU_DOMAIN'), '/');

    if (!$zoneId || !$apiToken || !$baseDomain) {
        error_log("❌ Cloudflare purge skipped: missing env vars.");
        return;
    }

    $files = [
        "$baseDomain/{$restaurantId}",
        "$baseDomain/menu-widget/{$restaurantId}",
        "$baseDomain/menu-widget/{$restaurantId}?v={$menuVersion}",
        "$baseDomain/api/menu-version.php?id={$restaurantId}",
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
        error_log("❌ Cloudflare purge failed ($status): $error");
    } else {
        error_log("✅ Cloudflare purge success — URLs purged: " . implode(', ', $files));
    }
}