(function bootMaxMenuWidgetStyling() {
  const FONT_FALLBACKS = {
    "Cormorant SC": "serif",
    "Tangerine": "cursive",
    "Outfit": "ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif",
    "Marcellus SC": "serif",
    "Lexend Exa": "ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif"
  };

  const qsa = s => Array.prototype.slice.call(document.querySelectorAll(s));
  const setAll = (nodes, fn) => nodes.forEach(el => { try { fn(el); } catch (_) {} });
  const px = n => (isNaN(parseInt(n, 10)) ? '' : parseInt(n, 10)) + 'px';
  const clamp = (v, a, b) => Math.max(a, Math.min(b, v));
  const RADIUS = { square: '0px', semi: '20px', round: '100px' };

  function applyAll() {
    const root = document.getElementById('maxmenu-menuContainer') || document.body;
    const conf = window.MaxMenuConfig || {};
    const colors = conf.menuColors || {};
    const typo = conf.menuTypography || {};
    const bord = conf.menuBorders || {};

    // 1️⃣ Fondo transparente del contenedor
    if (root) root.style.backgroundColor = 'transparent';

    // 2️⃣ Variables CSS de color (añado --mm-bg)
    if (root && root.style) {
      root.style.setProperty('--mm-title', colors.titleColor || '#ffffff');
      root.style.setProperty('--mm-body',  colors.descriptionColor || '#e6e6e6');
      root.style.setProperty('--mm-price', colors.priceColor || '#ffffff');
      root.style.setProperty('--mm-icon',  colors.iconColor || '#ffffff');
      root.style.setProperty('--mm-bg',    colors.backgroundColor || '#000000'); // 👈 NUEVO
    }

    // 3️⃣ Aplicación de colores directos
    setAll(qsa('.menu-title'),        el => { if (colors.titleColor)       el.style.color = colors.titleColor; });
    setAll(qsa('.menu-description'),  el => { if (colors.descriptionColor) el.style.color = colors.descriptionColor; });
    setAll(qsa('.menu-price'),        el => { if (colors.priceColor)       el.style.color = colors.priceColor; });
    setAll(qsa('.menu-icon'), el => {
      if (colors.iconColor) {
        el.style.color = colors.iconColor;
        el.style.borderColor = colors.iconColor;
      }
    });

    // 4️⃣ Tipografías
    const fontStack = f => `'${f}', ${FONT_FALLBACKS[f] || "system-ui, sans-serif"}`;

    const tFont = typo.titleFont || 'Cormorant SC';
    const tW    = String(typo.titleWeight || 600);
    const tSize = px(clamp(parseInt(typo.titleSize || 20, 10), 10, 99));

    const bFont = typo.bodyFont || 'Outfit';
    const bW    = String(typo.bodyWeight || 400);
    const bSize = px(clamp(parseInt(typo.bodySize || 15, 10), 10, 99));

    const pFont = typo.priceFont || 'Lexend Exa';
    const pW    = String(typo.priceWeight || 600);
    const pSize = px(clamp(parseInt(typo.priceSize || 16, 10), 10, 99));

    setAll(qsa('.menu-title'), el => {
      el.style.fontFamily = fontStack(tFont);
      el.style.fontWeight = tW;
      if (tSize) el.style.fontSize = tSize;
    });

    const applyBody = el => {
      el.style.fontFamily = fontStack(bFont);
      el.style.fontWeight = bW;
      if (bSize) el.style.fontSize = bSize;
    };
    setAll(qsa('.menu-description, .nombre-categoria, .nombre-subcategoria, .translate-buttom, .translate-buttom-mmx'), applyBody);

    setAll(qsa('.menu-price'), el => {
      el.style.fontFamily = fontStack(pFont);
      el.style.fontWeight = pW;
      if (pSize) el.style.fontSize = pSize;
    });

    // 5️⃣ Botón de traducción — usar background del menú
    setAll(qsa('.translate-buttom, .translate-buttom-mmx'), el => {
      // Fondo = background del menú
      if (colors.backgroundColor) el.style.backgroundColor = colors.backgroundColor;

      // Texto = titleColor (mejor contraste en general). Fallbacks.
      el.style.color = colors.titleColor || colors.iconColor || '#ffffff';

      // Borde coherente con el sistema
      el.style.borderColor = colors.titleColor || colors.iconColor || '#ffffff';

      el.style.transition = 'background-color .25s ease, color .25s ease, border-color .25s ease';
    });

    // 6️⃣ Bordes
    const rKey   = bord.border_style || 'round';
    const radius = RADIUS[rKey] || '0px';
    const bWidth = px(clamp(parseInt(bord.border_width ?? 2, 10), 0, 20));

    setAll(qsa('.category-button-atajo'), el => {
      el.style.borderRadius = radius;
      el.style.borderStyle  = 'solid';
      el.style.borderWidth  = bWidth;
    });
    setAll(qsa('.subcategory-button-atajo'), el => {
      el.style.borderRadius = radius;
      el.style.borderStyle  = 'none';
      el.style.borderWidth  = '0px';
    });
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

    if (!window.MaxMenuConfig.menuColors || !window.MaxMenuConfig.menuTypography) {
      return requestAnimationFrame(waitReady);
    }

    applyAll();

    const root = document.getElementById('maxmenu-menuContainer');
    if (root && typeof MutationObserver !== 'undefined') {
      const mo = new MutationObserver(() => applyAll());
      mo.observe(root, { childList: true, subtree: true, attributes: true });
    }

    const openBtn = document.getElementById('BtnTranslateMenu');
    if (openBtn) openBtn.addEventListener('click', () => requestAnimationFrame(applyAll));
  }

  requestAnimationFrame(waitReady);
})();