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
        z-index: 2;   /* sobre el menú durante swaps */
        opacity: 1;   /* visible de inicio */
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
  const fallbackVersion   = '__VERSION__';
  let currentVersion      = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  // Estado de swap
  let lockSkeleton = true;          // mientras true, el skeleton NO se oculta
  let awaitingFinalPaint = false;   // true sólo durante hot-swap hasta pintar latest
  let skeletonHidden = false;       // evita ocultar dos veces

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
  const ensureOverlayVisible = async () => {
    spacer.style.height = `${container.offsetHeight || spacer.offsetHeight || 0}px`;
    const skEl = document.getElementById('maxmenu-skeleton');
    if (skEl) { void skEl.offsetHeight; skEl.style.opacity = '1'; }
    await nextFrame(); await nextFrame(); await nextFrame();
  };

  const removeOldWidgetScripts = (keepVersion) => {
    document.querySelectorAll('script[maxmenu-script]').forEach(s => {
      if (s.getAttribute('data-mm-version') !== keepVersion) s.remove();
    });
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

  const loadWidget = (version) => {
    const script = document.createElement('script');
    script.src = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');
    script.setAttribute('data-mm-version', version);

    // No desbloqueamos aquí: esperamos a ver DOM nuevo (o evento) para ocultar skeleton.
    script.addEventListener('load', () => {
      // Fallbacks suaves por si el widget tarda en pintar tras cargar el script
      const tryHide = () => {
        if (!awaitingFinalPaint && !lockSkeleton && (container.offsetHeight > 0 || container.querySelector('*'))) {
          hideSkeleton();
        }
      };
      setTimeout(tryHide, 80);
      setTimeout(tryHide, 220);
      setTimeout(tryHide, 600);
    });

    document.head.appendChild(script);
  };

  const hotSwapTo = async (nextVersion) => {
    if (!nextVersion || nextVersion === currentVersion) return;
    console.log(`[MaxMenu] 🔄 Hot-swap → ${currentVersion} → ${nextVersion}`);

    // 1) Garantiza skeleton pintado ANTES de tocar scripts/DOM
    await ensureOverlayVisible();

    // 2) Marcamos que esperamos la pintura final (mantiene skeleton sí o sí)
    awaitingFinalPaint = true;
    lockSkeleton = true;        // asegura que nadie oculte el skeleton aún
    skeletonHidden = false;     // por si venimos de otro ciclo

    // 3) Carga la nueva versión SIN limpiar aún el contenedor (evita frame blanco)
    currentVersion = nextVersion;
    localStorage.setItem(KEY_STORAGE_VERSION, nextVersion);
    loadWidget(nextVersion);
  };

  // === Observadores para detectar pintura real y ocultar skeleton de forma atómica ===
  const finalizeIfPainted = () => {
    // Sólo cerramos el ciclo cuando vemos DOM real y altura > 0
    if (awaitingFinalPaint && (container.offsetHeight > 0 || container.querySelector('*'))) {
      awaitingFinalPaint = false;
      lockSkeleton = false;      // ya puede ocultarse
      hideSkeleton();            // oculta ahora
      removeOldWidgetScripts(currentVersion); // limpieza de scripts antiguos
    }
  };

  const observer = new MutationObserver(finalizeIfPainted);
  observer.observe(container, { childList: true, subtree: true });

  window.addEventListener('MaxMenuReady', finalizeIfPainted);

  // === Primer render (version.json) ===
  loadWidget(currentVersion);

  // === Resolver latest y decidir ===
  const latestVersion = await latestPromise;

  if (latestVersion && latestVersion !== currentVersion) {
    // Mismatch: mantenemos skeleton TODO el tiempo hasta que pinte la latest
    await hotSwapTo(latestVersion);
  } else {
    // Sin mismatch: desbloquear y ocultar cuando esté pintado el primer render
    lockSkeleton = false;
    // Si ya hay DOM real, ocúltalo; si no, lo hará el observer/ready.
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
