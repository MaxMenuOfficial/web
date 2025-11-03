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
  // Altura inicial para evitar micro-salto antes de medir
  spacer.style.height = '60vh';
  requestAnimationFrame(() => {
    const sk = overlay.querySelector('#maxmenu-skeleton');
    spacer.style.height = sk.offsetHeight ? `${sk.offsetHeight}px` : '60vh';
  });

  // === VERSIONING (optimistic-first) ===
  const KEY_STORAGE_VERSION = `mmx_last_version_${restaurantId}`;
  const fallbackVersion = '__VERSION__';
  let currentVersion = localStorage.getItem(KEY_STORAGE_VERSION) || fallbackVersion;

  // 1) Tomamos version.json (cacheado) para montar YA
  try {
    const vRes = await fetch(`https://cdn.maxmenu.com/s/${restaurantId}/widget/${currentVersion}/version.json`, { cache: 'force-cache' });
    if (vRes.ok) {
      const vData = await vRes.json();
      if (vData.version) currentVersion = vData.version;
    }
  } catch {}

  // 2) En paralelo pedimos latest.json (no-store), pero SIN bloquear el primer render
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

  // === Helpers de montaje y swap ===
  const removeExistingWidgetScripts = () => {
    document.querySelectorAll('script[maxmenu-script]').forEach(s => s.remove());
  };

  const loadWidget = (version) => {
    // Inyecta el widget de la versión indicada
    const script = document.createElement('script');
    script.src = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    script.async = true;
    script.setAttribute('maxmenu-script', 'true');

    // Fallback por si el widget no emite evento
    script.addEventListener('load', () => {
      setTimeout(() => {
        if (container.offsetHeight > 0 && container.querySelector('*')) {
          hideSkeleton();
        }
      }, 50);
    });

    document.head.appendChild(script);
  };

  const showSkeleton = () => {
    // Re-skeletonizar sin parpadeo, ocupando el alto actual del menú
    spacer.style.height = `${container.offsetHeight || spacer.offsetHeight || 0}px`;
    overlay.style.display = 'block';
    // forzar reflow antes de cambiar opacity
    void overlay.offsetHeight;
    overlay.style.opacity = '1';
  };

  const hideSkeleton = () => {
    // Ocultamos (no removemos) para poder reusarlo en hot-swap
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        overlay.style.opacity = '0';
        setTimeout(() => {
          overlay.style.display = 'none';
          spacer.style.height = '0px'; // ahora el alto lo manda el menú real
        }, 350);
      });
    });
  };

  const hotSwapTo = async (nextVersion) => {
    if (!nextVersion || nextVersion === currentVersion) return;
    console.log(`[MaxMenu] 🔄 Hot-swap → ${currentVersion} → ${nextVersion}`);

    // 1) Reaparece skeleton encima, ocupando el alto actual del menú
    showSkeleton();

    // 2) Limpiamos el contenedor y los scripts del widget anterior
    removeExistingWidgetScripts();
    container.innerHTML = '';

    // 3) Montamos la nueva versión sin recargar
    currentVersion = nextVersion;
    localStorage.setItem(KEY_STORAGE_VERSION, nextVersion);
    loadWidget(nextVersion);
  };

  // === Observadores para quitar skeleton cuando el DOM real exista ===
  const removeSkeletonIfPainted = () => {
    if (container.offsetHeight > 0 && container.querySelector('*')) hideSkeleton();
  };

  const observer = new MutationObserver(() => removeSkeletonIfPainted());
  observer.observe(container, { childList: true, subtree: true });

  const onReady = () => {
    hideSkeleton();
  };
  window.addEventListener('MaxMenuReady', onReady);

  // === 1er render inmediato (version.json cacheado) ===
  loadWidget(currentVersion);

  // === Si latest.json difiere, hot-swap in-place (sin reload) ===
  const latestVersion = await latestPromise;
  if (latestVersion && latestVersion !== currentVersion) {
    await hotSwapTo(latestVersion);
  }

  // Seguridad: si a los 12s no hay nada, atenuamos skeleton (no flash)
  setTimeout(() => {
    if (!(container.offsetHeight > 0 && container.querySelector('*'))) {
      overlay.style.opacity = '0.4';
    }
  }, 12000);
})();
