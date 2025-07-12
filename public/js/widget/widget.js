// File: public/js/widget.js
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

    // 📦 Inyectar estilos si no están presentes
    ['view-items', 'view-plataformas', 'view-logo', 'view-menu'].forEach(name => {
      const href = `https://menu.maxmenu.com/assets/css/widget/styles/${name}.css`;
      if (!document.querySelector(`link[href="${href}"]`)) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
      }
    });

    // 🎯 Eliminar y reemplazar por un nuevo contenedor
    const oldContainer = document.getElementById('maxmenu-menuContainer');
    if (!oldContainer || !oldContainer.parentNode) {
      console.error('[MaxMenu] ❌ Contenedor #maxmenu-menuContainer no encontrado o sin padre');
      return;
    }

    // 💣 Destruir el contenedor antiguo del DOM
    oldContainer.remove();

    // 🧬 Insertar uno nuevo limpio
    const newContainer = document.createElement('div');
    newContainer.id = 'maxmenu-menuContainer';
    document.body.appendChild(newContainer); // o el mismo parent si lo prefieres

    // 📥 Cargar HTML del widget
    fetch(`https://menu.maxmenu.com/widget/${restaurantId}`, { mode: 'cors' })
      .then(res => {
        if (!res.ok) throw new Error(`❌ Error HTTP ${res.status} al cargar el widget`);
        return res.text();
      })
      .then(html => {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;

        // 🧱 Insertar HTML renderizado
        newContainer.innerHTML = tmp.innerHTML;

        // ⚙️ Ejecutar todos los <script> embebidos de forma viva
        tmp.querySelectorAll('script').forEach(oldScript => {
          const s = document.createElement('script');
          [...oldScript.attributes].forEach(attr => s.setAttribute(attr.name, attr.value));
          s.textContent = oldScript.textContent;
          newContainer.appendChild(s);
        });

        console.log('[MaxMenu] ✅ Widget cargado y ejecutado correctamente');
      })
      .catch(err => {
        console.error('[MaxMenu] ❌ Error cargando el widget:', err);
        newContainer.innerHTML = '<p style="color:white;">[MaxMenu] No se pudo cargar el menú.</p>';
      });

  } catch (err) {
    console.error('[MaxMenu] ⚠️ Error crítico en widget.js:', err);
  }
})();