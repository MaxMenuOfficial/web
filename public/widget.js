(function () {
  const scripts = document.getElementsByTagName('script');
  const myScript = scripts[scripts.length - 1];
  const restaurantId = myScript?.getAttribute('data-restaurant-id');

  if (!restaurantId) return console.error('[MaxMenu] Falta el atributo data-restaurant-id.');

  const container = document.getElementById('maxmenu-menuContainer');
  if (!container) return console.error('[MaxMenu] No se encontró el contenedor');

  const cssFiles = [
    'https://menu.maxmenu.com/menu/styles/view-items.css',
    'https://menu.maxmenu.com/menu/styles/view-categorias.css',
    'https://menu.maxmenu.com/menu/styles/view-plataformas.css',
    'https://menu.maxmenu.com/menu/styles/view-idiomas.css',
    'https://menu.maxmenu.com/menu/styles/view-logo.css',
    'https://menu.maxmenu.com/menu/styles/view-menu.css'
  ];

  cssFiles.forEach(href => {
    if (!document.querySelector(`link[href="${href}"]`)) {
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = href;
      document.head.appendChild(link);
    }
  });

  // 🔥 PASO NUEVO: obtener la versión del menú antes de pedir el HTML del widget
  fetch(`https://menu.maxmenu.com/api/menu-version.php?id=${encodeURIComponent(restaurantId)}`)
    .then(res => res.ok ? res.json() : Promise.reject('[MaxMenu] Error al obtener la versión'))
    .then(data => {
      const v = data.version || Date.now(); // fallback paranoico
      const url = `https://menu.maxmenu.com/menu-widget/${encodeURIComponent(restaurantId)}?v=${v}`;

      fetch(url)
        .then(res => res.ok ? res.text() : Promise.reject('[MaxMenu] Error al obtener el menú'))
        .then(html => {
          container.innerHTML = html;

          // Reinyectar scripts del HTML (inline)
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

          // Inyectar JS externos después del HTML
          [
            "https://menu.maxmenu.com/assets/widget/colors.js",
            "https://menu.maxmenu.com/assets/widget/image.js",
            "https://menu.maxmenu.com/assets/widget/language.js",
            "https://menu.maxmenu.com/assets/widget/subcategories.js"
          ].forEach(src => {
            const s = document.createElement('script');
            s.src = src;
            s.defer = true;
            document.body.appendChild(s);
          });
        })
        .catch(err => console.error(err));
    })
    .catch(err => console.error(err));
})();