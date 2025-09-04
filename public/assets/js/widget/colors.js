
(function bootMaxMenuWidgetStyling() {
  const FONT_FALLBACKS = {
    "Cormorant SC":"serif",
    "Tangerine":"cursive",
    "Outfit":"ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif",
    "Marcellus SC":"serif",
    "Lexend Exa":"ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif"
  };
  const qsa = s => Array.prototype.slice.call(document.querySelectorAll(s));
  const setAll = (nodes, fn) => nodes.forEach(el => { try{ fn(el); }catch(_){ } });
  const px = n => (isNaN(parseInt(n,10)) ? '' : parseInt(n,10)) + 'px';
  const clamp = (v, a, b) => Math.max(a, Math.min(b, v));
  const RADIUS = { square:'0px', semi:'20px', round:'100px' };

  function ensureFonts(fams) {
    if (!fams || !fams.length) return;
    const key = fams.map(f => `${f.name}:${(f.weights||[]).join(',')}`).join('|');
    const id  = 'mmx-gf-' + btoa(key).replace(/=+$/,'');
    if (document.getElementById(id)) return;
    const qs = fams.map(f => `family=${encodeURIComponent(f.name).replace(/%20/g,'+')}%3Awght@${(f.weights||[400,600,700]).join(';')}`).join('&');
    const link = document.createElement('link');
    link.id = id; link.rel='stylesheet';
    link.href = `https://fonts.googleapis.com/css2?${qs}&display=swap`;
    document.head.appendChild(link);
  }

  function applyAll() {
    const root  = document.getElementById('maxmenu-menuContainer') || document.body;
    const conf  = window.MaxMenuConfig || {};
    const colors = conf.menuColors || {};
    const typo   = conf.menuTypography || {};
    const bord   = conf.menuBorders || {};

    // 1) Fondo: SIEMPRE transparente
    if (root) root.style.backgroundColor = 'transparent';

    // 2) Variables CSS de color (evitan negro si el JS tarda)
    if (root && root.style) {
      root.style.setProperty('--mm-title', colors.titleColor || '#ffffff');
      root.style.setProperty('--mm-body',  colors.descriptionColor || '#e6e6e6');
      root.style.setProperty('--mm-price', colors.priceColor || '#ffffff');
      root.style.setProperty('--mm-icon',  colors.iconColor || '#ffffff');
    }

    // 3) Aplica colores directos a los nodos (por si hay CSS más específico)
    setAll(qsa('.menu-title'),       el => { if (colors.titleColor) el.style.color = colors.titleColor; });
    setAll(qsa('.menu-description'), el => { if (colors.descriptionColor) el.style.color = colors.descriptionColor; });
    setAll(qsa('.menu-price'),       el => { if (colors.priceColor) el.style.color = colors.priceColor; });
    setAll(qsa('.menu-icon'),        el => {
      if (colors.iconColor) {
        el.style.color = colors.iconColor;
        el.style.borderColor = colors.iconColor;
      }
    });

    // 4) Tipografías: títulos ≠ descripciones ≠ precios
    const fontStack = f => `'${f}', ${FONT_FALLBACKS[f] || "system-ui, sans-serif"}`;

    const tFont = typo.titleFont   || 'Cormorant SC';
    const tW    = String(typo.titleWeight || 600);
    const tSize = px(clamp(parseInt(typo.titleSize||20,10), 10, 99));

    const bFont = typo.bodyFont    || 'Outfit';
    const bW    = String(typo.bodyWeight  || 400);
    const bSize = px(clamp(parseInt(typo.bodySize||15,10), 10, 99));

    const pFont = typo.priceFont   || 'Lexend Exa';
    const pW    = String(typo.priceWeight || 600);
    const pSize = px(clamp(parseInt(typo.priceSize||16,10), 10, 99));

    ensureFonts([
      {name:tFont, weights:[+tW||600]},
      {name:bFont, weights:[+bW||400]},
      {name:pFont, weights:[+pW||600]},
    ]);

    // títulos
    setAll(qsa('.menu-title'), el => {
      el.style.fontFamily = fontStack(tFont);
      el.style.fontWeight = tW;
      if (tSize) el.style.fontSize = tSize;
    });
    // descripciones + categorías + subcategorías + botón traducción
    const applyBody = el => {
      el.style.fontFamily = fontStack(bFont);
      el.style.fontWeight = bW;
      if (bSize) el.style.fontSize = bSize;
    };
    setAll(qsa('.menu-description, .nombre-categoria, .nombre-subcategoria, .translate-buttom, .translate-buttom-mmx'), applyBody);

    // precios
    setAll(qsa('.menu-price'), el => {
      el.style.fontFamily = fontStack(pFont);
      el.style.fontWeight = pW;
      if (pSize) el.style.fontSize = pSize;
    });

    // Botón de traducción: colores del botón (no del fondo global)
    setAll(qsa('.translate-buttom, .translate-buttom-mmx'), el => {
      if (colors.titleColor)       el.style.backgroundColor = colors.titleColor;
      if (colors.descriptionColor) el.style.color = colors.descriptionColor;
      el.style.transition = 'background-color .25s ease, color .25s ease, border-color .25s ease';
    });

    // 5) Bordes
    const rKey = (bord.border_style || 'round');
    const radius = RADIUS[rKey] || '0px';
    const bWidth = px(clamp(parseInt(bord.border_width ?? 2,10), 0, 20));

    // Categorías: radio + grosor
    setAll(qsa('.category-button-atajo'), el => {
      el.style.borderRadius = radius;
      el.style.borderStyle  = 'solid';
      el.style.borderWidth  = bWidth;
    });
    // Subcategorías: radio + grosor 0
    setAll(qsa('.subcategory-button-atajo'), el => {
      el.style.borderRadius = radius;
      el.style.borderStyle  = 'none';
      el.style.borderWidth  = '0px';
    });
    // Botón traducción igual que categoría
    setAll(qsa('.translate-buttom, .translate-buttom-mmx'), el => {
      el.style.borderRadius = radius;
      el.style.borderStyle  = 'solid';
      el.style.borderWidth  = bWidth;
    });
  }

  function waitReady() {
    const ok = typeof window.MaxMenuConfig !== 'undefined'
      && document.getElementById('maxmenu-menuContainer');
    if (!ok) return requestAnimationFrame(waitReady);

    // esperamos a que lleguen tipografías/colores si el fetch es asíncrono
    if (!window.MaxMenuConfig.menuColors || !window.MaxMenuConfig.menuTypography) {
      return requestAnimationFrame(waitReady);
    }

    applyAll();

    // Reaplicar al abrir modal/cambios de DOM
    const root = document.getElementById('maxmenu-menuContainer');
    if (root && typeof MutationObserver !== 'undefined') {
      const mo = new MutationObserver(() => applyAll());
      mo.observe(root, { childList:true, subtree:true, attributes:true });
    }
    const openBtn = document.getElementById('BtnTranslateMenu');
    if (openBtn) openBtn.addEventListener('click', () => requestAnimationFrame(applyAll));
  }

  requestAnimationFrame(waitReady);
})();
