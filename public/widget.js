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

  // ➌ Lista de CSS “fijos” del widget (idealmente unificado en un solo bundle)
  const cssFiles = [
    'https://menu.maxmenu.com/menu/styles/all.css'

  ];

  // ➍ Preload + inyección de cada CSS
  cssFiles.forEach(href => {
    // Preload
    if (!document.querySelector(`link[rel="preload"][href="${href}"]`)) {
      const pl = document.createElement('link');
      pl.rel  = 'preload';
      pl.as   = 'style';
      pl.href = href;
      document.head.appendChild(pl);
    }
    // stylesheet
    if (!document.querySelector(`link[href="${href}"]`)) {
      const link = document.createElement('link');
      link.rel  = 'stylesheet';
      link.href = href;
      document.head.appendChild(link);
    }
  });

  // ➎ Función principal: obtiene la versión, construye la URL y carga el HTML
  function loadWidget() {
    // 1️⃣ Obtener la versión actual del menú
    fetch(`https://menu.maxmenu.com/api/menu-version.php?id=${encodeURIComponent(restaurantId)}`, { mode: 'cors' })
      .then(res => {
        if (!res.ok) throw new Error('[MaxMenu] Error al obtener la versión del menú.');
        return res.json();
      })
      .then(data => {
        const v = data.version;
        if (typeof v !== 'number' || v <= 0) {
          throw new Error('[MaxMenu] Versión inválida recibida: ' + v);
        }
        return {
          widgetUrl: `https://menu.maxmenu.com/menu-widget?id=${encodeURIComponent(restaurantId)}&v=${v}`,
          v
        };
      })
      .then(({ widgetUrl, v }) =>
        // 2️⃣ Cargar el HTML versionado
        fetch(widgetUrl, { mode: 'cors' })
          .then(res => {
            if (!res.ok) throw new Error('[MaxMenu] Error al cargar el widget.');
            return res.text();
          })
          .then(html => ({ html, v }))
      )
      .then(({ html, v }) => {
        // 3️⃣ Inyectar el HTML dentro del contenedor
        container.innerHTML = html;

        // 4️⃣ Reejecutar scripts inline que vinieron en el HTML
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

  // ➏ Iniciar
  loadWidget();
})();