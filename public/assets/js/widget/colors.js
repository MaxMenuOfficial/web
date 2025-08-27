(function waitForMaxMenuConfigAndDOM() {
  if (
    typeof window.MaxMenuConfig === 'undefined' ||
    !document.getElementById('maxmenu-menuContainer')
  ) {
    return requestAnimationFrame(waitForMaxMenuConfigAndDOM);
  }

  const { menuColors } = window.MaxMenuConfig;

  if (!menuColors) {
    // ← antes cortabas aquí; reintentamos en el siguiente frame
    return requestAnimationFrame(waitForMaxMenuConfigAndDOM);
  }

  // Helpers seguros
  const safeSet = (nodeList, setter) => {
    nodeList.forEach(el => { try { setter(el); } catch (_) {} });
  };

  // Títulos
  safeSet(document.querySelectorAll('.menu-title'), el => {
    if (menuColors.titleColor) el.style.color = menuColors.titleColor;
  });

  // Descripciones
  safeSet(document.querySelectorAll('.menu-description'), el => {
    if (menuColors.descriptionColor) el.style.color = menuColors.descriptionColor;
  });

  // Botones de traducción
  const applyTranslateButtonStyles = () => {
    safeSet(document.querySelectorAll('.translate-buttom'), btn => {
      if (menuColors.titleColor)       btn.style.backgroundColor = menuColors.titleColor;     // fondo = título
      if (menuColors.descriptionColor) btn.style.color = menuColors.descriptionColor;         // texto = descripción
      btn.style.border = `5px solid ${menuColors.titleColor || 'transparent'}`;
      btn.style.borderRadius = '50px';
      btn.style.transition = 'background-color .25s ease, color .25s ease, border-color .25s ease';
    });
  };

  applyTranslateButtonStyles();

  // Precios
  safeSet(document.querySelectorAll('.menu-price'), el => {
    if (menuColors.priceColor) el.style.color = menuColors.priceColor;
  });

  // Íconos
  safeSet(document.querySelectorAll('.menu-icon'), el => {
    if (menuColors.iconColor) {
      el.style.color = menuColors.iconColor;
      el.style.borderColor = menuColors.iconColor; // asegúrate de que tenga border-style/width en CSS
    }
  });

  // Re-aplicar estilos cuando abras el modal (por si hay re-render)
  const btnOpen = document.getElementById('BtnTranslateMenu');
  if (btnOpen) {
    btnOpen.addEventListener('click', () => {
      requestAnimationFrame(applyTranslateButtonStyles);
    });
  }
})();