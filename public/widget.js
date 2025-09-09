/* MaxMenu Skeleton v3 — SOLO gris: círculo (50×50) + 7 placeholders de categorías (250px) */
(function () {
  if (window.MaxMenuSkeleton) return;

  function css(ns){return `
#${ns}-root{all:initial;display:block;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Arial}
#${ns}-wrap{max-width:1200px;margin:0 auto;padding:12px;display:flex;flex-direction:column;align-items:center;gap:16px}

/* CÍRCULO GRIS (idioma) */
.${ns}-lang{width:50px;height:50px;border-radius:50%;background:#e0e0e0}

/* GRID DE CATEGORÍAS EN GRIS */
.${ns}-cats{display:flex;flex-wrap:wrap;justify-content:center;gap:12px;width:100%}
.${ns}-cat{
  width:250px;            /* ancho fijo */
  min-height:72px;        /* cuerpo consistente */
  padding:10px 12px;      /* 10px arriba y abajo (y laterales suaves) */
  border-radius:14px;
  background:#e0e0e0;     /* TODO gris */
}

/* Dark Mode: sigue siendo gris (oscuro) */
@media (prefers-color-scheme:dark){
  .${ns}-lang{background:#2b2b2b}
  .${ns}-cat{background:#2b2b2b}
}

/* Error */
.${ns}-err{display:none;color:#c62828;font-size:14px}
.${ns}-err.show{display:block}
`; }

  function markup(ns, opts){
    const count = Math.max(7, Number.isFinite(opts.categories)? opts.categories : 7); // mínimo 7
    let cats=''; for (let i=0;i<count;i++) cats += `<div class="${ns}-cat" aria-hidden="true"></div>`;
    return `
    <div id="${ns}-root" aria-hidden="true">
      <style>${css(ns)}</style>
      <div id="${ns}-wrap">
        <!-- SOLO círculo gris (50x50) -->
        <div class="${ns}-lang"></div>

        <!-- Placeholders grises de categorías (mín. 7), 250px de ancho, centradas -->
        <div class="${ns}-cats">${cats}</div>

        <!-- Mensaje de error (si falla la carga del widget real) -->
        <div class="${ns}-err" id="${ns}-err">[MaxMenu] No se pudo cargar el menú.</div>
      </div>
    </div>`;
  }

  window.MaxMenuSkeleton = {
    /**
     * Monta el esqueleto gris.
     * @param {HTMLElement} container
     * @param {Object} param1
     * @param {number} param1.minHeight   Altura mínima reservada (evita CLS).
     * @param {number} param1.errorAfterMs Mostrar error tras este tiempo.
     * @param {number} param1.categories  Cantidad de placeholders (mínimo 7).
     */
    mount(container, {minHeight=900, errorAfterMs=12000, categories=7} = {}){
      const ns='mmSkel-'+Math.random().toString(36).slice(2,7);
      const holder=document.createElement('div');
      holder.setAttribute('data-mm-skeleton', ns);
      holder.style.minHeight = minHeight+'px';
      holder.innerHTML = markup(ns, {categories});
      container.prepend(holder);
      const tid = setTimeout(()=>{ const e=holder.querySelector('#'+ns+'-err'); if (e) e.classList.add('show'); }, errorAfterMs);
      return {
        destroy(){ clearTimeout(tid); holder.remove(); },
        showError(msg){
          const e=holder.querySelector('#'+ns+'-err');
          if(e){ e.textContent=msg||e.textContent; e.classList.add('show'); }
        }
      };
    }
  };
})();

/* Loader: latest.json -> widget.js versionado + skeleton gris */
(async () => {
  // Espera robusta por el contenedor (p.ej., si el host lo inyecta tarde)
  async function waitForContainer(id='maxmenu-menuContainer', tmo=5000){
    const el = document.getElementById(id);
    if (el) return el;
    return new Promise((res, rej) => {
      const t = setTimeout(()=>{ obs.disconnect(); rej(new Error('container timeout')); }, tmo);
      const obs = new MutationObserver(() => {
        const e = document.getElementById(id);
        if (e){ clearTimeout(t); obs.disconnect(); res(e); }
      });
      obs.observe(document.documentElement, { childList:true, subtree:true });
    });
  }

  const container = await waitForContainer();
  const restaurantId = container?.dataset?.restaurantId;
  if (!restaurantId) { console.error('[MaxMenu] Falta data-restaurant-id'); return; }

  // Limpia scripts previos del widget (no borres después el skeleton)
  container.innerHTML = '';
  document.querySelectorAll('script[maxmenu-script]').forEach(el => el.remove());

  // Monta el SKELETON 100% GRIS (círculo + 7 placeholders)
  const skel = window.MaxMenuSkeleton.mount(container, {
    minHeight: 1000,
    categories: 7,
    errorAfterMs: 12000
  });

  // Rutas versiónadas
  const latestUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`;

  // Espera a que el widget real haya pintado algo
  function waitRendered(timeout = 15000) {
    return new Promise((res, rej) => {
      let done = false;
      const finish = () => {
        if (!done){
          done = true;
          obs.disconnect();
          window.removeEventListener('maxmenu:ready', finish);
          res();
        }
      };
      const obs = new MutationObserver(() => {
        // Si el widget real ya añadió nodos dentro del container, damos por renderizado
        if (container.children.length > 1) finish(); // (>1 porque el skeleton ya ocupa 1)
      });
      obs.observe(container, { childList: true, subtree: true });
      window.addEventListener('maxmenu:ready', finish, { once: true });
      setTimeout(() => { if (!done){ obs.disconnect(); rej(new Error('timeout')); } }, timeout);
    });
  }

  try {
    // 1) Cargar latest.json (sin caché)
    const res = await fetch(latestUrl, { cache: 'no-store' });
    if (!res.ok) throw new Error(`HTTP ${res.status} al cargar latest.json`);
    const { version } = await res.json();
    if (!version) throw new Error('Campo "version" vacío en latest.json');

    // 2) Cargar widget.js versionado
    const widgetUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    await new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = widgetUrl; s.async = true;
      s.setAttribute('maxmenu-script','1');
      s.onload = resolve;
      s.onerror = () => reject(new Error('No cargó '+widgetUrl));
      document.head.appendChild(s);
    });

    // 3) Esperar render del widget real y log
    await waitRendered();
    console.log(`[MaxMenu] ✅ widget.js v${version} renderizado para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌', err);
    skel?.showError('[MaxMenu] No se pudo cargar el menú. Reintenta en unos segundos.');
  } finally {
    // Retira el skeleton gris cuando el widget ya esté visible
    setTimeout(() => skel?.destroy(), 50);
  }
})();