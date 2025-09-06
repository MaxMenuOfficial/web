
(async () => {
  const container = document.getElementById('maxmenu-menuContainer');
  const restaurantId = container?.dataset?.restaurantId;
  if (!container || !restaurantId) return console.error('[MaxMenu] Falta contenedor o restaurantId');

  // 0) Cloak inmediato
  const CLOAK_STYLE_ID = 'maxmenu-cloak-style';
  let cloak = document.getElementById(CLOAK_STYLE_ID);
  if (!cloak) {
    cloak = document.createElement('style');
    cloak.id = CLOAK_STYLE_ID;
    cloak.textContent = `#maxmenu-menuContainer{visibility:hidden}`;
    document.head.appendChild(cloak);
  }
  // Placeholder para evitar salto (ajústalo a tu “above the fold” típico)
  if (!container.style.minHeight) container.style.minHeight = '360px';

  // 0.1) Pre-conexiones (mejoran handshake TLS)
  const HINTS = [
    ['preconnect','https://cdn.maxmenu.com'],
    ['preconnect','https://menu.maxmenu.com'],
    ['dns-prefetch','//cdn.maxmenu.com'],
    ['dns-prefetch','//menu.maxmenu.com']
  ];
  for (const [rel, href] of HINTS) {
    const l = document.createElement('link');
    l.rel = rel; l.href = href;
    document.head.appendChild(l);
  }

  // 1) Limpieza (después del cloak)
  container.innerHTML = '';
  document.querySelectorAll('script[maxmenu-script]').forEach(el => el.remove());
  document.querySelectorAll('link[maxmenu-style]').forEach(el => el.remove());

  // 2) latest.json desde CDN
  const latestUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`;

  try {
    const res = await fetch(latestUrl, { cache: 'no-store' });
    if (!res.ok) throw new Error(`HTTP ${res.status} latest.json`);
    const { version } = await res.json();
    if (!version) throw new Error('latest.json sin "version"');

    // 3) Cargar widget.js versionado
    const widgetUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;

    await new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = widgetUrl;
      s.async = true;
      s.setAttribute('maxmenu-script', 'true');
      s.onload = resolve;
      s.onerror = () => reject(new Error('Fallo cargando widget.js'));
      document.head.appendChild(s);
    });

    // 4) Esperar handshake del widget
    const READY_EVT = 'maxmenu:ready';
    const readyTimeoutMs = 10000; // 10s por si hay fuentes/imágenes lentas
    await new Promise((resolve, reject) => {
      let done = false;
      const onReady = (ev) => {
        if (done) return;
        // (Opcional) Validar versión/restaurante:
        // if (ev?.detail?.restaurantId !== restaurantId) return;
        done = true;
        window.removeEventListener(READY_EVT, onReady);
        resolve();
      };
      window.addEventListener(READY_EVT, onReady, { once: true });
      setTimeout(() => {
        if (done) return;
        window.removeEventListener(READY_EVT, onReady);
        reject(new Error('Timeout esperando maxmenu:ready'));
      }, readyTimeoutMs);
    });

    // 5) Reveal controlado
    cloak?.remove();
    container.style.visibility = 'visible';
    container.style.minHeight = '';

    console.log(`[MaxMenu] ✅ Widget v${version} listo para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando el widget:', err);
    cloak?.remove();
    container.style.visibility = 'visible';
    container.innerHTML = '<p style="color:red;">[MaxMenu] No se pudo cargar el menú.</p>';
  }
})();
