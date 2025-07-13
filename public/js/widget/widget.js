(function () {
  try {
    // 🔍 Detectar restaurantId desde <script src=".../widget/maxmenu.js">
    const scriptUrl = document.currentScript?.src || '';
    const match = scriptUrl.match(/\/js\/widget\/([A-Za-z0-9_-]+)\.js$/);
    const restaurantId = match ? match[1] : null;

    if (!restaurantId) {
      console.error('[MaxMenu] ❌ Falta el restaurantId en widget.js (URL no válida)');
      return;
    }

    // 🎯 Buscar contenedor existente
    const oldContainer = document.getElementById('maxmenu-menuContainer');
    if (!oldContainer || !oldContainer.parentNode) {
      console.error('[MaxMenu] ❌ Contenedor #maxmenu-menuContainer no encontrado o sin padre');
      return;
    }

    // 💣 Eliminar contenedor viejo
    oldContainer.remove();
    // 🔥 Eliminar todo el contenido del contenedor antes de removerlo (por seguridad extrema)
    oldContainer.innerHTML = '';

    // 🧬 Crear contenedor limpio
    const newContainer = document.createElement('div');
    newContainer.id = 'maxmenu-menuContainer';
    document.body.appendChild(newContainer);

    // 📥 Insertar solo el HTML del widget
    fetch(`https://menu.maxmenu.com/widget/${restaurantId}`, { mode: 'cors' })
      .then(res => {
        if (!res.ok) throw new Error(`❌ Error HTTP ${res.status} al cargar el widget`);
        return res.text();
      })
      .then(html => {
        newContainer.innerHTML = html;
        console.log('[MaxMenu] ✅ Widget cargado sin estilos ni scripts');
      })
      .catch(err => {
        console.error('[MaxMenu] ❌ Error cargando el widget:', err);
        newContainer.innerHTML = '<p>[MaxMenu] No se pudo cargar el menú.</p>';
      });

  } catch (err) {
    console.error('[MaxMenu] ⚠️ Error crítico en widget.js:', err);
  }
})();