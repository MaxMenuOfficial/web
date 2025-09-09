
/* MaxMenu Skeleton v2 — layout: plataformas + bandera + lista de categorías */
(function () {
  if (window.MaxMenuSkeleton) return;

  function css(ns){return `
#${ns}-root{all:initial;display:block;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Arial}
#${ns}-wrap{max-width:720px;margin:0 auto;padding:12px;display:flex;flex-direction:column;align-items:center;gap:24px}
.${ns}-shimmer{position:relative;overflow:hidden;background:#eee}
@media (prefers-color-scheme:dark){.${ns}-shimmer{background:#1f1f1f}}
.${ns}-shimmer::after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.35) 45%,rgba(255,255,255,.35) 55%,transparent 100%);transform:translateX(-100%);animation:${ns}-shim 1.25s infinite;opacity:.6}
@keyframes ${ns}-shim{100%{transform:translateX(100%)}}
/* fila de plataformas */
.${ns}-platforms{display:flex;gap:16px;}
.${ns}-badge{width:120px;height:40px;border-radius:8px}
/* bandera circular */
.${ns}-flag{width:64px;height:64px;border-radius:50%}
/* categorías: mismo ancho/ritmo que tus botones */
.${ns}-list{width:100%;display:flex;flex-direction:column;gap:14px;padding:0 12px;box-sizing:border-box}
.${ns}-cat{height:84px;border-radius:12px;border:1.5px solid rgba(255,0,0,.45);position:relative;background:transparent}
.${ns}-cat::before{content:"";position:absolute;inset:8px;border-radius:10px;background:#eee;}
@media (prefers-color-scheme:dark){.${ns}-cat::before{background:#1f1f1f}}
.${ns}-cat.${ns}-shimmer::after{border-radius:10px}
/* error */
.${ns}-err{display:none;color:#c62828;font-size:14px}
.${ns}-err.show{display:block}
`;}

  function markup(ns, opts){
    const count = Math.max(3, Math.min(12, opts.categories||7));
    let cats=''; for (let i=0;i<count;i++) cats += `<div class="${ns}-cat ${ns}-shimmer"></div>`;
    return `
    <div id="${ns}-root">
      <style>${css(ns)}</style>
      <div id="${ns}-wrap">
        <div class="${ns}-platforms">
          <div class="${ns}-badge ${ns}-shimmer"></div>
          <div class="${ns}-badge ${ns}-shimmer"></div>
        </div>
        <div class="${ns}-flag ${ns}-shimmer" aria-hidden="true"></div>
        <div class="${ns}-list">${cats}</div>
        <div class="${ns}-err" id="${ns}-err">[MaxMenu] No se pudo cargar el menú.</div>
      </div>
    </div>`;
  }

  window.MaxMenuSkeleton = {
    mount(container, {minHeight=900, errorAfterMs=12000, categories=7} = {}){
      const ns='mmSkel-'+Math.random().toString(36).slice(2,7);
      const holder=document.createElement('div');
      holder.setAttribute('data-mm-skeleton', ns);
      holder.style.minHeight = minHeight+'px';
      holder.innerHTML = markup(ns, {categories});
      container.prepend(holder);
      const tid = setTimeout(()=>{ const e=holder.querySelector('#'+ns+'-err'); if (e) e.classList.add('show'); }, errorAfterMs);
      return { destroy(){ clearTimeout(tid); holder.remove(); }, showError(msg){ const e=holder.querySelector('#'+ns+'-err'); if(e){ e.textContent=msg||e.textContent; e.classList.add('show'); } } };
    }
  };
})();

/* Loader: latest.json -> widget.js versionado + skeleton */
(async () => {
  const container = document.getElementById('maxmenu-menuContainer');
  const restaurantId = container?.dataset?.restaurantId;
  if (!container || !restaurantId) { console.error('[MaxMenu] Falta #maxmenu-menuContainer o data-restaurant-id'); return; }

  // Skeleton inmediato (0ms)
  const skel = window.MaxMenuSkeleton.mount(container, {
    minHeight: 1000,     // ajusta al alto “fold” de tu página
    categories: 7,       // cuántos botones fantasma
    errorAfterMs: 12000
  });

  // Limpieza ligera
  container.innerHTML = '';
  document.querySelectorAll('script[maxmenu-script]').forEach(el => el.remove());

  const latestUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/latest.json`;

  // Resuelve cuando el widget real haya pintado algo
  function waitRendered(timeout = 15000) {
    return new Promise((res, rej) => {
      let done = false;
      const finish = () => { if (!done){ done = true; obs.disconnect(); window.removeEventListener('maxmenu:ready', finish); res(); } };
      const obs = new MutationObserver(() => { if (container.children.length > 0) finish(); });
      obs.observe(container, { childList: true, subtree: true });
      window.addEventListener('maxmenu:ready', finish, { once: true });
      setTimeout(() => { if (!done){ obs.disconnect(); rej(new Error('timeout')); } }, timeout);
    });
  }

  try {
    // 1) obtener versión
    const res = await fetch(latestUrl, { cache: 'no-store' });
    if (!res.ok) throw new Error(`HTTP ${res.status} al cargar latest.json`);
    const { version } = await res.json();
    if (!version) throw new Error('Campo "version" vacío en latest.json');

    // 2) inyectar widget
    const widgetUrl = `https://cdn.maxmenu.com/s/${restaurantId}/widget/${version}/widget.js`;
    await new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = widgetUrl; s.async = true;
      s.setAttribute('maxmenu-script','1');
      s.onload = resolve; s.onerror = () => reject(new Error('No cargó '+widgetUrl));
      document.head.appendChild(s);
    });

    // 3) esperar render real
    await waitRendered();
    console.log(`[MaxMenu] ✅ widget.js v${version} renderizado para ${restaurantId}`);
  } catch (err) {
    console.error('[MaxMenu] ❌', err);
    skel?.showError('[MaxMenu] No se pudo cargar el menú. Reintenta en unos segundos.');
  } finally {
    // 4) retirar skeleton cuando ya hay DOM o tras error
    setTimeout(() => skel?.destroy(), 50);
  }
})();
