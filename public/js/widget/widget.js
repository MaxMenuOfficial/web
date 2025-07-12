// File: public/js/widget.js
// 🚀 Cacheado 1 AÑO por Cloudflare + navegadores.
(function () {
  try {
    // 🔍 Detectar restaurantId desde la URL del <script src="">
    const scriptUrl = document.currentScript?.src || '';
    const match = scriptUrl.match(/\/js\/widget\/([A-Za-z0-9_-]+)\.js$/);
    const restaurantId = match ? match[1] : null;

    if (!restaurantId) {
      console.error('[MaxMenu] ❌ Falta el restaurantId en widget.js (URL no válida)');
      return;
    }

    // 📦 Inyectar estilos necesarios (solo si no existen)
    ['view-items', 'view-plataformas', 'view-logo', 'view-menu'].forEach(name => {
      const href = `https://menu.maxmenu.com/assets/css/widget/styles/${name}.css`;
      if (!document.querySelector(`link[href="${href}"]`)) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
      }
    });

    // 🎯 Seleccionar el contenedor del widget
    const container = document.getElementById('maxmenu-menuContainer');
    if (!container) {
      console.error('[MaxMenu] ❌ Contenedor #maxmenu-menuContainer no encontrado');
      return;
    }

    // 📥 Cargar el HTML del widget
    fetch(`https://menu.maxmenu.com/widget/${restaurantId}`, { mode: 'cors' })
      .then(res => {
        if (!res.ok) throw new Error(`❌ Error HTTP ${res.status} al cargar el widget`);
        return res.text();
      })
      .then(html => {
        // 🌪 Reemplazar completamente el contenido
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        container.innerHTML = tmp.innerHTML;

        // ⚙️ Reinyectar <script> embebidos
        tmp.querySelectorAll('script').forEach(old => {
          const ns = document.createElement('script');
          Array.from(old.attributes).forEach(a => ns.setAttribute(a.name, a.value));
          ns.textContent = old.textContent;
          container.appendChild(ns); // ✅ Usamos container, no document.body
        });
      })
      .catch(err => {
        console.error('[MaxMenu] ❌ Error cargando el widget:', err);
        container.innerHTML = '<p style="color:white;">[MaxMenu] No se pudo cargar el menú.</p>';
      });

  } catch (err) {
    console.error('[MaxMenu] ⚠️ Error crítico en widget.js:', err);
  }
})();