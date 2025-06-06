(function () {
  const scripts = document.getElementsByTagName('script');
  const myScript = scripts[scripts.length - 1];
  const restaurantId = myScript?.getAttribute('data-restaurant-id');

  if (!restaurantId) return console.error('[MaxMenu] Falta el atributo data-restaurant-id.');

  const container = document.getElementById('maxmenu-menuContainer');
  if (!container) return console.error('[MaxMenu] No se encontró el contenedor');

  // --- 🔥 OBTENER VERSIÓN ---
  fetch(`https://menu.maxmenu.com/api/menu-version.php?id=${encodeURIComponent(restaurantId)}`)
    .then(res => res.ok ? res.json() : Promise.reject('[MaxMenu] Error al obtener la versión'))
    .then(data => {
      if (!data.version || typeof data.version !== 'number') {
        throw new Error('[MaxMenu] Versión inválida del menú');
      }

      const v = data.version;

      // ✅ Inyectar CSS con versión
      [
        'view-items.css',
        'view-categorias.css',
        'view-plataformas.css',
        'view-idiomas.css',
        'view-logo.css',
        'view-menu.css'
      ].forEach(name => {
        const href = `https://menu.maxmenu.com/menu/styles/${name}?v=${v}`;
        if (!document.querySelector(`link[href="${href}"]`)) {
          const link = document.createElement('link');
          link.rel = 'stylesheet';
          link.href = href;
          document.head.appendChild(link);
        }
      });

      // 🔄 Inyectar HTML con versión
      const url = `https://menu.maxmenu.com/menu-widget/${encodeURIComponent(restaurantId)}?v=${v}`;
      fetch(url)
        .then(res => res.ok ? res.text() : Promise.reject('[MaxMenu] Error al obtener el menú'))
        .then(html => {
          container.innerHTML = html;

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

          // ✅ Inyectar JS con versión
          [
            'colors.js',
            'image.js',
            'language.js',
            'subcategories.js'
          ].forEach(name => {
            const s = document.createElement('script');
            s.src = `https://menu.maxmenu.com/assets/widget/${name}?v=${v}`;
            s.defer = true;
            document.body.appendChild(s);
          });

          // 🧠 Para debug
          console.log(`[MaxMenu] Widget versión ${v} cargado con éxito`);
        })
        .catch(err => console.error(err));
    })
    .catch(err => console.error(err));
})();