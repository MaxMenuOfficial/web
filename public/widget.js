(async () => {
  const container = document.getElementById('maxmenu-menuContainer');
  const restaurantId = container?.dataset?.restaurantId;
  if (!restaurantId) return console.error('[MaxMenu] ❌ data-restaurant-id no definido.');

  // === 1. INYECTAR SKELETON COMO OVERLAY EXTERNO ===
  const overlay = document.createElement('div');
  overlay.id = 'maxmenu-skeleton-overlay';
  overlay.innerHTML = `
  <style>
  #maxmenu-skeleton-overlay {
    position: relative;
  }

  #maxmenu-skeleton-inner {
    position: absolute;
    inset: 0; /* ocupa exactamente el contenedor */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: transparent;
    z-index: 5; /* visible, pero no sobre toda la web */
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
    background: linear-gradient(90deg, #eeeeee 25%, #f6f6f6 50%, #eeeeee 75%);
    background-size: 400% 100%;
    animation: shimmer 1.8s infinite linear;
  }

  @keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }
</style>
    <div id="maxmenu-skeleton-inner">
      <div id="maxmenu-skeleton-flag"></div>
      ${'<div class="skeleton-button"></div>'.repeat(7)}
    </div>`;
  
  // Lo colocamos justo después del contenedor, no dentro
  container.parentNode.insertBefore(overlay, container.nextSibling);

  console.log('[MaxMenu] 🩶 Skeleton overlay activo sobre el menú.');

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

    // Cuando el menú esté realmente listo → fade out del skeleton overlay
    window.addEventListener('MaxMenuReady', () => {
      const skeleton = document.getElementById('maxmenu-skeleton-inner');
      if (!skeleton) return;
      skeleton.style.opacity = '0';
      setTimeout(() => skeleton.parentElement.remove(), 400);
      console.log('[MaxMenu] ✅ Skeleton eliminado, menú visible sin lapsus.');
    });

    document.head.appendChild(script);
  } catch (err) {
    console.error('[MaxMenu] ❌ Error cargando widget.js:', err);
  }
})();