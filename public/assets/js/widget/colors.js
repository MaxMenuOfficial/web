
(function bootMaxMenuWidgetStyling() {
  // ================== helpers ==================
  const FONT_FALLBACKS = {
    "Cormorant SC": "serif",
    "Tangerine": "cursive",
    "Outfit": "ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif",
    "Marcellus SC": "serif",
    "Lexend Exa": "ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif"
  };
  const toPx = n => (isNaN(parseInt(n,10)) ? '' : parseInt(n,10)) + 'px';
  const clamp = (v, min, max) => Math.max(min, Math.min(max, v));
  const qsa = sel => Array.prototype.slice.call(document.querySelectorAll(sel));
  const safeSet = (nodes, fn) => nodes.forEach(el => { try { fn(el); } catch(_){} });

  // Google Fonts loader (sólo si hace falta)
  const GF_NAME = f => encodeURIComponent(f).replace(/%20/g,'+');
  function ensureGoogleFontsLoaded(families) {
    // families: [{name:'Cormorant SC', weights:[400,600,700]}, ...]
    if (!families || !families.length) return;
    const key = families.map(f => `${f.name}:${(f.weights||[]).join(',')}`).join('|');
    const id  = 'mm-gfonts-' + btoa(key).replace(/=+$/,'');
    if (document.getElementById(id)) return;

    const qs = families.map(f => `family=${GF_NAME(f.name)}:wght@${(f.weights||[400]).join(';')}`).join('&');
    const href = `https://fonts.googleapis.com/css2?${qs}&display=swap`;

    const link = document.createElement('link');
    link.id   = id;
    link.rel  = 'stylesheet';
    link.href = href;
    document.head.appendChild(link);
  }

  const RADIUS = { square: '0px', semi: '20px', round: '100px' };

  // ================== main apply ==================
  function applyAll() {
    const cfg = window.MaxMenuConfig || {};
    const menuColors     = cfg.menuColors     || {};
    const menuTypography = cfg.menuTypography || {};
    const menuBorders    = cfg.menuBorders    || {};

    // --- Fondo: SIEMPRE transparente en widget ---
    const container = document.getElementById('maxmenu-menuContainer');
    if (container) container.style.backgroundColor = 'transparent';

    // --- Tipografías: cargar Google Fonts si faltan ---
    const gFamilies = [];
    const addFam = (name, w) => {
      if (!name) return;
      const weights = Array.isArray(w) ? w : (w ? [w] : []);
      // normalizamos a 400/600/700 (soportadas en el panel)
      const norm = Array.from(new Set(weights.map(n => +n).filter(x => [400,600,700].includes(x))));
      gFamilies.push({ name, weights: norm.length ? norm : [400,600,700] });
    };
    addFam(menuTypography.titleFont, menuTypography.titleWeight);
    addFam(menuTypography.bodyFont,  menuTypography.bodyWeight);
    addFam(menuTypography.priceFont, menuTypography.priceWeight);
    ensureGoogleFontsLoaded(gFamilies);

    // ===== COLORES =====
    safeSet(qsa('.menu-title'), el => {
      if (menuColors.titleColor) el.style.color = menuColors.titleColor;
    });
    safeSet(qsa('.menu-description'), el => {
      if (menuColors.descriptionColor) el.style.color = menuColors.descriptionColor;
    });
    safeSet(qsa('.menu-price'), el => {
      if (menuColors.priceColor) el.style.color = menuColors.priceColor;
    });
    safeSet(qsa('.menu-icon'), el => {
      if (menuColors.iconColor) {
        el.style.color = menuColors.iconColor;
        el.style.borderColor = menuColors.iconColor;
      }
    });

    // ===== TIPOGRAFÍAS =====
    const fontStack = f => `'${f}', ${FONT_FALLBACKS[f] || "system-ui, sans-serif"}`;

    const titleFont   = menuTypography.titleFont   || 'Cormorant SC';
    const titleWeight = String(menuTypography.titleWeight || 600);
    const titleSize   = toPx(clamp(parseInt(menuTypography.titleSize||20,10), 10, 99));

    const bodyFont    = menuTypography.bodyFont    || 'Outfit';
    const bodyWeight  = String(menuTypography.bodyWeight  || 400);
    const bodySize    = toPx(clamp(parseInt(menuTypography.bodySize||15,10), 10, 99));

    const priceFont   = menuTypography.priceFont   || 'Lexend Exa';
    const priceWeight = String(menuTypography.priceWeight || 600);
    const priceSize   = toPx(clamp(parseInt(menuTypography.priceSize||16,10), 10, 99));

    // TITULOS -> SOLO .menu-title
    safeSet(qsa('.menu-title'), el => {
      el.style.fontFamily = fontStack(titleFont);
      el.style.fontWeight = titleWeight;
      if (titleSize) el.style.fontSize = titleSize;
    });

    // DESCRIPCIONES -> SOLO .menu-description
    safeSet(qsa('.menu-description'), el => {
      el.style.fontFamily = fontStack(bodyFont);
      el.style.fontWeight = bodyWeight;
      if (bodySize) el.style.fontSize = bodySize;
    });

    // Categorías / Subcategorías usan tipografía de descripción
    safeSet(qsa('.nombre-categoria, .nombre-subcategoria'), el => {
      el.style.fontFamily = fontStack(bodyFont);
      el.style.fontWeight = bodyWeight;
      if (bodySize) el.style.fontSize = bodySize;
    });

    // PRECIOS
    safeSet(qsa('.menu-price'), el => {
      el.style.fontFamily = fontStack(priceFont);
      el.style.fontWeight = priceWeight;
      if (priceSize) el.style.fontSize = priceSize;
    });

    // Botón de traducción (ambas clases por compatibilidad)
    const styleTranslateBtn = el => {
      // fuente como descripción
      el.style.fontFamily  = fontStack(bodyFont);
      el.style.fontWeight  = bodyWeight;
      if (bodySize) el.style.fontSize = bodySize;

      // Colores (NO fondo del contenedor; sólo botón)
      if (menuColors.titleColor)       el.style.backgroundColor = menuColors.titleColor;
      if (menuColors.descriptionColor) el.style.color           = menuColors.descriptionColor;

      el.style.transition = 'background-color .25s ease, color .25s ease, border-color .25s ease';
    };
    safeSet(qsa('.translate-buttom, .translate-buttom-mmx'), styleTranslateBtn);

    // ===== BORDES =====
    const styleKey  = (menuBorders.border_style || 'round');
    const radius    = RADIUS[styleKey] || '0px';
    const widthPx   = toPx(clamp(parseInt(menuBorders.border_width ?? 2,10), 0, 20));

    // Categorías: radio + grosor
    safeSet(qsa('.category-button-atajo'), el => {
      el.style.borderRadius = radius;
      el.style.borderStyle  = 'solid';
      el.style.borderWidth  = widthPx;
    });
    // Subcategorías: radio + grosor 0
    safeSet(qsa('.subcategory-button-atajo'), el => {
      el.style.borderRadius = radius;
      el.style.borderStyle  = 'none';
      el.style.borderWidth  = '0px';
    });
    // Botón de traducción: igual que categoría
    safeSet(qsa('.translate-buttom, .translate-buttom-mmx'), el => {
      el.style.borderRadius = radius;
      el.style.borderStyle  = 'solid';
      el.style.borderWidth  = widthPx;
    });
  }

  // ================== wait for config & DOM ==================
  function waitReady() {
    const ready = typeof window.MaxMenuConfig !== 'undefined'
      && document.getElementById('maxmenu-menuContainer');
    if (!ready) return requestAnimationFrame(waitReady);

    // si aún no llegaron los colores/tipografías, seguimos esperando un frame
    if (!window.MaxMenuConfig.menuColors || !window.MaxMenuConfig.menuTypography) {
      return requestAnimationFrame(waitReady);
    }

    applyAll();

    // Re-aplicar ante re-render (modal, idioma, paginación…)
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
