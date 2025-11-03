(async () => {
  const container = document.getElementById('maxmenu-menuContainer');
  const restaurantId = container?.dataset?.restaurantId;
  if (!restaurantId) return console.error('[MaxMenu] ❌ data-restaurant-id no definido.');

  // === 1. ESTRUCTURA DE ENVOLTURA (skeleton dentro del flujo del sitio) ===
  const wrapper = document.createElement('div');
  wrapper.id = 'maxmenu-wrapper';
  wrapper.style.position = 'relative';
  wrapper.style.width = '100%';

  // Creamos el skeleton
  const skeleton = document.createElement('div');
  skeleton.id = 'maxmenu-skeleton';
  skeleton.innerHTML = `
    <style>
      #maxmenu-skeleton {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 60vh;
        background: transparent;
        animation: fadein 0.25s ease-out;
        transition: opacity 0.4s ease;
      }

      #maxmenu-skeleton-flag {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #e7e7e7ff;
        margin: 10px 0 20px 0;
      }

      .skeleton-button {
        font-weight: bolder;
        background-color: #e7e7e7ff;
        border: 6px solid #e7e7e7ff;
        color: transparent;
        padding: 30px 20px;
        margin: 6px auto;
        border-radius: 0;
        font-size: 14px;
        max-width: 250px;
        min-width: 250px;
        text-align: center;
        opacity: 0.95;
        background: linear-gradient(90deg,#eeeeee 25%,#f6f6f6 50%,#eeeeee 75%);
        background-size: 400% 100%;
        animation: shimmer 1.8s infinite linear;
      }

      @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
      }
      @keyframes fadein { from{opacity:0;} to{opacity:1;} }
    </style>

    <div id="maxmenu-skeleton-flag"></div>
    ${'<div class="skeleton-button"></div>'.repeat(7)}
  `;

  // Insertamos el skeleton dentro del contenedor (flujo natural)
  wrapper.appendChild(skeleton);

  // Movemos el contenido actual dentro del wrapper
  container.parentNode.insertBefore(wrapper, container);
  wrapper.appendChild(container);

  console.log('[MaxMenu] 🩶 Skeleton integrado en el flujo visual.');

  // === 2. LÓGICA DE VERSIÓN ===
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

  // === 3. INYECTAR WIDGET ===
  try {
    const widgetUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/widget.js`;
    const script = document.createElement('script');
    script.src = widgetUrl;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');

    // Eliminamos el skeleton cuando el menú esté completamente renderizado
    window.addEventListener('MaxMenuReady', () => {
      const skeleton = document.getElementById('maxmenu-skeleton');
      if (!skeleton) return;

      // fade-out suave
      skeleton.style.opacity = '0';
      setTimeout(() => skeleton.remove(), 400);
      console.log('[MaxMenu] ✅ Skeleton eliminado, menú visible.');
    });

    document.head.appendChild(script);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando widget.js:', err);
  }
})();