(async () => {
  const container = document.getElementById('maxmenu-menuContainer');
  const restaurantId = container?.dataset?.restaurantId;

  if (!restaurantId) {
    console.error('[MaxMenu] ❌ data-restaurant-id no definido.');
    return;
  }

  // === 1. INYECTAR SKELETON SIN LAPSUS ===
  const skeletonHTML = `
    <style id="maxmenu-skeleton-style">
      #maxmenu-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        width: 100%;
        background-color: transparent; /* fondo sin color */
        animation: fadein 0.25s ease-out;
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
        border-radius: 0px;
        font-size: 14px;
        max-width: 250px;
        min-width: 250px;
        text-align: center;
        opacity: 0.95;
        background: linear-gradient(90deg, #eeeeee 25%, #f6f6f6 50%, #eeeeee 75%);
        background-size: 400% 100%;
        animation: shimmer 1.8s infinite linear;
      }

      @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
      }

      @keyframes fadein {
        from { opacity: 0; }
        to { opacity: 1; }
      }
    </style>

    <div id="maxmenu-loading">
      <div id="maxmenu-skeleton-flag"></div>
      <div class="skeleton-button"></div>
      <div class="skeleton-button"></div>
      <div class="skeleton-button"></div>
      <div class="skeleton-button"></div>
      <div class="skeleton-button"></div>
      <div class="skeleton-button"></div>
      <div class="skeleton-button"></div>
    </div>
  `;

  // Inyectamos sin borrar el contenido previo (evita lapsus visual)
  container.insertAdjacentHTML('afterbegin', skeletonHTML);
  // Forzamos render inmediato antes de limpiar
  void container.offsetHeight;

  // Ahora eliminamos lo anterior, ya con el skeleton pintado
  const prev = container.querySelectorAll(':scope > :not(#maxmenu-loading):not(style)');
  prev.forEach(el => el.remove());

  console.log('[MaxMenu] ⏳ Skeleton transparente activo sin lapsus...');

  // === 2. LÓGICA DE VERSIÓN ===
  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const fallbackVersion = '__VERSION__';
  let currentVersion = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  try {
    const versionJsonURL = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`;
    const versionRes = await fetch(versionJsonURL, { cache: 'force-cache' });
    if (versionRes.ok) {
      const versionData = await versionRes.json();
      if (versionData.version) currentVersion = versionData.version;
    }
  } catch (err) {
    console.warn('[MaxMenu] ⚠️ Error obteniendo version.json:', err);
  }

  try {
    const latestUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`;
    const latestRes = await fetch(latestUrl, { cache: 'no-store' });
    if (latestRes.ok) {
      const { version: latestVersion } = await latestRes.json();
      if (latestVersion && latestVersion !== currentVersion) {
        console.log(`[MaxMenu] 🔁 Versión desactualizada: ${currentVersion} → ${latestVersion}`);
        localStorage.setItem(KEY_STORAGE_VERSION, latestVersion);
        location.reload();
      }
    }
  } catch (err) {
    console.warn('[MaxMenu] ⚠️ Error verificando latest.json:', err);
  }

  // === 3. INYECTAR WIDGET ===
  try {
    const widgetUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/widget.js`;
    const script = document.createElement('script');
    script.src = widgetUrl;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');

    // Cuando el widget real esté listo → eliminar skeleton
    window.addEventListener('MaxMenuReady', () => {
      const loader = document.getElementById('maxmenu-loading');
      if (loader) {
        loader.style.transition = 'opacity 0.25s ease';
        loader.style.opacity = '0';
        setTimeout(() => loader.remove(), 250);
      }
      console.log('[MaxMenu] ✅ Skeleton eliminado suavemente, menú visible.');
    });

    // Fallback: si pasan 10s sin cargar
    setTimeout(() => {
      const loader = document.getElementById('maxmenu-loading');
      if (loader) loader.style.opacity = '0.4';
    }, 10000);

    document.head.appendChild(script);
    console.log(`[MaxMenu] 📦 widget.js v${currentVersion} inyectado para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando widget.js:', err);
    container.innerHTML = '<p style="color:red;text-align:center;">[MaxMenu] Error loading menu.</p>';
  }
})();