// File: public/js/widget.js
// 🚀 ESTE archivo se cachea 1 AÑO: Cloudflare + navegadores.
(function(){
  try {
    // 🔍 Detectar ID desde la URL del propio <script>
    const scriptUrl = document.currentScript?.src || '';
    const match = scriptUrl.match(/\/widget\/([A-Za-z0-9_-]+)\.js$/);
    const restaurantId = match ? match[1] : null;

    if (!restaurantId) {
      console.error('[MaxMenu] ❌ Falta el restaurantId en widget.js (URL no válida)');
      return;
    }

    // 📦 Cargar CSS fijos
    ['view-items','view-plataformas','view-logo','view-menu'].forEach(name => {
      const href = `https://menu.maxmenu.com/assets/css/widget/styles/${name}.css`;
      if (!document.querySelector(`link[href="${href}"]`)) {
        const link = document.createElement('link');
        link.rel  = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
      }
    });

    // 📥 Fetch y render del HTML dinámico
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
        container.innerHTML = html;

        // ⚙️ Volver a ejecutar scripts inline embebidos en el HTML cargado
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        tmp.querySelectorAll('script').forEach(old => {
          const ns = document.createElement('script');
          Array.from(old.attributes).forEach(a => ns.setAttribute(a.name, a.value));
          ns.textContent = old.textContent;
          document.body.appendChild(ns);
        });
      })
      .catch(err => {
        console.error('[MaxMenu] ❌ Error cargando el widget:', err);
        container.innerHTML = '<p>[MaxMenu] No se pudo cargar el menú.</p>';
      });
  } catch (err) {
    console.error('[MaxMenu] ⚠️ Error crítico en widget.js:', err);
  }
})();