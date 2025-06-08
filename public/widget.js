// File: public/widget.js

(function () {
  // ➊ Detectar el <script> embebido y leer restaurantId
  const scripts      = document.getElementsByTagName('script');
  const myScript     = scripts[scripts.length - 1];
  const restaurantId = myScript?.getAttribute('data-restaurant-id');
  if (!restaurantId) {
    return console.error('[MaxMenu] Falta el atributo data-restaurant-id en el <script>.');
  }

  // ➋ Localizar contenedor
  const container = document.getElementById('maxmenu-menuContainer');
  if (!container) {
    return console.error('[MaxMenu] No se encontró el contenedor con id="maxmenu-menuContainer".');
  }

  // ➌ Función principal
  function loadWidget() {
    // 1️⃣ Obtener versión del menú
    fetch(`https://menu.maxmenu.com/api/menu-version.php?id=${encodeURIComponent(restaurantId)}`)
      .then(res => {
        if (!res.ok) throw new Error('[MaxMenu] Error al obtener la versión del menú.');
        return res.json();
      })
      .then(data => {
        const v = data.version;
        if (typeof v !== 'number' || v <= 0) {
          throw new Error('[MaxMenu] Versión inválida recibida: ' + v);
        }

        // 2️⃣ Inyectar CSS versionados
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
            link.rel  = 'stylesheet';
            link.href = href;
            document.head.appendChild(link);
            console.log(`[MaxMenu] CSS cargado: ${name}?v=${v}`);
          }
        });

        // 3️⃣ Inyectar JS versionados del widget
        [
          'colors.js',
          'image.js',
          'language.js',
          'subcategories.js'
        ].forEach(name => {
          const src = `https://menu.maxmenu.com/assets/widget/${name}?v=${v}`;
          if (!document.querySelector(`script[src="${src}"]`)) {
            const s = document.createElement('script');
            s.src   = src;
            s.defer = true;
            document.body.appendChild(s);
            console.log(`[MaxMenu] JS cargado: ${name}?v=${v}`);
          }
        });

        // 4️⃣ Cargar el HTML versionado
        const widgetUrl = `https://menu.maxmenu.com/menu-widget?id=${encodeURIComponent(restaurantId)}&v=${v}`;
        return fetch(widgetUrl, { mode: 'cors' })
          .then(res => {
            if (!res.ok) throw new Error('[MaxMenu] Error al cargar el widget.');
            return res.text();
          })
          .then(html => ({ html, v }));
      })
      
      .then(({ html, v }) => {
        // 5️⃣ Inyectar HTML
        container.innerHTML = html;

        // 6️⃣ Ejecutar scripts inline del HTML
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

        console.log(`[MaxMenu] Widget versión ${v} cargado con éxito.`);
      })
      .catch(err => {
        console.error(err);
        container.innerHTML = '<p>[MaxMenu] No se pudo cargar el menú.</p>';
      });
  }

  // ➏ Arrancar
  loadWidget();
})();