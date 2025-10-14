(async () => {
  const container = document.getElementById('maxmenu-menuContainer');
  const restaurantId = container?.dataset?.restaurantId;

  if (!restaurantId) {
    console.error('[MaxMenu] ❌ data-restaurant-id no definido.');
    return;
  }
  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const fallbackVersion = '__VERSION__'; // 🔧 Reemplazar en build si se desea
  let currentVersion = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  // 1️⃣ Obtener versión cacheada desde Cloudflare (RÁPIDO - EDGE)
  try {
    const versionJsonURL = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`;
    const versionRes = await fetch(versionJsonURL, { cache: 'force-cache' });

    if (versionRes.ok) {
      const versionData = await versionRes.json();
      if (versionData.version) {
        currentVersion = versionData.version;
      } else {
        console.warn('[MaxMenu] ⚠️ version.json sin campo "version" válido.');
      }
    } else {
      console.warn(`[MaxMenu] ⚠️ No se pudo obtener version.json (${versionRes.status}).`);
    }
  } catch (err) {
    console.warn('[MaxMenu] ⚠️ Error al obtener version.json cacheado:', err);
  }

  // 2️⃣ Validación contra latest.json (NO CACHED - siempre nuevo)
  (async () => {
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
        console.log(`[MaxMenu] 🔁 Versión desactualizada detectada: ${currentVersion} → ${latestVersion}`);
        localStorage.setItem(KEY_STORAGE_VERSION, latestVersion);
        location.reload(); // 🚨 Fuerza recarga para tomar los nuevos recursos
      } else {
        console.log('[MaxMenu] ✅ Versión actual es la más reciente.');
      }
    } catch (err) {
      console.warn('[MaxMenu] ⚠️ Error al verificar latest.json:', err);
    }
  })();

  // 3️⃣ Limpieza de scripts y estilos previos (si los hubiera)
  container.innerHTML = '';
  document.querySelectorAll('script[maxmenu-script]').forEach(s => s.remove());
  document.querySelectorAll('link[maxmenu-style]').forEach(l => l.remove());

  // 4️⃣ Cargar widget.js desde la versión exacta (EDGE)
  try {
    const widgetUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/widget.js`;
    const script = document.createElement('script');
    script.src = widgetUrl;
    script.async = false;
    script.setAttribute('maxmenu-script', 'true');
    document.head.appendChild(script);

    console.log(`[MaxMenu] ✅ widget.js v${currentVersion} inyectado para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando el widget.js:', err);
    container.innerHTML = '<p style="color:red;">[MaxMenu] Error al cargar el menú.</p>';
  }
})();

