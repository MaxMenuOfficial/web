// 📦 MaxMenu Loader — carga dinámicamente el widget desde CDN
(async () => {
  const container = document.getElementById('maxmenu-menuContainer');
  const restaurantId = container?.dataset?.restaurantId;

  if (!restaurantId) {
    console.error('[MaxMenu] ❌ data-restaurant-id no definido en #maxmenu-menuContainer');
    return;
  }

  // 1️⃣ Limpieza previa (contenedor + scripts/estilos antiguos)
  container.innerHTML = '';
  document.querySelectorAll('script[maxmenu-script]').forEach(el => el.remove());
  document.querySelectorAll('link[maxmenu-style]').forEach(el => el.remove());

  // 2️⃣ Construcción de la URL (SIEMPRE vía CDN)
  const latestUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`;

  try {
    // 3️⃣ Cargar latest.json (no-cache)
    const res = await fetch(latestUrl, { cache: 'no-store' });
    if (!res.ok) throw new Error(`HTTP ${res.status} al cargar latest.json`);

    const { version } = await res.json();
    if (!version) throw new Error('Campo "version" no válido en latest.json');

    // 4️⃣ Construcción de la URL al widget.js versionado
    const widgetUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;

    // 5️⃣ Inyectar el script del widget
    const script = document.createElement('script');
    script.src = widgetUrl;
    script.async = false;
    script.setAttribute('maxmenu-script', 'true'); // para limpieza en recargas
    document.head.appendChild(script);

    console.log(`[MaxMenu] ✅ Cargado widget.js v${version} para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando el widget:', err);
    container.innerHTML = '<p style="color:red;">[MaxMenu] No se pudo cargar el menú.</p>';
  }
})();

