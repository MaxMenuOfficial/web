// File: public/widget.js
(function () {
  // ➊ Detectar el <script> embebido y leer restaurantId
  const scripts      = document.getElementsByTagName('script');
  const myScript     = scripts[scripts.length - 1];
  const restaurantId = myScript?.getAttribute('data-restaurant-id');
  if (!restaurantId) {
    return console.error('[MaxMenu] Falta el atributo data-restaurant-id en el <script>.');
  }

  // ➋ Localizar el contenedor donde inyectar el widget
  const container = document.getElementById('maxmenu-menuContainer');
  if (!container) {
    return console.error('[MaxMenu] No se encontró el contenedor con id="maxmenu-menuContainer".');
  }

  // ➌ Inyectar CSS “fijos” del widget (no requieren versión)
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

  // ➍ Cargar directamente el HTML del widget (sin versión)
  function loadWidget() {
    const widgetUrl = `https://menu.maxmenu.com/menu-widget?id=${encodeURIComponent(restaurantId)}`;

    fetch(widgetUrl, { mode: 'cors' })
      .then(res => {
        if (!res.ok) throw new Error('[MaxMenu] Error al cargar el widget.');
        return res.text();
      })
      .then(html => {
        // 4️⃣ Inyectar el HTML dentro del contenedor
        container.innerHTML = html;

        // 5️⃣ Reejecutar scripts inline que vinieron en el HTML
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

        console.log(`[MaxMenu] Widget cargado con éxito.`);
      })
      .catch(err => {
        console.error(err);
        container.innerHTML = '<p>[MaxMenu] No se pudo cargar el menú.</p>';
      });
  }

  // ➏ Iniciar
  loadWidget();
})();