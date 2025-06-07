<?php
// File: public/widget.php
// Cabeceras para JS y forzar revalidación
header('Content-Type: application/javascript');
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Access-Control-Allow-Origin: *');
?>
(function () {

  const scripts = document.getElementsByTagName('script');
  const myScript = scripts[scripts.length - 1];
  const restaurantId = myScript?.getAttribute('data-restaurant-id');
  if (!restaurantId) {
    return console.error('[MaxMenu] Falta el atributo data-restaurant-id en el');
  }

  // 2️⃣ Obtener el contenedor donde inyectar el widget
  const container = document.getElementById('maxmenu-menuContainer');
  if (!container) {
    return console.error('[MaxMenu] No se encontró el contenedor con id="maxmenu-menuContainer".');
  }

  // 3️⃣ Función principal: carga versión y renderiza widget
  function loadWidget() {
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

        // 4️⃣ Construir URL versionada del widget
        const widgetUrl = `https://menu.maxmenu.com/menu-widget?id=${encodeURIComponent(restaurantId)}&v=${v}`;

        // 5️⃣ Cargar el HTML del widget
        return fetch(widgetUrl)
          .then(res => {
            if (!res.ok) throw new Error('[MaxMenu] Error al cargar el widget.');
            return res.text();
          })
          .then(html => ({ html, v }));
      })
      .then(({ html, v }) => {
        // 6️⃣ Inyectar el HTML dentro del contenedor
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

        console.log(`[MaxMenu] Widget versión ${v} cargado con éxito.`);
      })
      .catch(err => {
        console.error(err);
        container.innerHTML = '<p>[MaxMenu] No se pudo cargar el menú.</p>';
      });
  }

  // 8️⃣ Iniciar carga
  loadWidget();
})();
