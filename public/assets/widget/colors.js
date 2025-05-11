// Convertir el array PHP a un objeto JS usando json_encode
document.addEventListener('DOMContentLoaded', function() {
    // Aplicar el color de fondo al contenedor principal del menú
    var menuContainer = document.getElementById('menu-container');
    if (menuContainer) {
        menuContainer.style.backgroundColor = menuColors.backgroundColor;
    } else {
        console.warn('No se encontró el contenedor con id "menu-container".');
    }
    
    // Aplicar estilos a los elementos del menú:
    // Títulos
    var menuTitles = document.querySelectorAll('.menu-title');
    menuTitles.forEach(function(title) {
        title.style.color = menuColors.titleColor;
    });

    // Descripciones
    var menuDescriptions = document.querySelectorAll('.menu-description');
    menuDescriptions.forEach(function(desc) {
        desc.style.color = menuColors.descriptionColor;
    });

    // Precios
    var menuPrices = document.querySelectorAll('.menu-price');
    menuPrices.forEach(function(price) {
        price.style.color = menuColors.priceColor;
    });

    // Íconos
    var menuIcons = document.querySelectorAll('.menu-icon');
    menuIcons.forEach(function(icon) {
        icon.style.color = menuColors.iconColor;
        icon.style.borderColor = menuColors.iconColor;
    });
});

