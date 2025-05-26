<?php

function purgeCloudflareCacheForRestaurant(string $restaurantId): void {
    $zoneId     = getenv('CLOUDFLARE_ZONE_ID');
    $apiToken   = getenv('CLOUDFLARE_API_TOKEN');
    $baseDomain = rtrim(getenv('CLOUDFLARE_MENU_DOMAIN'), '/');

    $urls = [
        "$baseDomain/$restaurantId",
        "$baseDomain/menu-widget.php?id=$restaurantId"
    ];

    $data = json_encode(['files' => $urls]);

    $ch = curl_init("https://api.cloudflare.com/client/v4/zones/$zoneId/purge_cache");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => $data,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $apiToken,
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("❌ Error purgando caché para $restaurantId: HTTP $httpCode");
    } else {
        error_log("✅ Cloudflare caché purgada con éxito para $restaurantId (menú + widget)");
    }
}