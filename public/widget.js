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

  // === OVERLAY + SPACER (en el flujo, reusables) ===
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
        z-index: 2;  /* encima del menú solo en el área del host */
        opacity: 1;  /* visible de inicio */
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
      ${'<div class="skeleton-button"></div>'.repeat(7)}
    </div>
  `;
  host.appendChild(overlay);

  const spacer = document.createElement('div');
  spacer.id = 'maxmenu-skeleton-spacer';
  host.appendChild(spacer);
  spacer.style.height = '60vh';
  requestAnimationFrame(() => {
    const sk = overlay.querySelector('#maxmenu-skeleton');
    spacer.style.height = sk.offsetHeight ? `${sk.offsetHeight}px` : '60vh';
  });

  // === VERSIONING (optimistic-first) ===
  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const fallbackVersion   = '__VERSION__';
  let currentVersion      = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  // Bloqueo del skeleton hasta confirmar versión final si hay mismatch
  let lockSkeleton = true;             // sigue tu lógica
  let finalTargetVersion = null;

  // 1) Tomamos version.json (cacheado) para montar YA
  try {
    const vRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`, { cache: 'force-cache' });
    if (vRes.ok) {
      const vData = await vRes.json();
      if (vData.version) currentVersion = vData.version;
    }
  } catch {}

  // 2) En paralelo pedimos latest.json (no-store), SIN bloquear el primer render
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

  // ========== 🔒 DETECCIÓN ROBUSTA DE “MENÚ PINTADO” (NUEVO) ==========
  const sleep = (ms) => new Promise(r => setTimeout(r, ms));

  // Heurística: altura > 80px + hay nodos; o existen sentinelas del widget
  const isMenuPainted = () => {
    const h = container.getBoundingClientRect().height;
    if (h > 80 && container.querySelector('*')) return true;
    // Sentinelas habituales del widget (ajústalas si cambian):
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

  // Espera hasta que el layout sea estable unos frames antes de ocultar
  const waitPaintThenHide = async (timeoutMs = 2000) => {
    const t0 = performance.now();
    let stable = 0;
    let lastH = 0;

    while (performance.now() - t0 < timeoutMs) {
      await sleep(40); // ~2-3 frames
      const h = container.getBoundingClientRect().height;
      if (isMenuPainted()) {
        if (Math.abs(h - lastH) < 2) stable++;
        else stable = 0;
        lastH = h;
        if (stable >= 3) { // ~120ms de estabilidad
          hideSkeleton();
          return true;
        }
      }
    }
    return false;
  };

  // Observa cambios de tamaño para detectar pintura tardía del layout
  const ro = new ResizeObserver(async () => {
    if (!lockSkeleton) {
      if (await waitPaintThenHide(800)) { /* ocultado */ }
    }
  });
  ro.observe(container);

  // ================================================================

  const removeExistingWidgetScripts = () => {
    document.querySelectorAll('script[maxmenu-script]').forEach(s => s.remove());
  };

  const hideSkeleton = () => {
    const skEl = overlay.querySelector('#maxmenu-skeleton');
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        skEl.style.opacity = '0';          // solo opacidad, no display
        setTimeout(() => {
            spacer.style.height = '0px';   // ahora manda la altura real del menú
        }, 350);
      });
    });
  };

  const showSkeleton = () => {
    spacer.style.height = `${container.offsetHeight || spacer.offsetHeight || 0}px`;
    const skEl = overlay.querySelector('#maxmenu-skeleton');
    void skEl.offsetHeight;
    skEl.style.opacity = '1';
  };

  // Inyecta el widget y, si es la versión final, desbloquea y fuerza comprobación robusta
  const loadWidget = (version, unlockOnReady = false) => {
    const script = document.createElement('script');
    script.src = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');
    script.setAttribute('data-mm-version', version);

    script.addEventListener('load', async () => {
      // Si esta es la versión final, desbloquea y comprueba con heurística robusta
      if (unlockOnReady) lockSkeleton = false;

      // Intento inmediato + reintentos por estabilidad
      if (!lockSkeleton) {
        // 1) check rápido
        if (isMenuPainted()) { hideSkeleton(); return; }
        // 2) espera layout estable y oculta
        await waitPaintThenHide(1500);
      }
    });

    document.head.appendChild(script);
  };

  // Hot-swap a otra versión **manteniendo SIEMPRE el skeleton visible**
  const hotSwapTo = async (nextVersion) => {
    if (!nextVersion || nextVersion === currentVersion) return;
    console.log(`[MaxMenu] 🔄 Hot-swap → ${currentVersion} → ${nextVersion}`);

    // Skeleton ya visible; aseguramos 2 frames antes de limpiar
    await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));

    removeExistingWidgetScripts();
    container.innerHTML = '';

    currentVersion = nextVersion;
    finalTargetVersion = nextVersion;
    localStorage.setItem(KEY_STORAGE_VERSION, nextVersion);

    // Cargamos latest y **desbloqueamos** al pintar
    loadWidget(nextVersion, /* unlockOnReady */ true);
  };

  // Ocultar skeleton solo cuando esté permitido y pintado
  const removeSkeletonIfPainted = async () => {
    if (lockSkeleton) return;
    if (isMenuPainted()) hideSkeleton();
  };

  const observer = new MutationObserver(() => removeSkeletonIfPainted());
  observer.observe(container, { childList: true, subtree: true });

  // Si el widget emite evento explícito:
  const onReady = async () => {
    if (!lockSkeleton) {
      if (isMenuPainted()) hideSkeleton();
      else await waitPaintThenHide(1500);
    }
  };
  window.addEventListener('MaxMenuReady', onReady);

  // === 1er render inmediato (version.json cacheado) ===
  loadWidget(currentVersion, /* unlockOnReady */ false);

  // === Resolver latest y decidir flujo de ocultación ===
  const latestVersion = await latestPromise;

  if (latestVersion && latestVersion !== currentVersion) {
    // Mismatch: mantenemos el skeleton visible hasta montar la latest
    await hotSwapTo(latestVersion);
  } else {
    // No mismatch: esta versión es final → desbloquear y ocultar con heurística
    finalTargetVersion = currentVersion;
    lockSkeleton = false;
    // Intento directo + estabilidad
    if (!isMenuPainted()) await waitPaintThenHide(1500);
    else hideSkeleton();
  }

  // Seguridad: si a los 12s no hay nada, atenuar skeleton (no flash)
  setTimeout(() => {
    if (!(container.offsetHeight > 0 && container.querySelector('*'))) {
      const skEl = overlay.querySelector('#maxmenu-skeleton');
      skEl.style.opacity = '0.4';
    }
  }, 12000);
})();
