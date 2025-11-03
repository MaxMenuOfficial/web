(async () => {
  const container = document.getElementById('maxmenu-menuContainer');
  const restaurantId = container?.dataset?.restaurantId;
  if (!restaurantId) return console.error('[MaxMenu] ❌ data-restaurant-id no definido.');

  // === 0) PREPARAR HOST (WRAPPER) PARA COLOCAR SKELETON EN SU SITIO ===
  const host = document.createElement('div');
  host.id = 'maxmenu-host';
  host.style.position = 'relative';
  host.style.width = '100%';

  // Insertar host antes del contenedor y mover el contenedor dentro (mantiene el flujo)
  container.parentNode.insertBefore(host, container);
  host.appendChild(container);

  // === 1) OVERLAY + SPACER (el overlay no aporta altura; el spacer sí) ===
  const overlay = document.createElement('div');
  overlay.id = 'maxmenu-skeleton-overlay';
  overlay.innerHTML = `
    <style>
      #maxmenu-skeleton-overlay { pointer-events: none; }
      #maxmenu-skeleton {
        position: absolute; inset: 0;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        background: transparent; transition: opacity 0.35s ease;
      }
      #maxmenu-skeleton-flag {
        width: 30px; height: 30px; border-radius: 50%;
        background-color: #e7e7e7; margin: 10px 0 20px 0;
      }
      .skeleton-button {
        font-weight: bolder;
        background-color: #e7e7e7;
        border: 6px solid #e7e7e7;
        color: transparent;
        padding: 30px 20px;
        margin: 6px auto;
        border-radius: 0;
        font-size: 14px;
        max-width: 250px; min-width: 250px;
        text-align: center; opacity: 0.95;
        background: linear-gradient(90deg, #eee 25%, #f6f6f6 50%, #eee 75%);
        background-size: 400% 100%;
        animation: shimmer 1.8s infinite linear;
      }
      @keyframes shimmer { 0%{background-position:200% 0;} 100%{background-position:-200% 0;} }
    </style>
    <div id="maxmenu-skeleton">
      <div id="maxmenu-skeleton-flag"></div>
      ${'<div class="skeleton-button"></div>'.repeat(7)}
    </div>
  `;
  host.appendChild(overlay);

  // Spacer que ocupa exactamente el alto del skeleton para integrarse al layout
  const spacer = document.createElement('div');
  spacer.id = 'maxmenu-skeleton-spacer';
  // Medimos después de insertar para capturar alto real
  host.appendChild(spacer);
  requestAnimationFrame(() => {
    const sk = overlay.querySelector('#maxmenu-skeleton');
    spacer.style.height = sk.offsetHeight ? `${sk.offsetHeight}px` : '60vh';
  });

  // === 2) LÓGICA DE VERSIÓN (idéntica a la tuya) ===
  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const fallbackVersion = '__VERSION__';
  let currentVersion = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  try {
    const vRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`, { cache: 'force-cache' });
    if (vRes.ok) {
      const vData = await vRes.json();
      if (vData.version) currentVersion = vData.version;
    }
  } catch {}

  try {
    const latestRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`, { cache: 'no-store' });
    if (latestRes.ok) {
      const { version: latestVersion } = await latestRes.json();
      if (latestVersion && latestVersion !== currentVersion) {
        localStorage.setItem(KEY_STORAGE_VERSION, latestVersion);
        location.reload();
      }
    }
  } catch {}

  // === 3) INYECTAR WIDGET ===
  const widgetUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/widget.js`;
  const script = document.createElement('script');
  script.src = widgetUrl;
  script.async = true;
  script.setAttribute('maxmenu-script', 'true');

  // === 4) CRITERIOS DE "RENDER LISTO" (tres vías) ===
  const removeSkeleton = () => {
    const sk = document.querySelector('#maxmenu-skeleton');
    if (!sk) return;
    // Alinear altura al contenido final para evitar salto
    spacer.style.height = `${container.offsetHeight || sk.offsetHeight || 0}px`;
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        sk.style.opacity = '0';
        setTimeout(() => {
          overlay.remove();
          spacer.remove();
        }, 350);
      });
    });
    window.removeEventListener('MaxMenuReady', onMaxMenuReady);
    if (observer) observer.disconnect();
  };

  // 4.1) Vía evento explícito del widget
  const onMaxMenuReady = () => removeSkeleton();
  window.addEventListener('MaxMenuReady', onMaxMenuReady);

  // 4.2) Vía detección de cambios en el contenedor (por si el evento no existe)
  const observer = new MutationObserver((mutations) => {
    for (const m of mutations) {
      if (m.addedNodes && m.addedNodes.length > 0) {
        // Contenido real presente y con altura
        if (container.offsetHeight > 0 && container.querySelector('*')) {
          removeSkeleton();
          break;
        }
      }
    }
  });
  observer.observe(container, { childList: true, subtree: true });

  // 4.3) Fallback por carga del script: si carga y vemos contenido, quitamos
  script.addEventListener('load', () => {
    setTimeout(() => {
      if (container.offsetHeight > 0 && container.querySelector('*')) {
        removeSkeleton();
      }
      // si no hay contenido aún, seguimos con el observer
    }, 50);
  });

  // Seguridad: si a los 12s no hay nada, mantenemos skeleton atenuado (no flash)
  setTimeout(() => {
    const sk = document.querySelector('#maxmenu-skeleton');
    if (sk && !(container.offsetHeight > 0 && container.querySelector('*'))) {
      sk.style.opacity = '0.4';
    }
  }, 12000);

  document.head.appendChild(script);
})();
