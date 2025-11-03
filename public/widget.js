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

  // === KEYS & FLAGS ===
  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const fallbackVersion     = '__VERSION__';
  let currentVersion        = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  const url = new URL(location.href);
  const qMMXV  = url.searchParams.get('mmxv');    // versión en URL
  const qSTEP  = url.searchParams.get('mmxstep'); // '1' | '2' | null

  let reloadingNow = false; // si vamos a recargar, nunca ocultamos skeleton

  const raf = () => new Promise(r => requestAnimationFrame(r));
  const keepSkeletonLocked = () => {
    spacer.style.height = `${container.offsetHeight || spacer.offsetHeight || 0}px`;
    const skEl = document.getElementById('maxmenu-skeleton');
    if (skEl) { void skEl.offsetHeight; skEl.style.opacity = '1'; }
  };
  const hideSkeleton = () => {
    if (reloadingNow) return;
    const skEl = document.getElementById('maxmenu-skeleton');
    if (!skEl) return;
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        skEl.style.opacity = '0';
        setTimeout(() => { spacer.style.height = '0px'; }, 350);
      });
    });
  };
  const hardReloadTo = async (latestVersion, step) => {
    reloadingNow = true;
    keepSkeletonLocked();
    localStorage.setItem(KEY_STORAGE_VERSION, latestVersion);
    const newUrl = new URL(location.href);
    newUrl.searchParams.set('mmxv', latestVersion);
    newUrl.searchParams.set('mmxstep', String(step)); // "1" o "2"
    await raf(); await raf(); // asegurar frame pintado con skeleton
    location.replace(newUrl.toString());
  };

  // Bust BFCache: si el navegador intenta restaurar, forzamos nueva carga
  window.addEventListener('pageshow', (e) => { if (e.persisted) location.reload(); });

  // === 1) version.json cacheado → establecemos currentVersion de trabajo
  try {
    const vRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`, { cache: 'force-cache' });
    if (vRes.ok) {
      const vData = await vRes.json();
      if (vData.version) currentVersion = vData.version;
    }
  } catch {}

  // Si la URL ya trae mmxv (post-mismatch), úsala como versión objetivo
  if (qMMXV) currentVersion = qMMXV;

  // === 2) Cargar widget de currentVersion (bajo skeleton)
  const loadWidget = (version) => {
    const script = document.createElement('script');
    script.src = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');
    script.setAttribute('data-mm-version', version);
    script.addEventListener('load', () => {
      const tryHide = () => {
        if (!reloadingNow && (container.offsetHeight > 0 || container.querySelector('*'))) {
          hideSkeleton();
        }
      };
      // varios intentos por si el script carga antes que el DOM completo
      setTimeout(tryHide, 80);
      setTimeout(tryHide, 220);
      setTimeout(tryHide, 600);
    });
    document.head.appendChild(script);
  };
  loadWidget(currentVersion);

  // === 3) latest.json en paralelo (no-store)
  (async () => {
    try {
      const latestRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`, { cache: 'no-store' });
      if (!latestRes.ok) return;
      const { version: latestVersion } = await latestRes.json();
      if (!latestVersion) return;

      // — LÓGICA DE RECARGA DOBLE —
      if (latestVersion !== currentVersion) {
        // Primer salto: mismatch detectado → recargar a step=1 con mmxv=latest
        await hardReloadTo(latestVersion, 1);
        return; // detenemos aquí
      }

      // Si ya venimos de step=1 (mmxv fijado), hacemos la segunda recarga "de confirmación"
      if (qMMXV === latestVersion && qSTEP === '1') {
        // Mantén skeleton fijado y recarga por segunda vez a step=2
        await hardReloadTo(latestVersion, 2);
        return;
      }

      // Si estamos en step=2 o sin step (todo coincide), flujo normal:
      // el skeleton se ocultará sólo cuando el menú esté pintado (tryHide)
    } catch {}
  })();

  // Seguridad: si a los 12s no hay nada, atenuamos skeleton (sin parpadeo)
  setTimeout(() => {
    if (!(container.offsetHeight > 0 || container.querySelector('*'))) {
      const skEl = document.getElementById('maxmenu-skeleton');
      if (skEl) skEl.style.opacity = '0.4';
    }
  }, 12000);

  // No tocamos el skeleton en unload: debe permanecer hasta abandonar la página
  window.addEventListener('beforeunload', () => {});
})();
