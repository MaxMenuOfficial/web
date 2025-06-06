<?php
function purgeCloudflareCacheForRestaurant(string $rid): void
{
    $zoneId   = getenv('CLOUDFLARE_ZONE_ID');
    $apiToken = getenv('CLOUDFLARE_API_TOKEN');
    $domain   = rtrim(getenv('CLOUDFLARE_MENU_DOMAIN'), '/');

    if (!$zoneId || !$apiToken || !$domain) {
        error_log("⚠️ Cloudflare purge skipped: env vars missing");
        return;
    }

    /* 1) Obtener la versión actual directamente de BD */
    try {
        $svc  = new MenuService();
        $data = $svc->getRestaurantPublicData($rid, true);  // force BD
        $v    = (int)($data['menu_version'] ?? time());
    } catch (Throwable $e) {
        $v = time();  // fallback paranoico
    }

    /* 2) Rutas a purgar */
    $files = [
        "$domain/$rid",
        "$domain/menu-widget/$rid",
        "$domain/menu-widget/$rid?v=$v",
    ];

    /* 3) Llamada a la API */
    $payload = json_encode(['files' => $files]);

    $ch = curl_init("https://api.cloudflare.com/client/v4/zones/$zoneId/purge_cache");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer $apiToken",
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 10,
    ]);

    $resp   = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error  = curl_error($ch);
    curl_close($ch);

    if ($status === 200) {
        error_log("✅ Cloudflare purged: ".implode(', ', $files));
    } else {
        error_log("❌ Cloudflare purge failed ($status): $error — $resp");
    }
}