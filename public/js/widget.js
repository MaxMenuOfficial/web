// File: public/js/widget.js
// 🚀 ESTE archivo se cachea 1 AÑO: Cloudflare + navegadores.
(function(){
    // 🔍 Detectar ID desde ?id=… en la URL del propio <script>
    const scriptUrl = document.currentScript.src;
    const params    = new URL(scriptUrl).searchParams;
    const restaurantId = params.get('id');
    if (!restaurantId) {
      console.error('[MaxMenu] Falta el parámetro id en widget.js');
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
      console.error('[MaxMenu] Contenedor #maxmenu-menuContainer no encontrado');
      return;
    }
  
    fetch(`https://menu.maxmenu.com/widget/${restaurantId}`, { mode: 'cors' })
      .then(res => {
        if (!res.ok) throw new Error('Error cargando HTML del widget');
        return res.text();
      })
      .then(html => {
        container.innerHTML = html;
        // ⚙️ Volver a ejecutar scripts inline
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
        console.error(err);
        container.innerHTML = '<p>[MaxMenu] No se pudo cargar el menú.</p>';
      });
  })();