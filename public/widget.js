// 📦 Widget.js — simple y determinista (versión __VERSION__)
(async () => {
  const containerId  = 'maxmenu-menuContainer';
  const container    = document.getElementById(containerId);
  const restaurantId = container?.dataset?.restaurantId;
  const version      = '__VERSION__';

  if (!container || !restaurantId) return;

  // Limpieza mínima de restos anteriores del widget
  container.innerHTML = '';
  document.querySelectorAll('link[maxmenu-style]').forEach(el => el.remove());
  document.querySelectorAll('script[maxmenu-script]').forEach(el => el.remove());

  // 1) Lista de estilos (en orden). No seguimos hasta que TODOS terminen.
  const cssHrefs = [
    'https://menu.maxmenu.com/assets/css/widget/styles/view-items.css',
    'https://menu.maxmenu.com/assets/css/widget/styles/view-logo.css',
    'https://menu.maxmenu.com/assets/css/widget/styles/view-plataformas.css',
    'https://menu.maxmenu.com/assets/css/widget/styles/view-menu.css',
  ];

  function loadCssSequential(href) {
    return new Promise((resolve, reject) => {
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = href;
      link.setAttribute('maxmenu-style', '');
      link.onload = () => resolve();
      link.onerror = () => reject(new Error('CSS failed: ' + href));
      document.head.appendChild(link);
    });
  }

  // Cargar cada CSS secuencialmente, garantizando orden y bloqueo total
  for (const href of cssHrefs) {
    await loadCssSequential(href); // si falla, se aborta y NO se inserta HTML
  }

  // 2) Cuando TODOS los CSS estén cargados → insertar HTML
  const widgetHtmlUrl = `https://menu.maxmenu.com/widget/${restaurantId}/${version}`;
  const res = await fetch(widgetHtmlUrl, { cache: 'no-store' });
  if (!res.ok) return; // simple: si falla, no pintamos nada
  const html = await res.text();
  container.innerHTML = html;

  // 3) Reejecutar <script> embebidos del HTML insertado (necesario en DOM)
  container.querySelectorAll('script').forEach(old => {
    const s = document.createElement('script');
    for (const a of old.attributes) s.setAttribute(a.name, a.value);
    s.setAttribute('maxmenu-script', '');
    s.textContent = old.textContent;
    old.replaceWith(s);
  });
})();