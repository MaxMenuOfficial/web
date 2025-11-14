
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

  // === OVERLAY + SPACER (skeleton con “cuerpo” que empuja layout) ===
  const overlay = document.createElement('div');
  overlay.id = 'maxmenu-skeleton-overlay';
  overlay.innerHTML = `
    <style>
      #maxmenu-skeleton-overlay { pointer-events: none; }
      #maxmenu-skeleton {
        position: absolute; inset: 0;
        display: flex; flex-direction: column; align-items: center;
        justify-content: flex-start; padding-top: 10px;
        background: transparent; transition: opacity 0.35s ease;
        z-index: 2; opacity: 1;
      }
      #maxmenu-skeleton-flag {
        width: 30px; height: 30px; border-radius: 50%;
        background-color: #e7e7e7; margin: 10px 0;
      }
      .skeleton-button {
        font-weight: bolder; background-color: #e7e7e7; border: 6px solid #e7e7e7;
        color: transparent; padding: 30px 20px; margin: 6px auto; border-radius: 0;
        font-size: 14px; max-width: 250px; min-width: 250px; text-align: center; opacity: .95;
        background: linear-gradient(90deg, #eee 25%, #f6f6f6 50%, #eee 75%);
        background-size: 400% 100%; animation: shimmer 1.8s infinite linear;
      }
      @keyframes shimmer { 0%{background-position:200% 0;} 100%{background-position:-200% 0;} }
    </style>
    <div id="maxmenu-skeleton">
      <div id="maxmenu-skeleton-flag"></div>
      ${'<div class="skeleton-button"></div>'.repeat(9)}
    </div>
  `;
  host.appendChild(overlay);

  const spacer = document.createElement('div');
  spacer.id = 'maxmenu-skeleton-spacer';
  host.appendChild(spacer);

  // ===== Skeleton “con cuerpo” =====
  const SKELETON_MIN_PX = 320;

  const parseSkeletonHeightConfig = (val) => {
    if (!val) return 0;
    const s = String(val).trim().toLowerCase();
    if (s.endsWith('vh')) { const n = parseFloat(s); return isFinite(n) ? Math.max(0, (n/100)*window.innerHeight) : 0; }
    if (s.endsWith('px')) { const n = parseFloat(s); return isFinite(n) ? Math.max(0, n) : 0; }
    const n = parseFloat(s); return isFinite(n) ? Math.max(0, n) : 0;
  };
  const configuredSkeletonHeight = parseSkeletonHeightConfig(container?.dataset?.skeletonHeight);

  const getSkeletonIntrinsic = () => overlay.querySelector('#maxmenu-skeleton')?.offsetHeight || 0;

  const calcDesiredSkeletonHeight = () => {
    const rect = host.getBoundingClientRect();
    const viewportFill = Math.max(window.innerHeight - Math.max(rect.top, 0), 0);
    const intrinsic    = getSkeletonIntrinsic();
    const containerH   = container.offsetHeight || 0;
    return Math.max(configuredSkeletonHeight, viewportFill, intrinsic, containerH, SKELETON_MIN_PX);
  };

  let skeletonActive = false;
  const setSpacerToDesiredHeight = () => { spacer.style.height = `${calcDesiredSkeletonHeight()}px`; };

  const showSkeleton = () => {
    skeletonActive = true;
    setSpacerToDesiredHeight();
    requestAnimationFrame(() => requestAnimationFrame(setSpacerToDesiredHeight));
    const skEl = overlay.querySelector('#maxmenu-skeleton');
    void skEl.offsetHeight; skEl.style.opacity = '1';
  };

  const hideSkeleton = () => {
    skeletonActive = false;
    const skEl = overlay.querySelector('#maxmenu-skeleton');
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        skEl.style.opacity = '0';
        setTimeout(() => { spacer.style.height = '0px'; }, 350);
      });
    });
  };

  // Reajustar altura mientras esté visible
  const onViewportChange = () => { if (skeletonActive) setSpacerToDesiredHeight(); };
  window.addEventListener('resize', onViewportChange);
  window.addEventListener('orientationchange', onViewportChange);
  const roSkeleton = new ResizeObserver(() => { if (skeletonActive) setSpacerToDesiredHeight(); });
  roSkeleton.observe(host);
  roSkeleton.observe(container);

  // Al empezar, muestra skeleton con cuerpo
  showSkeleton();

  // === Helpers / timing ===
  const sleep = (ms) => new Promise(r => setTimeout(r, ms));

  // === VERSIONING (optimistic-first) ===
  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const fallbackVersion = '__VERSION__';
  let currentVersion = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  let lockSkeleton = true;   // hasta versión final pintada
  let finalTargetVersion = null;

  // 1) version.json cacheado
  try {
    const vRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`, { cache: 'force-cache' });
    if (vRes.ok) {
      const vData = await vRes.json();
      if (vData.version) currentVersion = vData.version;
    }
  } catch {}

  // 2) latest.json no-store en paralelo
  const latestPromise = (async () => {
    try {
      const latestRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`, { cache: 'no-store' });
      if (latestRes.ok) {
        const { version: latestVersion } = await latestRes.json();
        return latestVersion || null;
      }
    } catch {}
    return null;
  })();

  // === DETECCIÓN DE “MENÚ PINTADO” ===
  const isMenuPainted = () => {
    const h = container.getBoundingClientRect().height;
    if (h > 80 && container.querySelector('*')) return true;
    if (
      container.querySelector('#maxmenu-category-container') ||
      container.querySelector('#maxmenu-language') ||
      container.querySelector('.maxmenu-root, .mmx-root') ||
      container.querySelector('[data-mm-ready="1"]') ||
      container.querySelector('[data-role="maxmenu-root"]')
    ) {
      const cs = getComputedStyle(container);
      if (cs.display !== 'none' && cs.visibility !== 'hidden') return true;
    }
    return false;
  };

  const waitPaintThenHide = async (timeoutMs = 2000) => {
    const t0 = performance.now(); let stable = 0, lastH = 0;
    while (performance.now() - t0 < timeoutMs) {
      await sleep(40);
      const h = container.getBoundingClientRect().height;
      if (isMenuPainted()) {
        if (Math.abs(h - lastH) < 2) stable++; else stable = 0;
        lastH = h;
        if (stable >= 3) { hideSkeleton(); return true; }
      }
    }
    return false;
  };

  const ro = new ResizeObserver(async () => {
    if (!lockSkeleton) { await waitPaintThenHide(800); }
  });
  ro.observe(container);

  const removeExistingWidgetScripts = () => {
    document.querySelectorAll('script[maxmenu-script]').forEach(s => s.remove());
  };

  // === Carga de widget.js (con fallback si no pinta) ===
  const loadWidget = (version, unlockOnReady = false) => {
    const script = document.createElement('script');
    // Nota: mantenemos cache del CDN, pero en mismatch ya hemos traído HTML no-store.
    script.src = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');
    script.setAttribute('data-mm-version', version);

    script.addEventListener('load', async () => {
      // Solo desbloquea si es la versión final
      if (unlockOnReady && version === finalTargetVersion) lockSkeleton = false;

      if (!lockSkeleton) {
        if (isMenuPainted()) { hideSkeleton(); return; }
        const painted = await waitPaintThenHide(2000);
        if (!painted) {
          // Último intento: si seguimos sin pintura, mantenemos skeleton atenuado (pero ya hay HTML)
          console.warn('[MaxMenu] ⚠️ Script cargado pero sin pintura estable. Mantengo skeleton visible.');
        }
      }
    });

    script.addEventListener('error', () => {
      console.error('[MaxMenu] ❌ Error cargando widget.js versión', version);
    });

    document.head.appendChild(script);
  };

  // === Hot-swap HTML-first (nunca “nada”) ===
  const hotSwapTo = async (nextVersion) => {
    if (!nextVersion || nextVersion === currentVersion) return;
    console.log(`[MaxMenu] 🔄 Hot-swap → ${currentVersion} → ${nextVersion}`);

    // 1) Asegurar skeleton ACTIVO
    showSkeleton();
    lockSkeleton = true;

    // 2) Dos frames para fijar overlay
    await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));

    // 3) Limpiar scripts antiguos
    removeExistingWidgetScripts();

    // 4) Inyectar HTML de la versión destino (HTML-first)
    try {
      const htmlUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${nextVersion}/widget.html?ts=${Date.now()}`;
      const htmlRes = await fetch(htmlUrl, { cache: 'no-store' });
      if (htmlRes.ok) {
        const html = await htmlRes.text();
        container.innerHTML = html; // ya hay algo visible; nunca en blanco
      } else {
        // si no hay HTML, al menos vacío limpio (pero skeleton sostiene el alto)
        container.innerHTML = '';
      }
    } catch {
      container.innerHTML = '';
    }

    // 5) Persistir versión objetivo y cargar JS
    currentVersion = nextVersion;
    finalTargetVersion = nextVersion;
    localStorage.setItem(KEY_STORAGE_VERSION, nextVersion);

    loadWidget(nextVersion, /* unlockOnReady */ true);
  };

  const removeSkeletonIfPainted = async () => {
    if (lockSkeleton) return;
    if (isMenuPainted()) hideSkeleton();
  };

  const observer = new MutationObserver(() => removeSkeletonIfPainted());
  observer.observe(container, { childList: true, subtree: true });

  const onReady = async () => {
    if (!lockSkeleton) {
      if (isMenuPainted()) hideSkeleton();
      else await waitPaintThenHide(1500);
    }
  };
  window.addEventListener('MaxMenuReady', onReady);

  // === 1er render inmediato (version.json cacheado) ===
  loadWidget(currentVersion, /* unlockOnReady */ false);

  // === Resolver latest y decidir ===
  const latestVersion = await latestPromise;

  if (latestVersion && latestVersion !== currentVersion) {
    // Reasegura skeleton por si se ocultó
    showSkeleton();
    await hotSwapTo(latestVersion);
  } else {
    finalTargetVersion = currentVersion;
    lockSkeleton = false;
    if (!isMenuPainted()) await waitPaintThenHide(1500);
    else hideSkeleton();
  }

  // Seguridad: a los 12s atenuar skeleton si no hay nada visible
  setTimeout(() => {
    if (!(container.offsetHeight > 0 && container.querySelector('*'))) {
      const skEl = overlay.querySelector('#maxmenu-skeleton');
      skEl.style.opacity = '0.4';
      console.warn('[MaxMenu] ⏳ Sigue sin pintar tras 12s; skeleton atenuado.');
    }
  }, 12000);
})();
