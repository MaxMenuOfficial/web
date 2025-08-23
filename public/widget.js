(async () => {
  const originalContainer = document.getElementById('maxmenu-menuContainer');
  const restaurantId = originalContainer?.dataset?.restaurantId;

  if (!restaurantId) {
    console.error('[MaxMenu] ❌ data-restaurant-id no definido en #maxmenu-menuContainer');
    return;
  }
  
  // Limpia contenido sin remover el div original
  originalContainer.innerHTML = '';

  // Limpia scripts y estilos anteriores si los hubiera
  document.querySelectorAll('script[maxmenu-script]').forEach(el => el.remove());
  document.querySelectorAll('link[maxmenu-style]').forEach(el => el.remove());

  const newContainer = originalContainer; // Reutiliza el original, no crees uno nuevo



  const latestUrl = `https://storage.googleapis.com/maxmenu-storage/${restaurantId}/widget/latest.json?_=${Date.now()}`;

  try {
    const res = await fetch(latestUrl, { cache: 'no-store' });
    if (!res.ok) throw new Error(`HTTP ${res.status} al cargar latest.json`);

    const { version } = await res.json();
    if (!version) throw new Error('Campo "version" no válido');

    const widgetUrl = `https://storage.googleapis.com/maxmenu-storage/${restaurantId}/widget/${version}/widget.js`;

    const script = document.createElement('script');
    script.src = widgetUrl + '?_=' + Date.now();
    script.async = false;
    document.head.appendChild(script);

    console.log(`[MaxMenu] ✅ Cargado widget.js v${version} para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando el widget:', err);
    newContainer.innerHTML = '<p style="color:red;">[MaxMenu] No se pudo cargar el menú.</p>';
  }
})();