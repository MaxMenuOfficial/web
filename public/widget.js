(function () {
    const scripts = document.getElementsByTagName('script');
    const myScript = scripts[scripts.length - 1];

    if (!myScript || !myScript.getAttribute) {
        console.error('[MaxMenu] Script no detectado correctamente.');
        return;
    }

    const restaurantId = myScript.getAttribute('data-restaurant-id');
    if (!restaurantId) {
        console.error('[MaxMenu] Falta el atributo obligatorio data-restaurant-id.');
        return;
    }

    const menuUrl = 'https://menu.maxmenu.com/menu-widget.php?id=' + encodeURIComponent(restaurantId);

    function injectStylesheet(href) {
        if (!document.querySelector(`link[href="${href}"]`)) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = href;
            link.type = 'text/css';
            link.media = 'all';
            document.head.appendChild(link);
        }
    }

    [
        'https://menu.maxmenu.com/menu/styles/view-items.css',
        'https://menu.maxmenu.com/menu/styles/view-categorias.css',
        'https://menu.maxmenu.com/menu/styles/view-plataformas.css',
        'https://menu.maxmenu.com/menu/styles/view-idiomas.css',
        'https://menu.maxmenu.com/menu/styles/view-logo.css',
        'https://menu.maxmenu.com/menu/styles/view-menu.css'
    ].forEach(injectStylesheet);

    const container = document.getElementById('maxmenu-menuContainer');
    if (!container) {
        console.error('[MaxMenu] Contenedor con ID "maxmenu-menuContainer" no encontrado.');
        return;
    }

    fetch(menuUrl)
        .then(response => {
            if (!response.ok) throw new Error('[MaxMenu] Error del servidor: ' + response.statusText);
            return response.text();
        })
        .then(html => {
            // Crear contenedor temporal
            const temp = document.createElement('div');
            temp.innerHTML = html;

            // Extraer y reinsertar los <script> uno por uno
            const scripts = temp.querySelectorAll('script');
            scripts.forEach(script => {
                const newScript = document.createElement('script');
                if (script.src) {
                    newScript.src = script.src;
                    newScript.defer = script.defer || false;
                } else {
                    newScript.textContent = script.textContent;
                }
                document.head.appendChild(newScript);
                script.remove();
            });

            // Inyectar el HTML restante (sin los scripts duplicados)
            container.innerHTML = temp.innerHTML;
        })
        .catch(error => {
            console.error('[MaxMenu] Error al cargar el menú:', error);
        });
})();