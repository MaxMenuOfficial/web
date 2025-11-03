(async () => {
  const containerInitial = document.getElementById('maxmenu-menuContainer');
  const restaurantId = containerInitial?.dataset?.restaurantId;
  if (!restaurantId) return console.error('[MaxMenu] ❌ data-restaurant-id no definido.');

  // === HOST WRAPPER ===
  const host = document.createElement('div');
  host.id = 'maxmenu-host';
  host.style.position = 'relative';
  host.style.width = '100%';
  containerInitial.parentNode.insertBefore(host, containerInitial);
  host.appendChild(containerInitial);

  // Mantendremos "container" apuntando al contenedor VISIBLE actual
  let container = containerInitial;

  // === OVERLAY + SPACER (en el flujo) ===
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
        will-change: opacity, transform; transform: translateZ(0);
        contain: layout paint; backface-visibility: hidden;
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
    const sk = document.getElementById('maxmenu-skeleton');
    spacer.style.height = sk?.offsetHeight ? `${sk.offsetHeight}px` : '60vh';
  });

  // === VERSIONING (optimistic-first) ===
  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const fallbackVersion = '__VERSION__';
  let currentVersion = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  // Estado de swap
  let lockSkeleton = true;        // mientras true, jamás ocultamos el esqueletón
  let awaitingFinalPaint = false; // true durante hot-swap (hasta pintar latest)
  let skeletonHidden = false;

  // 1) version.json cacheado (para montaje inmediato)
  try {
    const vRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`, { cache: 'force-cache' });
    if (vRes.ok) {
      const vData = await vRes.json();
      if (vData.version) currentVersion = vData.version;
    }
  } catch {}

  // 2) latest.json en paralelo (no-store)
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

  // === Helpers ===
  const raf = () => new Promise(r => requestAnimationFrame(r));
  const ensureOverlayVisible = async () => {
    spacer.style.height = `${container.offsetHeight || spacer.offsetHeight || 0}px`;
    const skEl = document.getElementById('maxmenu-skeleton');
    if (skEl) { void skEl.offsetHeight; skEl.style.opacity = '1'; }
    await raf(); await raf(); // estabiliza la capa
  };

  const hideSkeleton = () => {
    if (skeletonHidden) return;
    const skEl = document.getElementById('maxmenu-skeleton');
    if (!skEl) return;
    skeletonHidden = true;
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        skEl.style.opacity = '0';
        setTimeout(() => { spacer.style.height = '0px'; }, 350);
      });
    });
  };

  const removeScriptsExcept = (keepVersion) => {
    document.querySelectorAll('script[maxmenu-script]').forEach(s => {
      if (s.getAttribute('data-mm-version') !== keepVersion) s.remove();
    });
  };

  // Carga widget en el contenedor VISIBLE actual (primer render)
  const loadWidgetVisible = (version) => {
    const script = document.createElement('script');
    script.src = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');
    script.setAttribute('data-mm-version', version);

    script.addEventListener('load', () => {
      const tryHide = () => {
        if (!lockSkeleton && (container.offsetHeight > 0 || container.querySelector('*'))) {
          hideSkeleton();
        }
      };
      setTimeout(tryHide, 80);
      setTimeout(tryHide, 220);
      setTimeout(tryHide, 600);
    });

    document.head.appendChild(script);
  };

  // === Doble buffer: staging off-screen con el MISMO id para que widget.js pinte ahí ===
  const hotSwapTo = async (nextVersion) => {
    if (!nextVersion || nextVersion === currentVersion) return;
    console.log(`[MaxMenu] 🔄 Hot-swap (double-buffer) ${currentVersion} → ${nextVersion}`);

    // 1) Esqueletón pintado ANTES de cualquier operación
    await ensureOverlayVisible();

    // 2) Crear STAGING off-screen
    const staging = document.createElement('div');
    staging.style.cssText = 'position:absolute;left:-99999px;top:-99999px;visibility:hidden;pointer-events:none;';
    staging.dataset.restaurantId = restaurantId;

    // 3) Cambiar IDs: el staging toma el id oficial, el visible lo suelta
    const officialId = container.id; // "maxmenu-menuContainer"
    container.id = officialId + '__old';
    staging.id = officialId;

    host.appendChild(staging);

    // 4) Estado: esperamos la pintura final en staging
    awaitingFinalPaint = true;
    lockSkeleton = true;
    skeletonHidden = false;

    // 5) Cargar script apuntando al STAGING
    const script = document.createElement('script');
    script.src = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${nextVersion}/widget.js`;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');
    script.setAttribute('data-mm-version', nextVersion);

    // —— Finalización ATÓMICA con doble RAF tras el SWAP —— //
    const finalizeSwap = async () => {
      if (!awaitingFinalPaint) return;
      // Requisito mínimo: staging ya tiene DOM (no sólo script cargado)
      if (!staging.querySelector('*')) return;

      // SWAP: staging pasa a visible (pero NO ocultamos aún el esqueletón)
      staging.style.cssText = '';                 // vuelve a flujo normal
      host.insertBefore(staging, container);      // staging ocupa lugar
      host.removeChild(container);                // quitamos contenedor viejo
      container = staging;                        // ahora el visible es staging

      // Alinear altura para evitar salto mientras sigue visible el esqueletón
      spacer.style.height = `${container.offsetHeight || spacer.offsetHeight || 0}px`;

      // Esperar 2 frames para asegurar layout/paint completo en pantalla
      await raf(); await raf();

      // Ahora sí: desbloquear y ocultar esqueletón (sin frame blanco)
      awaitingFinalPaint = false;
      lockSkeleton = false;
      hideSkeleton();

      // Limpieza de scripts antiguos
      removeScriptsExcept(nextVersion);

      // Persistir versión actual
      currentVersion = nextVersion;
      localStorage.setItem(KEY_STORAGE_VERSION, nextVersion);

      // Desenganchar observadores/handlers
      stageObserver.disconnect();
      window.removeEventListener('MaxMenuReady', onStageReady);
    };

    // Observer de DOM en staging
    const stageObserver = new MutationObserver(finalizeSwap);
    stageObserver.observe(staging, { childList: true, subtree: true });

    // También respondemos a MaxMenuReady (si lo emite el widget)
    const onStageReady = () => finalizeSwap();
    window.addEventListener('MaxMenuReady', onStageReady);

    // Reintentos por si sólo llega "load" del script pero tarda el DOM
    script.addEventListener('load', () => {
      setTimeout(finalizeSwap, 80);
      setTimeout(finalizeSwap, 220);
      setTimeout(finalizeSwap, 600);
    });

    document.head.appendChild(script);
  };

  // === Primer render inmediato con version.json ===
  loadWidgetVisible(currentVersion);

  // === Resolver latest y decidir ===
  const latestVersion = await latestPromise;

  if (latestVersion && latestVersion !== currentVersion) {
    // Mismatch: doble buffer. Skeleton visible hasta que la latest YA está pintada y swappeada.
    await hotSwapTo(latestVersion);
  } else {
    // Sin mismatch: desbloquear y ocultar cuando pinte
    lockSkeleton = false;
    if (container.offsetHeight > 0 || container.querySelector('*')) hideSkeleton();
  }

  // Seguridad: si a los 12s no hay nada, atenuamos skeleton (no flash)
  setTimeout(() => {
    if (!(container.offsetHeight > 0 || container.querySelector('*'))) {
      const skEl = document.getElementById('maxmenu-skeleton');
      if (skEl) skEl.style.opacity = '0.4';
    }
  }, 12000);
})();
