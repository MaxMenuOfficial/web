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
        location.reload();
      } else {
        console.log('[MaxMenu] ✅ Versión actual es la más reciente.');
      }
    } catch (err) {
      console.warn('[MaxMenu] ⚠️ Error al verificar latest.json:', err);
    }
  })();

  container.innerHTML = '';
  document.querySelectorAll('script[maxmenu-script]').forEach(s => s.remove());
  document.querySelectorAll('link[maxmenu-style]').forEach(l => l.remove());

  try {
    const widgetUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/widget.js`;
    const script = document.createElement('script');
    script.src = widgetUrl;
    script.async = false;
    script.setAttribute('maxmenu-script', 'true');
    document.head.appendChild(script);

    console.log(`[MaxMenu] ✅ widget.js v${currentVersion} inyectado para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] loading Error the widget.js:', err);
    container.innerHTML = '<p width:100%;text-aling:center; style="color:red;">[MaxMenu] Error loading the menu.</p>';
  }
})();

