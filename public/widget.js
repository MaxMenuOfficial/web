(function () {
    var scripts = document.getElementsByTagName('script');
    var myScript = scripts[scripts.length - 1];

    if (!myScript || !myScript.getAttribute) {
        console.error('[MaxMenu] Script no detectado correctamente.');
        return;
    }

    var restaurantId = myScript.getAttribute('data-restaurant-id');
    if (!restaurantId) {
        console.error('[MaxMenu] Falta el atributo obligatorio data-restaurant-id.');
        return;
    }

    var menuUrl = 'https://menu.maxmenu.com/menu-widget.php?id=' + encodeURIComponent(restaurantId);

    fetch(menuUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('[MaxMenu] Error del servidor: ' + response.statusText);
            }
            return response.text();
        })
        .then(html => {
            var container = document.getElementById('maxmenu-menuContainer');
            if (!container) {
                console.error('[MaxMenu] Contenedor con ID "maxmenu-menuContainer" no encontrado.');
                return;
            }

            container.innerHTML = '';
            container.insertAdjacentHTML('beforeend', html);

            // Cargar funcionalidades (animaciones, modals, etc.)
            var widgetJs = document.createElement('script');
            widgetJs.src = 'https://menu.maxmenu.com/assets/js/menu-widget.core.js';
            widgetJs.defer = true;
            document.head.appendChild(widgetJs);

            // Cargar estilos si aún no se han cargado
            if (!document.querySelector('link[href*="menu-widget.css"]')) {
                var css = document.createElement('link');
                css.rel = 'stylesheet';
                css.href = 'https://menu.maxmenu.com/assets/css/menu-widget.css';
                document.head.appendChild(css);
            }
        })
        .catch(error => {
            console.error('[MaxMenu] Error al cargar el menú:', error);
        });
})();