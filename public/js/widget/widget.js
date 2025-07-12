// File: public/js/widget.js
// 🚀 Este archivo se cachea 1 AÑO: Cloudflare + navegadores

(function(){
  try {
    // 🔍 Detectar ID desde la URL del propio <script>
    const scriptUrl = document.currentScript?.src || '';
    const urlParams = new URL(scriptUrl).searchParams;
    const restaurantId = urlParams.get('id');

    if (!restaurantId) {
      console.error('[MaxMenu] ❌ Falta el parámetro ?id= en widget.js');
      return;
    }

    console.debug(`[MaxMenu] 🔍 Detectado ID del restaurante: ${restaurantId}`);

    // 📦 Cargar CSS fijos si no están en el DOM
    ['view-items','view-plataformas','view-logo','view-menu'].forEach(name => {
      const href = `https://menu.maxmenu.com/assets/css/widget/styles/${name}.css`;
      if (!document.querySelector(`link[href="${href}"]`)) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
      }
    });

    // 🧼 Eliminar contenedor anterior si existe
    const existing = document.getElementById('maxmenu-menuContainer');
    if (existing) {
      existing.remove();
    }

    // 🔁 Crear contenedor limpio
    const container = document.createElement('div');
    container.id = 'maxmenu-menuContainer';
    document.body.appendChild(container);

    // 📥 Fetch del contenido HTML del widget
    fetch(`https://menu.maxmenu.com/widget/${restaurantId}`, { mode: 'cors' })
      .then(res => {
        if (!res.ok) throw new Error(`❌ Error HTTP ${res.status} al cargar el widget`);
        return res.text();
      })
      .then(html => {
        console.debug('[MaxMenu] ✅ HTML del widget recibido, insertando...');

        // Reemplazar HTML completamente
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        container.innerHTML = tmp.innerHTML;

        // 🧠 Ejecutar scripts inline (comportamientos dinámicos)
        tmp.querySelectorAll('script').forEach(old => {
          const ns = document.createElement('script');
          Array.from(old.attributes).forEach(a => ns.setAttribute(a.name, a.value));
          ns.textContent = old.textContent;
          container.appendChild(ns); // No usar body
        });

        console.debug('[MaxMenu] ⚙️ Widget completamente reconstruido');
      })
      .catch(err => {
        console.error('[MaxMenu] ❌ Error cargando el widget:', err);
        container.innerHTML = '<p>[MaxMenu] No se pudo cargar el menú.</p>';
      });

  } catch (err) {
    console.error('[MaxMenu] ⚠️ Error crítico en widget.js:', err);
  }
})();