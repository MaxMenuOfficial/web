<?php
function purgeCloudflareCacheForRestaurant(string $restaurantId): void {
    $zoneId     = getenv('CLOUDFLARE_ZONE_ID');
    $apiToken   = getenv('CLOUDFLARE_API_TOKEN');
    $baseDomain = rtrim(getenv('CLOUDFLARE_MENU_DOMAIN'), '/'); // ejemplo: menu.maxmenu.com

    if (!$zoneId || !$apiToken || !$baseDomain) {
        error_log("❌ Cloudflare purge skipped: missing env vars.");
        return;
    }

    $urls = [
        "https://{$baseDomain}/{$restaurantId}",                                        // URL amigable (menu.php?id=X)
        "https://{$baseDomain}/menu.php?id={$restaurantId}",                            // URL real del menú
        "https://{$baseDomain}/menu-widget?id={$restaurantId}",                         // Widget sin versión
        "https://{$baseDomain}/menu-widget.php?id={$restaurantId}",                     // Widget archivo real
        "https://{$baseDomain}/api/menu-version?id={$restaurantId}",                    // API de versión
    ];

    $payload = json_encode(['files' => $urls]);

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
        if ($response) {
            error_log("🔁 Response: $response");
        }
    } else {
        error_log("✅ Cloudflare purge success for:\n" . implode("\n", $urls));
    }

    if (php_sapi_name() === 'cli' || $_ENV['APP_ENV'] === 'development') {
        echo "🧹 Purged URLs:\n" . implode("\n", $urls) . "\n";
    }
}