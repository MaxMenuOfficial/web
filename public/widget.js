
(async () => {
  const container = document.getElementById('maxmenu-menuContainer');
  const restaurantId = container?.dataset?.restaurantId;
  if (!restaurantId) return console.error('[MaxMenu] ❌ data-restaurant-id no definido.');

  // === HOST WRAPPER ===
  const host = document.createElement('div');
  host.id = 'maxmenu-host';
  host.style.position = 'relative';
  host.style.width = '100%';
  container.parentNode.insertBefore(host, container);
  host.appendChild(container);

  // === SKELETON (siempre visible hasta menú pintado) ===
  const overlay = document.createElement('div');
  overlay.id = 'maxmenu-skeleton-overlay';
  overlay.innerHTML = `
    <style>
      #maxmenu-skeleton-overlay { pointer-events:none; }
      #maxmenu-skeleton{
        position:absolute; inset:0; display:flex; flex-direction:column; align-items:center;
        justify-content:flex-start; padding-top:10px; background:transparent;
        transition:opacity .35s ease; z-index:2; opacity:1;
        will-change:opacity,transform; transform:translateZ(0); contain:layout paint;
      }
      #maxmenu-skeleton-flag{
        width:30px; height:30px; border-radius:50%; background-color:#e7e7e7; margin:10px 0;
      }
      .skeleton-button{
        font-weight:bolder; background-color:#e7e7e7; border:6px solid #e7e7e7; color:transparent;
        padding:30px 20px; margin:6px auto; border-radius:0; font-size:14px;
        max-width:250px; min-width:250px; text-align:center; opacity:.95;
        background:linear-gradient(90deg,#eee 25%,#f6f6f6 50%,#eee 75%);
        background-size:400% 100%; animation:shimmer 1.8s infinite linear;
      }
      @keyframes shimmer{0%{background-position:200% 0;}100%{background-position:-200% 0;}}
    </style>
    <div id="maxmenu-skeleton">
      <div id="maxmenu-skeleton-flag"></div>
      ${'<div class="skeleton-button"></div>'.repeat(7)}
    </div>
  `;
  host.appendChild(overlay);

  const spacer = document.createElement('div');
  spacer.id = 'maxmenu-skeleton-spacer';
  host.appendChild(spacer);
  spacer.style.height = '60vh';
  requestAnimationFrame(() => {
    const sk = document.getElementById('maxmenu-skeleton');
    spacer.style.height = sk?.offsetHeight ? `${sk.offsetHeight}px` : '60vh';
  });

  // === KEYS ===
  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const KEY_RELOAD_TS       = `mmx_reload_ts_${restaurantId}`;     // anti bucle
  const fallbackVersion     = '__VERSION__';
  let currentVersion        = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  // Si el navegador intenta restaurar desde BFCache, forzamos nueva carga
  window.addEventListener('pageshow', (e) => { if (e.persisted) location.reload(); });

  const raf = () => new Promise(r => requestAnimationFrame(r));

  // Skeleton: no lo quitamos JAMÁS si hay o habrá recarga.
  let mustReload = false;

  const keepSkeletonLocked = () => {
    spacer.style.height = `${container.offsetHeight || spacer.offsetHeight || 0}px`;
    const skEl = document.getElementById('maxmenu-skeleton');
    if (skEl) { void skEl.offsetHeight; skEl.style.opacity = '1'; }
  };

  const hideSkeletonSafely = async () => {
    if (mustReload) return; // si vamos a recargar, no ocultes
    // doble RAF para asegurar DOM del menú ya pintado
    await raf(); await raf();
    if (mustReload) return;
    if (!(container.offsetHeight > 0 || container.querySelector('*'))) return;

    const skEl = document.getElementById('maxmenu-skeleton');
    if (!skEl) return;
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        skEl.style.opacity = '0';
        setTimeout(() => { spacer.style.height = '0px'; }, 350);
      });
    });
  };

  const hardReloadTo = async (latestVersion) => {
    mustReload = true;
    keepSkeletonLocked(); // queda a la vista hasta abandonar la página
    // persistimos la versión destino
    localStorage.setItem(KEY_STORAGE_VERSION, latestVersion);
    // anti bucle simple (1.5s)
    sessionStorage.setItem(KEY_RELOAD_TS, String(Date.now()));

    // bust cache por query param de versión
    const url = new URL(location.href);
    url.searchParams.set('mmxv', latestVersion);

    // asegurar al menos un frame con skeleton visible antes de irnos
    await raf(); await raf();
    location.replace(url.toString());
  };

  // === 1) version.json (cacheado) → establecemos currentVersion
  try {
    const vRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`, { cache: 'force-cache' });
    if (vRes.ok) {
      const vData = await vRes.json();
      if (vData.version) currentVersion = vData.version;
    }
  } catch {}

  // Si la URL trae mmxv (tras recarga), úsala como versión objetivo
  const urlMMXV = new URL(location.href).searchParams.get('mmxv');
  if (urlMMXV) currentVersion = urlMMXV;

  // === 2) Cargar widget de currentVersion (bajo skeleton)
  const loadWidget = (version) => {
    const script = document.createElement('script');
    script.src = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');
    script.setAttribute('data-mm-version', version);

    // Cuando el script esté cargado, intentamos ocultar skeleton solo si NO habrá recarga
    script.addEventListener('load', () => {
      const tryHide = () => hideSkeletonSafely();
      setTimeout(tryHide, 80);
      setTimeout(tryHide, 220);
      setTimeout(tryHide, 600);
    });

    document.head.appendChild(script);
  };
  loadWidget(currentVersion);

  // === 3) latest.json en paralelo (no-store). Si difiere: reload duro con skeleton fijado.
  (async () => {
    try {
      const latestRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`, { cache: 'no-store' });
      if (!latestRes.ok) return;
      const { version: latestVersion } = await latestRes.json();
      if (!latestVersion) return;

      if (latestVersion !== currentVersion) {
        // anti bucle: si acabamos de recargar, no recargues otra vez
        const last = parseInt(sessionStorage.getItem(KEY_RELOAD_TS) || '0', 10);
        if (Date.now() - last < 1500) return;

        await hardReloadTo(latestVersion);
        return;
      }

      // Coinciden: no hay recarga → el skeleton se ocultará cuando el menú pinte (tryHide)
    } catch {}
  })();

  // Seguridad: si a los 12s no hay nada, atenuamos skeleton
  setTimeout(() => {
    if (!(container.offsetHeight > 0 || container.querySelector('*'))) {
      const skEl = document.getElementById('maxmenu-skeleton');
      if (skEl) skEl.style.opacity = '0.4';
    }
  }, 12000);

  // Nunca tocamos skeleton en unload
  window.addEventListener('beforeunload', () => {});
})();
