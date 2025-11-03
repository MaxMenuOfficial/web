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
        z-index: 2;  /* por encima del menú durante swaps, pero solo en el área del host */
        opacity: 1;  /* visible de inicio */
      }
      #maxmenu-skeleton-flag {
        width: 30px; height: 30px; border-radius: 50%;
        background-color: #e7e7e7; margin: 10px 0; /* 10px arriba/abajo */
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
  // Altura inicial para evitar micro-salto antes de medir
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
  let lockSkeleton = true;             // ⬅️ NUEVO: de entrada, el skeleton NO se oculta
  let finalTargetVersion = null;       // versión que consideramos "final" para desbloquear

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

  // === Helpers ===
  const nextFrame = () => new Promise(r => requestAnimationFrame(() => r()));

  const removeExistingWidgetScripts = () => {
    document.querySelectorAll('script[maxmenu-script]').forEach(s => s.remove());
  };

  const hideSkeleton = () => {
    const skEl = overlay.querySelector('#maxmenu-skeleton');
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        skEl.style.opacity = '0';          // solo opacidad, no display
        setTimeout(() => {
          spacer.style.height = '0px';     // ahora manda la altura real del menú
        }, 350);
      });
    });
  };

  const showSkeleton = () => {
    // Ocupa el alto actual del menú para evitar salto
    spacer.style.height = `${container.offsetHeight || spacer.offsetHeight || 0}px`;
    const skEl = overlay.querySelector('#maxmenu-skeleton');
    void skEl.offsetHeight;                // forzar reflow si venía de transición
    skEl.style.opacity = '1';
  };

  // Inyecta el widget y, si es la versión final, desbloquea para poder ocultar skeleton
  const loadWidget = (version, unlockOnReady = false) => {
    const script = document.createElement('script');
    script.src = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');
    script.setAttribute('data-mm-version', version);

    // 1) Evento de carga del script (fallback si no emite MaxMenuReady)
    script.addEventListener('load', () => {
      setTimeout(() => {
        if (container.offsetHeight > 0 && container.querySelector('*')) {
          if (unlockOnReady) lockSkeleton = false;  // ⬅️ DESBLOQUEA solo si es la versión final
          if (!lockSkeleton) hideSkeleton();
        }
      }, 50);
    });

    document.head.appendChild(script);
  };

  // Hot-swap a otra versión manteniendo SIEMPRE el skeleton visible
  const hotSwapTo = async (nextVersion) => {
    if (!nextVersion || nextVersion === currentVersion) return;
    console.log(`[MaxMenu] 🔄 Hot-swap → ${currentVersion} → ${nextVersion}`);

    // El skeleton ya está visible (lockSkeleton=true). Asegura paint antes de limpiar:
    await nextFrame(); await nextFrame();

    removeExistingWidgetScripts();
    container.innerHTML = '';

    currentVersion = nextVersion;
    finalTargetVersion = nextVersion;
    localStorage.setItem(KEY_STORAGE_VERSION, nextVersion);

    // Cargamos la versión final y desbloqueamos al pintar
    loadWidget(nextVersion, /* unlockOnReady */ true);
  };

  // === Observadores para ocultar skeleton solo cuando esté permitido y pintado ===
  const removeSkeletonIfPainted = () => {
    if (lockSkeleton) return; // ⬅️ mientras esté bloqueado, NUNCA ocultamos
    if (container.offsetHeight > 0 && container.querySelector('*')) hideSkeleton();
  };

  const observer = new MutationObserver(() => removeSkeletonIfPainted());
  observer.observe(container, { childList: true, subtree: true });

  // Si el widget emite el evento explícito:
  const onReady = () => {
    if (!lockSkeleton) hideSkeleton(); // ⬅️ solo si ya se puede
  };
  window.addEventListener('MaxMenuReady', onReady);

  // === 1er render inmediato (version.json cacheado) ===
  // IMPORTANTE: todavía NO sabemos si habrá mismatch → mantenemos lockSkeleton=true
  loadWidget(currentVersion, /* unlockOnReady */ false);

  // === Resolver latest y decidir flujo de ocultación
  const latestVersion = await latestPromise;

  if (latestVersion && latestVersion !== currentVersion) {
    // Mismatch: mantenemos el skeleton SIEMPRE visible hasta montar la latest
    // (lockSkeleton ya está true, así que no se ocultará entre medias)
    await hotSwapTo(latestVersion);
  } else {
    // No mismatch: esta versión ya es la final → desbloquear para poder ocultar
    finalTargetVersion = currentVersion;
    lockSkeleton = false;
    // Si ya está pintado, se oculta ahora; si no, lo hará el observer o MaxMenuReady.
    removeSkeletonIfPainted();
  }

  // Seguridad: si a los 12s no hay nada, atenuamos skeleton (no flash)
  setTimeout(() => {
    if (!(container.offsetHeight > 0 && container.querySelector('*'))) {
      const skEl = overlay.querySelector('#maxmenu-skeleton');
      skEl.style.opacity = '0.4';
    }
  }, 12000);
})();
