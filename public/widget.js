// File: public/widget.js
(function () {
  const scripts      = document.getElementsByTagName('script');
  const myScript     = scripts[scripts.length - 1];
  const restaurantId = myScript?.getAttribute('data-restaurant-id');

  if (!restaurantId) {
    return console.error('[MaxMenu] Falta el atributo data-restaurant-id en el <script>.');
  }

  const container = document.getElementById('maxmenu-menuContainer');
  if (!container) {
    return console.error('[MaxMenu] No se encontró el contenedor con id="maxmenu-menuContainer".');
  }

  // ➊ Inyectar los CSS necesarios (no cacheados por restaurante)
  const cssFiles = [
    'https://menu.maxmenu.com/menu_api/styles/view-items.css',
    'https://menu.maxmenu.com/menu_api/styles/view-plataformas.css',
    'https://menu.maxmenu.com/menu_api/styles/view-logo.css',
    'https://menu.maxmenu.com/menu_api/styles/view-menu.css'
  ];
  cssFiles.forEach(href => {
    if (!document.querySelector(`link[href="${href}"]`)) {
      const link = document.createElement('link');
      link.rel  = 'stylesheet';
      link.href = href;
      document.head.appendChild(link);
    }
  });

  // ➋ Construir la nueva URL amigable sin versión
  const widgetUrl = `https://menu.maxmenu.com/widget/${encodeURIComponent(restaurantId)}`;

  // ➌ Cargar el HTML del widget
  fetch(widgetUrl, { mode: 'cors' })
    .then(res => {
      if (!res.ok) throw new Error(`[MaxMenu] Error al cargar el widget para ${restaurantId}`);
      return res.text();
    })
    .then(html => {
      container.innerHTML = html;

      // ➍ Ejecutar scripts embebidos que vinieron en el HTML
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      tempDiv.querySelectorAll('script').forEach(oldScript => {
        const newScript = document.createElement('script');
        Array.from(oldScript.attributes).forEach(attr =>
          newScript.setAttribute(attr.name, attr.value)
        );
        newScript.textContent = oldScript.textContent;
        document.body.appendChild(newScript);
      });

      console.log(`[MaxMenu] Widget cargado con éxito desde /widget/${restaurantId}`);
    })
    .catch(err => {
      console.error(err);
      container.innerHTML = '<p>[MaxMenu] No se pudo cargar el menú.</p>';
    });
})();