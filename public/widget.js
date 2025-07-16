(async () => {
  const restaurantId = window.MaxMenuRestaurantID;

  if (!restaurantId) {
    console.error('[MaxMenu] ❌ MaxMenu RestaurantID no definido. Define: window.MaxMenuRestaurantID = "..." antes del script.');
    return;
  }

  // 🔥 1️⃣ ELIMINAR CONTENIDO PREVIO DEL DOM
  const prevContainer = document.getElementById('maxmenu-menuContainer');
  if (prevContainer) prevContainer.remove();

  document.querySelectorAll('script[maxmenu-script]').forEach(el => el.remove());
  document.querySelectorAll('link[maxmenu-style]').forEach(el => el.remove());

  // 🧱 2️⃣ CREAR NUEVO CONTENEDOR VACÍO
  const container = document.createElement('div');
  container.id = 'maxmenu-menuContainer';
  document.body.appendChild(container);

  // 🧭 3️⃣ CARGAR latest.json (sin caché)
  const latestUrl = `https://storage.googleapis.com/maxmenu-storage/${restaurantId}/widget/latest.json?_=${Date.now()}`;

  try {
    const res = await fetch(latestUrl, { cache: 'no-store' });
    if (!res.ok) throw new Error(`HTTP ${res.status} al cargar latest.json`);

    const { version } = await res.json();
    if (!version) throw new Error('Campo "version" no válido');

    // 🚀 4️⃣ CARGAR widget.js VERSIONADO
    const widgetUrl = `https://storage.googleapis.com/maxmenu-storage/${restaurantId}/widget/${version}/widget.js`;

    const script = document.createElement('script');
    script.src = widgetUrl + '?_=' + Date.now(); // ⏱️ evitar caché de navegador
    script.async = false;
    document.head.appendChild(script);

    console.log(`[MaxMenu] ✅ Cargado widget.js v${version} para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando el widget:', err);
    const fallback = document.getElementById('maxmenu-menuContainer');
    if (fallback) {
      fallback.innerHTML = '<p style="color:red;">[MaxMenu] No se pudo cargar el menú.</p>';
    }
  }
})();