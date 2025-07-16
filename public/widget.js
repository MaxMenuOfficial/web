// 📁 public/widget.js
(async () => {
  // 1️⃣ Obtener restaurantId
  const restaurantId = window.MaxMenuRestaurantID;

  if (!restaurantId) {
    console.error('[MaxMenu] ❌ MaxMenu RestaurantID no definido. Asegúrate de incluir: window.MaxMenuRestaurantID = "..." antes del script.');
    return;
  }

  // 2️⃣ Ruta al archivo latest.json en Google Cloud Storage
  const latestUrl = `https://storage.googleapis.com/maxmenu-storage/${restaurantId}/widget/latest.json`;

  try {
    // 3️⃣ Obtener la versión sin cache
    const res = await fetch(latestUrl, { cache: 'no-store' });
    if (!res.ok) throw new Error(`Error HTTP ${res.status} al cargar latest.json`);

    const data = await res.json();
    const version = data.version;

    if (!version) throw new Error('latest.json no contiene campo "version" válido');

    // 4️⃣ Ruta al widget versionado
    const widgetUrl = `https://storage.googleapis.com/maxmenu-storage/${restaurantId}/widget/${version}/widget.js`;

    // 5️⃣ Inyectar el widget script
    const script = document.createElement('script');
    script.src = widgetUrl;
    script.async = true;
    document.head.appendChild(script);

    console.log(`[MaxMenu] ✅ Widget versión ${version} cargado para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando el widget:', err);
    const fallback = document.getElementById('maxmenu-menuContainer');
    if (fallback) {
      fallback.innerHTML = '<p style="color:red;">[MaxMenu] No se pudo cargar el menú. Intenta más tarde.</p>';
    }
  }
})();