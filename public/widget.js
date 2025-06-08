// File: public/widget.js
(function () {
  // ➊ Detectar el <script> embebido y leer restaurantId
  const scripts      = document.getElementsByTagName('script');
  const myScript     = scripts[scripts.length - 1];
  const restaurantId = myScript?.getAttribute('data-restaurant-id');
  if (!restaurantId) {
    return console.error('[MaxMenu] Falta el atributo data-restaurant-id.');
  }

  // ➋ Localizar el contenedor donde inyectar el widget
  const container = document.getElementById('maxmenu-menuContainer');
  if (!container) {
    return console.error('[MaxMenu] No se encontró el contenedor con id="maxmenu-menuContainer".');
  }

  // ➌ Lista de CSS fijos (sin versiones)
  const cssFiles = [
    'https://menu.maxmenu.com/menu/styles/view-items.css',
    'https://menu.maxmenu.com/menu/styles/view-categorias.css',
    'https://menu.maxmenu.com/menu/styles/view-plataformas.css',
    'https://menu.maxmenu.com/menu/styles/view-idiomas.css',
    'https://menu.maxmenu.com/menu/styles/view-logo.css',
    'https://menu.maxmenu.com/menu/styles/view-menu.css'
  ];

  // ➍ Inyectar cada CSS solo una vez
  cssFiles.forEach(href => {
    if (!document.querySelector(`link[href="${href}"]`)) {
      const link = document.createElement('link');
      link.rel  = 'stylesheet';
      link.href = href;
      document.head.appendChild(link);
    }
  });

  // ➎ URL del HTML del widget (sin versión)
  const widgetUrl = `https://menu.maxmenu.com/menu-widget?id=${encodeURIComponent(restaurantId)}`;

  // ➏ Fetch del HTML
  fetch(widgetUrl, { mode: 'cors' })
    .then(res => {
      if (!res.ok) throw new Error('[MaxMenu] Error al obtener el widget.');
      return res.text();
    })
    .then(html => {
      // ➐ Inyectar el HTML dentro del contenedor
      container.innerHTML = html;

      // ➑ Reinyectar scripts inline que vinieron en el HTML
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

      // ➒ Inyectar los JS externos de comportamiento del widget
      [
        'https://menu.maxmenu.com/assets/widget/colors.js',
        'https://menu.maxmenu.com/assets/widget/image.js',
        'https://menu.maxmenu.com/assets/widget/language.js',
        'https://menu.maxmenu.com/assets/widget/subcategories.js'
      ].forEach(src => {
        if (!document.querySelector(`script[src="${src}"]`)) {
          const s = document.createElement('script');
          s.src   = src;
          s.defer = true;
          document.body.appendChild(s);
        }
      });

      console.log('[MaxMenu] Widget cargado con éxito.');
    })
    .catch(err => {
      console.error(err);
      container.innerHTML = '<p>[MaxMenu] No se pudo cargar el menú.</p>';
    });
})();