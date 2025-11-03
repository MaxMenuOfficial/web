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
        z-index: 2;  /* sobre el menú durante swaps */
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

  // Mantener skeleton visible hasta confirmar versión final si hay mismatch
  let lockSkeleton = true;       // ⬅️ mientras true, no se oculta nunca
  let finalTargetVersion = null; // informativo

  // 1) version.json cacheado
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
  const nextFrame = () => new Promise(r => requestAnimationFrame(() => r()));
  const removeExistingWidgetScripts = () => {
    document.querySelectorAll('script[maxmenu-script]').forEach(s => s.remove());
  };

  const showSkeleton = () => {
    spacer.style.height = `${container.offsetHeight || spacer.offsetHeight || 0}px`;
    const skEl = overlay.querySelector('#maxmenu-skeleton');
    void skEl.offsetHeight;
    skEl.style.opacity = '1';
  };

  const hideSkeleton = () => {
    const skEl = overlay.querySelector('#maxmenu-skeleton');
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        skEl.style.opacity = '0';
        setTimeout(() => { spacer.style.height = '0px'; }, 350);
      });
    });
  };

  // Inyecta widget; si es versión final, desbloquea inmediatamente y prueba a ocultar varias veces
  const loadWidget = (version, unlockOnReady = false) => {
    const script = document.createElement('script');
    script.src = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');
    script.setAttribute('data-mm-version', version);

    script.addEventListener('load', () => {
      // ⬇️ Desbloqueo inmediato si es la versión final (aunque aún no esté todo pintado)
      if (unlockOnReady) lockSkeleton = false;

      // Intentos escalonados de ocultar (cubre layouts que pintan tarde)
      const tryHide = () => {
        if (!lockSkeleton && (container.offsetHeight > 0 || container.querySelector('*'))) {
          hideSkeleton();
        }
      };
      setTimeout(tryHide, 50);
      setTimeout(tryHide, 200);
      setTimeout(tryHide, 600);
    });

    document.head.appendChild(script);
  };

  const hotSwapTo = async (nextVersion) => {
    if (!nextVersion || nextVersion === currentVersion) return;
    console.log(`[MaxMenu] 🔄 Hot-swap → ${currentVersion} → ${nextVersion}`);

    // Skeleton debe seguir visible; aseguramos paint antes de limpiar
    showSkeleton();
    await nextFrame(); await nextFrame();

    removeExistingWidgetScripts();
    container.innerHTML = '';

    currentVersion      = nextVersion;
    finalTargetVersion  = nextVersion;
    localStorage.setItem(KEY_STORAGE_VERSION, nextVersion);

    // Cargamos la versión final y desbloqueamos en load
    loadWidget(nextVersion, /* unlockOnReady */ true);
  };

  // === Ocultar skeleton sólo cuando esté permitido y haya algo pintado ===
  const removeSkeletonIfPainted = () => {
    if (lockSkeleton) return;
    if (container.offsetHeight > 0 || container.querySelector('*')) hideSkeleton();
  };

  const observer = new MutationObserver(() => removeSkeletonIfPainted());
  observer.observe(container, { childList: true, subtree: true });

  window.addEventListener('MaxMenuReady', () => {
    // Si ya podemos, ocultamos en cuanto el widget avisa
    if (!lockSkeleton) hideSkeleton();
  });

  // === Primer render (version.json) ===
  loadWidget(currentVersion, /* unlockOnReady */ false);

  // === Resolver latest y decidir ===
  const latestVersion = await latestPromise;

  if (latestVersion && latestVersion !== currentVersion) {
    // Mismatch: mantener skeleton hasta latest
    await hotSwapTo(latestVersion);
  } else {
    // Sin mismatch: desbloquear y ocultar cuando esté pintado
    finalTargetVersion = currentVersion;
    lockSkeleton = false;
    removeSkeletonIfPainted();
  }

  // Seguridad: si a los 12s no hay nada, atenuamos skeleton (no flash)
  setTimeout(() => {
    if (!(container.offsetHeight > 0 || container.querySelector('*'))) {
      const skEl = overlay.querySelector('#maxmenu-skeleton');
      skEl.style.opacity = '0.4';
    }
  }, 12000);
})();
