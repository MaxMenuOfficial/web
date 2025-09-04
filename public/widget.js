(async () => {
  const originalContainer = document.getElementById('maxmenu-menuContainer');
  const restaurantId = originalContainer?.dataset?.restaurantId;

  if (!restaurantId) {
    console.error('[MaxMenu] ❌ data-restaurant-id no definido en #maxmenu-menuContainer');
    return;
  }

  // Limpieza previa
  originalContainer.innerHTML = '';
  document.querySelectorAll('script[maxmenu-script]').forEach(el => el.remove());
  document.querySelectorAll('link[maxmenu-style]').forEach(el => el.remove());

  const newContainer = originalContainer;

  // 🔹 Ahora pedimos a través de tu dominio protegido
  const latestUrl = `https://menu.maxmenu.com/data/${restaurantId}/widget/latest.json`;

  try {
    const res = await fetch(latestUrl, { cache: 'no-store' });
    if (!res.ok) throw new Error(`HTTP ${res.status} al cargar latest.json`);

    const { version } = await res.json();
    if (!version) throw new Error('Campo "version" no válido');

    const widgetUrl = `https://menu.maxmenu.com/data/${restaurantId}/widget/${version}/widget.js`;

    const script = document.createElement('script');
    script.src = widgetUrl;
    script.async = false;
    script.setAttribute("maxmenu-script", "true");
    document.head.appendChild(script);

    console.log(`[MaxMenu] ✅ Cargado widget.js v${version} para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando el widget:', err);
    newContainer.innerHTML = '<p style="color:red;">[MaxMenu] No se pudo cargar el menú.</p>';
  }
})();