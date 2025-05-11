(function waitForMaxMenuConfigAndDOM() {
    if (
      typeof window.MaxMenuConfig === 'undefined' ||
      !document.getElementById('maxmenu-menuContainer')
    ) {
      return requestAnimationFrame(waitForMaxMenuConfigAndDOM);
    }
  
    const { menuColors } = window.MaxMenuConfig;
  
    if (!menuColors) {
      console.warn('[MaxMenu] menuColors no está definido en MaxMenuConfig');
      return;
    }
  
    // 🟡 Aplicar el color de fondo al contenedor principal del menú
    const menuContainer = document.getElementById('menu-container');
    if (menuContainer) {
      menuContainer.style.backgroundColor = menuColors.backgroundColor;
    } else {
      console.warn('[MaxMenu] No se encontró el contenedor con id "menu-container".');
    }
  
    // 🔹 Aplicar color a títulos
    document.querySelectorAll('.menu-title').forEach(title => {
      title.style.color = menuColors.titleColor;
    });
  
    // 🔹 Aplicar color a descripciones
    document.querySelectorAll('.menu-description').forEach(desc => {
      desc.style.color = menuColors.descriptionColor;
    });
  
    // 🔹 Aplicar color a precios
    document.querySelectorAll('.menu-price').forEach(price => {
      price.style.color = menuColors.priceColor;
    });
  
    // 🔹 Aplicar color y borde a íconos
    document.querySelectorAll('.menu-icon').forEach(icon => {
      icon.style.color = menuColors.iconColor;
      icon.style.borderColor = menuColors.iconColor;
    });
  
  })();