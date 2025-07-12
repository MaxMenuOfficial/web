<?php
// File: public/widget/widget-js.php
// ⚠️ Este archivo debe regenerarse manualmente cuando cambia el menú.
// Está cacheado por Cloudflare y navegadores por 1 año.

$restaurantId = $_GET['id'] ?? null;
$menuVersion  = $_GET['v']  ?? null;

header('Content-Type: application/javascript');
header('Cache-Control: public, max-age=31536000, s-maxage=31536000');

if (!$restaurantId || !$menuVersion) {
    http_response_code(400);
    exit('// [MaxMenu] Error: Faltan parámetros ?id= y ?v=');
}

$widgetUrl = "https://menu.maxmenu.com/widget/{$restaurantId}/v/{$menuVersion}";

echo <<<JS
(function () {
  const containerId = 'maxmenu-menuContainer';
  const container   = document.getElementById(containerId);
  if (!container) {
    return console.error('[MaxMenu] Contenedor no encontrado: #' + containerId);
  }

  // CSS fijos
  ['view-items','view-plataformas','view-logo','view-menu'].forEach(name => {
    const href = `https://menu.maxmenu.com/assets/css/widget/styles/\${name}.css`;
    if (!document.querySelector(\`link[href="\${href}"]\`)) {
      const link = document.createElement('link');
      link.rel  = 'stylesheet';
      link.href = href;
      document.head.appendChild(link);
    }
  });

  // HTML versionado (cambia si cambia el menú)
  fetch('$widgetUrl', { mode: 'cors' })
    .then(res => res.ok ? res.text() : Promise.reject('[MaxMenu] Error cargando widget HTML.'))
    .then(html => {
      container.innerHTML = html;
      const tmp = document.createElement('div');
      tmp.innerHTML = html;
      tmp.querySelectorAll('script').forEach(script => {
        const newScript = document.createElement('script');
        Array.from(script.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
        newScript.textContent = script.textContent;
        document.body.appendChild(newScript);
      });
    })
    .catch(err => {
      console.error(err);
      container.innerHTML = '<p>[MaxMenu] No se pudo cargar el menú.</p>';
    });
})();
JS;