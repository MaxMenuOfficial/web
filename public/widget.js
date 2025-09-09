
/* MaxMenu Skeleton v5 — Overlay atómico: sin flicker, todo gris hasta ready */
(function () {
  if (window.MaxMenuSkeleton) return;

  function css(ns){return `
/* host reserva espacio sin afectar layout del sitio */
#${ns}-host{all:initial;display:block;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Arial}
#${ns}-spacer{display:block;min-height:700px} /* evita CLS, ajustable */

/* Overlay absoluto por encima del host */
#${ns}-overlay{
  position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; gap:12px;
  padding:8px; pointer-events:none;
}

/* CÍRCULO gris 50x50 */
.${ns}-lang{width:50px;height:50px;border-radius:50%;background:#eaeaea}

/* CATEGORÍAS en columna, 250px centrado, gris claro */
.${ns}-cats{display:flex;flex-direction:column;align-items:center;gap:12px;width:100%}
.${ns}-cat{width:250px;height:72px;background:#eaeaea;border-radius:14px}

/* Dark mode consistente */
@media (prefers-color-scheme:dark){
  .${ns}-lang{background:#2b2b2b}
  .${ns}-cat{background:#2b2b2b}
}

/* Mensaje de error opcional */
.${ns}-err{display:none;color:#c62828;font-size:13px;margin-top:8px}
.${ns}-err.show{display:block}
`; }

  function overlayMarkup(ns, catsCount){
    let cats=''; for (let i=0;i<catsCount;i++) cats += `<div class="${ns}-cat" aria-hidden="true"></div>`;
    return `
      <style>${css(ns)}</style>
      <div id="${ns}-spacer"></div>
      <div id="${ns}-overlay" aria-hidden="true">
        <div class="${ns}-lang"></div>
        <div class="${ns}-cats">${cats}</div>
        <div class="${ns}-err" id="${ns}-err">[MaxMenu] No se pudo cargar el menú.</div>
      </div>
    `;
  }

  window.MaxMenuSkeleton = {
    /**
     * Monta overlay dentro de container sin vaciarlo; crea host pos:relative.
     * Devuelve handlers para ocultar overlay sin flicker.
     */
    mount(container, {minHeight=700, categories=7, errorAfterMs=10000} = {}){
      const ns='mmSkel-'+Math.random().toString(36).slice(2,7);

      // Asegura stacking correcto
      const prevPos = getComputedStyle(container).position;
      if (prevPos === 'static' || !prevPos) container.style.position = 'relative';

      // Host interno donde colocamos spacer + overlay y un live mount para el widget
      const host = document.createElement('div');
      host.id = `${ns}-host`;
      host.style.position = 'relative';
      host.style.width = '100%';

      // Capa "live" donde el widget pintará su UI, por debajo del overlay
      const live = document.createElement('div');
      live.id = `${ns}-live`;
      // visible: el overlay lo tapa; así evitamos cualquier flash
      live.style.width = '100%';

      // Overlay + spacer
      const holder = document.createElement('div');
      holder.innerHTML = overlayMarkup(ns, Math.max(7, categories));
      const spacer = holder.querySelector(`#${ns}-spacer`);
      spacer.style.minHeight = `${minHeight}px`;

      // Ensamblado: live debajo, overlay arriba
      host.appendChild(live);
      while (holder.firstChild) host.appendChild(holder.firstChild);
      container.appendChild(host);

      // Error timer
      const tid = setTimeout(()=>{
        const e = host.querySelector('#'+ns+'-err'); if (e) e.classList.add('show');
      }, errorAfterMs);

      // API
      return {
        getLiveMount(){ return live; },          // punto donde puede montar el widget si lo soporta
        showError(msg){ const e=host.querySelector('#'+ns+'-err'); if(e){ e.textContent=msg||e.textContent; e.classList.add('show'); } },
        // Swap atómico: primero aseguramos que el live está listo, luego retiramos overlay en el próximo frame
        atomicReveal(){
          const overlay = host.querySelector('#'+ns+'-overlay');
          if (!overlay) return;
          // garantizamos un frame de pintura
          requestAnimationFrame(()=>{ requestAnimationFrame(()=>{ overlay.remove(); clearTimeout(tid); }); });
        },
        destroy(){ const overlay = host.querySelector('#'+ns+'-overlay'); overlay?.remove(); clearTimeout(tid); },
        _ns: ns
      };
    }
  };
})();

/* Loader v5: no limpia el container; pinta overlay instantáneo, monta widget por debajo y hace swap sin lag */
(async () => {
  function getContainer(id='maxmenu-menuContainer'){ return document.getElementById(id); }
  async function waitForContainer(id='maxmenu-menuContainer', tmo=2000){
    const el = getContainer(id);
    if (el) return el;
    return new Promise((res, rej) => {
      const t = setTimeout(()=>rej(new Error('container timeout')), tmo);
      const obs = new MutationObserver(() => { const e=getContainer(id); if (e){ clearTimeout(t); obs.disconnect(); res(e); }});
      obs.observe(document.documentElement, { childList:true, subtree:true });
    });
  }

  const container = await waitForContainer();
  const restaurantId = container?.dataset?.restaurantId;
  if (!restaurantId) { console.error('[MaxMenu] Falta data-restaurant-id'); return; }

  // Solo eliminamos scripts previos de nuestro widget para no tocar el DOM del host
  document.querySelectorAll('script[maxmenu-script]').forEach(s => s.remove());

  // Skeleton overlay inmediato
  const skel = window.MaxMenuSkeleton.mount(container, {
    minHeight: 700,
    categories: 7,
    errorAfterMs: 10000
  });

  // Permitimos que el widget conozca un punto de montaje explícito (opcional)
  // Si tu widget lo soporta, puede leer window.__MAXMENU_MOUNT__ y montar ahí.
  window.__MAXMENU_MOUNT__ = skel.getLiveMount();

  // Carga versionada
  const latestUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`;

  // Señal de "listo": el widget debe disparar este evento cuando termina su render **completo**
  let revealed = false;
  function reveal(){ if (revealed) return; revealed = true; skel.atomicReveal(); }

  window.addEventListener('maxmenu:ready', reveal, { once: true });

  try {
    const res = await fetch(latestUrl, { cache: 'no-store' });
    if (!res.ok) throw new Error(`HTTP ${res.status} al cargar latest.json`);
    const { version } = await res.json();
    if (!version) throw new Error('Campo "version" vacío en latest.json');

    const widgetUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;

    await new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = widgetUrl; s.async = true;
      s.setAttribute('maxmenu-script','1');
      s.onload = resolve;
      s.onerror = () => reject(new Error('No cargó '+widgetUrl));
      document.head.appendChild(s);
    });

    // Fallback barato: si el widget no emite evento, detecta DOM real debajo del overlay
    const host = container.lastElementChild; // nuestro host v5
    const live = host && host.querySelector(`[id$="-live"]`);
    const t0 = performance.now();

    (function cheapCheck(){
      // Heurística: si el live tiene >0 hijos, asumimos render "completo" del widget
      if (live && live.children.length > 0) return reveal();
      if (performance.now() - t0 < 15000) requestAnimationFrame(cheapCheck);
      // si supera 15s, quedará el mensaje de error del overlay (timer interno)
    })();

    console.log(`[MaxMenu] ✅ widget.js v${version} lanzado para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌', err);
    skel?.showError('[MaxMenu] No se pudo cargar el menú. Reintenta en unos segundos.');
  }
})();
