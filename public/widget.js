(async () => {
  const container = document.getElementById('maxmenu-menuContainer');
  const restaurantId = container?.dataset?.restaurantId;

  if (!restaurantId) {
    console.error('[MaxMenu] ❌ Falta data-restaurant-id en #maxmenu-menuContainer');
    return;
  }

  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const fallbackVersion = '__VERSION__'; // Puedes reemplazarlo por build-time si quieres
  let currentVersion = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  // 🔹 Paso 1: Intenta cargar version.json cacheado (rápido y gratis desde Cloudflare)
  try {
    const versionJsonURL = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`;
    const vRes = await fetch(versionJsonURL, { cache: 'force-cache' });
    if (vRes.ok) {
      const data = await vRes.json();
      if (data.version) {
        currentVersion = data.version;
      }
    }
  } catch (e) {
    console.warn('[MaxMenu] ⚠️ Error al obtener version.json cacheado:', e);
  }

  // 🔹 Paso 2: Disparo en paralelo para latest.json → comparar versiones
  (async () => {
    try {
      const latestUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`;
      const res = await fetch(latestUrl, { cache: 'no-store' });
      const { version: latestVersion } = await res.json();

      if (latestVersion && latestVersion !== currentVersion) {
        console.log(`[MaxMenu] 🔁 Nueva versión detectada: ${latestVersion}`);
        localStorage.setItem(KEY_STORAGE_VERSION, latestVersion);
        location.reload(); // Recarga para reinyectar nuevo widget
      }
    } catch (err) {
      console.warn('[MaxMenu] ⚠️ Error al verificar latest.json:', err);
    }
  })();

  // 🔹 Paso 3: Limpieza visual y DOM
  container.innerHTML = '';
  document.querySelectorAll('script[maxmenu-script]').forEach(s => s.remove());
  document.querySelectorAll('link[maxmenu-style]').forEach(l => l.remove());

  // 🔹 Paso 4: Cargar el widget.js desde la versión detectada
  try {
    const scriptURL = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/widget.js`;
    const script = document.createElement('script');
    script.src = scriptURL;
    script.async = false;
    script.setAttribute("maxmenu-script", "true");
    document.head.appendChild(script);

    console.log(`[MaxMenu] ✅ Cargado widget.js v${currentVersion} para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando el widget.js:', err);
    container.innerHTML = '<p style="color:red;">[MaxMenu] No se pudo cargar el menú.</p>';
  }
})();