(async () => {
  const container = document.getElementById('maxmenu-menuContainer');
  const restaurantId = container?.dataset?.restaurantId;

  if (!restaurantId) {
    console.error('[MaxMenu] ❌ data-restaurant-id no definido.');
    return;
  }

  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const fallbackVersion = '__VERSION__';
  let currentVersion = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  // 1️⃣ Obtener versión cacheada desde Cloudflare (version.json)
  try {
    const versionJsonURL = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`;
    const versionRes = await fetch(versionJsonURL, { cache: 'force-cache' });

    if (versionRes.ok) {
      const versionData = await versionRes.json();
      if (versionData.version) {
        currentVersion = versionData.version;
        console.log(`[MaxMenu] 📦 Versión detectada: ${currentVersion}`);
      } else {
        console.warn('[MaxMenu] ⚠️ version.json sin campo "version" válido.');
      }
    } else {
      console.warn(`[MaxMenu] ⚠️ No se pudo obtener version.json (${versionRes.status}).`);
    }
  } catch (err) {
    console.warn('[MaxMenu] ⚠️ Error al obtener version.json cacheado:', err);
  }

  // 2️⃣ Inyectar inmediatamente el widget.js (EDGE)
  try {
    const widgetUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/widget.js`;

    // Limpieza de scripts previos
    container.innerHTML = '';
    document.querySelectorAll('script[maxmenu-script]').forEach(s => s.remove());
    document.querySelectorAll('link[maxmenu-style]').forEach(l => l.remove());

    const script = document.createElement('script');
    script.src = widgetUrl;
    script.async = false;
    script.setAttribute('maxmenu-script', 'true');
    document.head.appendChild(script);

    console.log(`[MaxMenu] 🚀 widget.js v${currentVersion} inyectado`);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando el widget.js:', err);
    container.innerHTML = '<p style="color:red;">[MaxMenu] Error al cargar el menú.</p>';
  }

  // 3️⃣ Validación contra latest.json (en segundo plano)
  setTimeout(async () => {
    try {
      const latestUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`;
      const latestRes = await fetch(latestUrl, { cache: 'no-store' });

      if (!latestRes.ok) {
        console.warn(`[MaxMenu] ⚠️ latest.json no disponible (${latestRes.status})`);
        return;
      }

      const { version: latestVersion } = await latestRes.json();

      if (!latestVersion) {
        console.warn('[MaxMenu] ⚠️ latest.json sin campo "version" válido.');
        return;
      }

      if (latestVersion !== currentVersion) {
        console.log(`[MaxMenu] 🔁 Nueva versión detectada: ${currentVersion} → ${latestVersion}`);
        localStorage.setItem(KEY_STORAGE_VERSION, latestVersion);
        location.reload();
      } else {
        console.log('[MaxMenu] ✅ Ya estás usando la versión más reciente.');
      }
    } catch (err) {
      console.warn('[MaxMenu] ⚠️ Error al verificar latest.json:', err);
    }
  }, 1000); // ⏱ 1 segundo para evitar impacto en percepción inicial
})();