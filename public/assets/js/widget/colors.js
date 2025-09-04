/**
 * MaxMenu Widget Styling
 * - Colores (fondo, títulos, descripción, precios, iconos)
 * - Tipografías (títulos / descripción / precios)
 * - Bordes (categorías, subcategorías, botón de traducción)
 * Robusto a re-renders (MutationObserver) y a cargas diferidas.
 */

(function bootMaxMenuWidgetStyling() {
  // ---- helpers -------------------------------------------------------------
  const FONT_FALLBACKS = {
    "Cormorant SC": "serif",
    "Tangerine": "cursive",
    "Outfit": "ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif",
    "Marcellus SC": "serif",
    "Lexend Exa": "ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif"
  };

  const fontStack = f => `'${f}', ${FONT_FALLBACKS[f] || "system-ui, sans-serif"}`;
  const px = n => (isNaN(parseInt(n,10)) ? '' : parseInt(n,10)) + 'px';
  const clamp = (v, min, max) => Math.max(min, Math.min(max, v));

  const qsa = sel => Array.prototype.slice.call(document.querySelectorAll(sel));
  const safeSet = (nodes, setter) => nodes.forEach(el => { try { setter(el); } catch(_){} });

  // Mapeo bordes → border-radius
  const RADIUS = { square: '0px', semi: '20px', round: '100px' };

  // ---- rutina principal ----------------------------------------------------
  function applyAll() {
    const cfg = window.MaxMenuConfig || {};
    const menuColors     = cfg.menuColors     || {};
    const menuTypography = cfg.menuTypography || {};
    const menuBorders    = cfg.menuBorders    || {};

    // ===== COLORES =====
    const container = document.getElementById('maxmenu-menuContainer');
    if (container && menuColors.backgroundColor) {
      container.style.backgroundColor = menuColors.backgroundColor;
    }

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
    // (tamaños limitados 10..99)
    const titleFont   = menuTypography.titleFont   || 'Cormorant SC';
    const titleWeight = String(menuTypography.titleWeight || 600);
    const titleSize   = px(clamp(parseInt(menuTypography.titleSize||20,10), 10, 99));

    const bodyFont    = menuTypography.bodyFont    || 'Outfit';
    const bodyWeight  = String(menuTypography.bodyWeight  || 400);
    const bodySize    = px(clamp(parseInt(menuTypography.bodySize||15,10), 10, 99));

    const priceFont   = menuTypography.priceFont   || 'Lexend Exa';
    const priceWeight = String(menuTypography.priceWeight || 600);
    const priceSize   = px(clamp(parseInt(menuTypography.priceSize||16,10), 10, 99));

    // Títulos
    safeSet(qsa('.menu-title'), el => {
      el.style.fontFamily = fontStack(titleFont);
      el.style.fontWeight = titleWeight;
      if (titleSize) el.style.fontSize = titleSize;
    });

    // Descripciones
    safeSet(qsa('.menu-description'), el => {
      el.style.fontFamily = fontStack(bodyFont);
      el.style.fontWeight = bodyWeight;
      if (bodySize) el.style.fontSize = bodySize;
    });

    // Categorías / Subcategorías usan la tipografía de descripción
    safeSet(qsa('.nombre-categoria, .nombre-subcategoria'), el => {
      el.style.fontFamily = fontStack(bodyFont);
      el.style.fontWeight = bodyWeight;
      if (bodySize) el.style.fontSize = bodySize;
    });

    // Precios
    safeSet(qsa('.menu-price'), el => {
      el.style.fontFamily = fontStack(priceFont);
      el.style.fontWeight = priceWeight;
      if (priceSize) el.style.fontSize = priceSize;
    });

    // Botón de traducción (dos variantes de clase por compatibilidad)
    const styleTranslateBtn = btn => {
      // fuente como descripción
      btn.style.fontFamily  = fontStack(bodyFont);
      btn.style.fontWeight  = bodyWeight;
      if (bodySize) btn.style.fontSize = bodySize;

      // colores
      if (menuColors.titleColor)       btn.style.backgroundColor = menuColors.titleColor;
      if (menuColors.descriptionColor) btn.style.color           = menuColors.descriptionColor;

      // bordes (se ajustan también abajo con menuBorders)
      btn.style.transition = 'background-color .25s ease, color .25s ease, border-color .25s ease';
    };
    safeSet(qsa('.translate-buttom, .translate-buttom-mmx'), styleTranslateBtn);

    // ===== BORDES =====
    const styleKey   = (menuBorders.border_style || 'round');
    const borderRad  = RADIUS[styleKey] || '0px';
    const borderW    = px(clamp(parseInt(menuBorders.border_width ?? 2,10), 0, 20));

    // Categorías: radio + grosor
    safeSet(qsa('.category-button-atajo'), el => {
      el.style.borderRadius = borderRad;
      el.style.borderStyle  = 'solid';
      el.style.borderWidth  = borderW;
    });

    // Subcategorías: radio + grosor 0
    safeSet(qsa('.subcategory-button-atajo'), el => {
      el.style.borderRadius = borderRad;
      el.style.borderStyle  = 'none';
      el.style.borderWidth  = '0px';
    });

    // Botón de traducción: mismo borde que categoría
    safeSet(qsa('.translate-buttom, .translate-buttom-mmx'), el => {
      el.style.borderRadius = borderRad;
      el.style.borderStyle  = 'solid';
      el.style.borderWidth  = borderW;
    });
  }

  // ---- esperar config + DOM ------------------------------------------------
  function waitReady() {
    if (
      typeof window.MaxMenuConfig === 'undefined' ||
      !document.getElementById('maxmenu-menuContainer')
    ) {
      return requestAnimationFrame(waitReady);
    }
    // si config existe pero aún no trajo colores/tipografías, volvemos a intentar
    if (!window.MaxMenuConfig.menuColors) {
      return requestAnimationFrame(waitReady);
    }
    applyAll();

    // Re-aplicar en re-renders dinámicos (modal, cambios de idioma, etc.)
    const root = document.getElementById('maxmenu-menuContainer');
    if (root && typeof MutationObserver !== 'undefined') {
      const mo = new MutationObserver(() => applyAll());
      mo.observe(root, { childList:true, subtree:true, attributes:true });
    }

    // Por si el botón abre contenido diferido
    const openBtn = document.getElementById('BtnTranslateMenu');
    if (openBtn) openBtn.addEventListener('click', () => requestAnimationFrame(applyAll));
  }

  requestAnimationFrame(waitReady);
})();


