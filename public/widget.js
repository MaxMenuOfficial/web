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

  // === OVERLAY + SPACER (siempre presente hasta pintar menú) ===
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

  // === FLAGS DE CONTROL ENTRE RECARGAS ===
  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const KEY_RELOAD_GUARD   = `mmx_reload_guard_${restaurantId}`; // evita bucles
  const fallbackVersion    = '__VERSION__';
  let   currentVersion     = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  // Si venimos de una recarga forzada recientemente, no intentamos otra durante 2s
  const now = Date.now();
  const lastReload = parseInt(localStorage.getItem(KEY_RELOAD_GUARD) || '0', 10);
  const reloadCooldownMs = 2000;

  // === HELPERS SKELETON ===
  const hideSkeleton = () => {
    const skEl = document.getElementById('maxmenu-skeleton');
    if (!skEl) return;
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        skEl.style.opacity = '0';
        setTimeout(() => { spacer.style.height = '0px'; }, 350);
      });
    });
  };

  const keepSkeletonLocked = () => {
    // Mantener visible y fijar altura sobre el alto actual del contenedor (si lo hay)
    spacer.style.height = `${container.offsetHeight || spacer.offsetHeight || 0}px`;
    const skEl = document.getElementById('maxmenu-skeleton');
    if (skEl) { void skEl.offsetHeight; skEl.style.opacity = '1'; }
  };

  // === 1) version.json (cacheado) → versión operativa inmediata
  try {
    const vRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`, { cache: 'force-cache' });
    if (vRes.ok) {
      const vData = await vRes.json();
      if (vData.version) currentVersion = vData.version;
    }
  } catch {}

  // === 2) Cargar widget.js de la versión actual (pinta por debajo del skeleton)
  const loadWidget = (version) => {
    const script = document.createElement('script');
    script.src = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');
    script.setAttribute('data-mm-version', version);
    // Ocultamos skeleton solo cuando realmente hay DOM del menú visible
    script.addEventListener('load', () => {
      const tryHide = () => {
        if (container.offsetHeight > 0 || container.querySelector('*')) {
          hideSkeleton();
        }
      };
      setTimeout(tryHide, 80);
      setTimeout(tryHide, 220);
      setTimeout(tryHide, 600);
    });
    document.head.appendChild(script);
  };

  loadWidget(currentVersion);

  // === 3) latest.json (no-store) en paralelo → si difiere, RECARGA con skeleton permanente
  (async () => {
    try {
      const latestRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`, { cache: 'no-store' });
      if (!latestRes.ok) return;
      const { version: latestVersion } = await latestRes.json();

      if (latestVersion && latestVersion !== currentVersion) {
        // Mantener skeleton SIEMPRE visible
        keepSkeletonLocked();

        // Persistir next version y setear guard para evitar bucles
        localStorage.setItem(KEY_STORAGE_VERSION, latestVersion);
        if (now - lastReload > reloadCooldownMs) {
          localStorage.setItem(KEY_RELOAD_GUARD, String(Date.now()));
          // Recarga dura: el skeleton volverá a mostrarse instantáneamente al iniciar (este mismo script)
          location.reload();
        }
      }
    } catch {}
  })();

  // Seguridad: si a los 12s no hay nada, atenuamos skeleton (no flash)
  setTimeout(() => {
    if (!(container.offsetHeight > 0 || container.querySelector('*'))) {
      const skEl = document.getElementById('maxmenu-skeleton');
      if (skEl) skEl.style.opacity = '0.4';
    }
  }, 12000);

  // Aún mejor UX: si el host va a descargar, no toques nada (skeleton ya está visible)
  window.addEventListener('beforeunload', () => {
    // no ocultamos nada, dejamos el skeleton tal cual
  });
})();
