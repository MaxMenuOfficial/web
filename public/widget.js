// 📦 File: widget.js – Cargador embebido universal de MaxMenu
(function () {
    // 1. Detectar el script actual (para obtener el data-restaurant-id)
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

    // 2. Inyectar los estilos necesarios al <head>
    function injectStylesheet(href) {
        if (!document.querySelector(`link[href="${href}"]`)) {
            var link = document.createElement('link');
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

    // 3. Obtener el contenedor destino
    var container = document.getElementById('maxmenu-menuContainer');
    if (!container) {
        console.error('[MaxMenu] Contenedor con ID "maxmenu-menuContainer" no encontrado.');
        return;
    }

    // 4. Cargar el HTML del menú desde el backend
    fetch(menuUrl)
        .then(response => {
            if (!response.ok) throw new Error('[MaxMenu] Error del servidor: ' + response.statusText);
            return response.text();
        })
        .then(html => {
            // Crear un contenedor temporal para parsear los scripts
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            // Extraer y ejecutar los <script> manualmente
            var scripts = tempDiv.querySelectorAll('script');
            scripts.forEach(script => {
                var newScript = document.createElement('script');
                if (script.src) {
                    newScript.src = script.src;
                    newScript.defer = script.defer || false;
                } else {
                    newScript.textContent = script.textContent;
                }
                document.head.appendChild(newScript);
                script.remove();
            });

            // Inyectar el resto del contenido (sin duplicar los scripts)
            container.innerHTML = tempDiv.innerHTML;
        })
        .catch(error => {
            console.error('[MaxMenu] Error al cargar el menú:', error);
        });
})();
