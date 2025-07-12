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

    // 📦 Inyectar estilos si no están ya
    ['view-items', 'view-plataformas', 'view-logo', 'view-menu'].forEach(name => {
      const href = `https://menu.maxmenu.com/assets/css/widget/styles/${name}.css`;
      if (!document.querySelector(`link[href="${href}"]`)) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
      }
    });

    // 🎯 Buscar el contenedor y hacerle hard-reset
    const container = document.getElementById('maxmenu-menuContainer');
    if (!container) {
      console.error('[MaxMenu] ❌ Contenedor #maxmenu-menuContainer no encontrado');
      return;
    }

    fetch(`https://menu.maxmenu.com/widget/${restaurantId}`, { mode: 'cors' })
      .then(res => {
        if (!res.ok) throw new Error(`❌ Error HTTP ${res.status} al cargar el widget`);
        return res.text();
      })
      .then(html => {
        // 🌪 Reemplazo estructural del contenedor (no solo innerHTML)
        const tmp = document.createElement('div');
        tmp.innerHTML = html;

        const newContainer = container.cloneNode(false); // clonado limpio sin hijos
        container.parentNode.replaceChild(newContainer, container); // 🔄 reemplazo real
        newContainer.innerHTML = tmp.innerHTML;

        // ⚙️ Reinyectar todos los <script> embebidos del HTML cargado
        tmp.querySelectorAll('script').forEach(oldScript => {
          const s = document.createElement('script');
          Array.from(oldScript.attributes).forEach(attr => s.setAttribute(attr.name, attr.value));
          s.textContent = oldScript.textContent;
          newContainer.appendChild(s);
        });

        console.log('[MaxMenu] ✅ Widget cargado y ejecutado correctamente');
      })
      .catch(err => {
        console.error('[MaxMenu] ❌ Error cargando el widget:', err);
        container.innerHTML = '<p style="color:white;">[MaxMenu] No se pudo cargar el menú.</p>';
      });

  } catch (err) {
    console.error('[MaxMenu] ⚠️ Error crítico en widget.js:', err);
  }
})();