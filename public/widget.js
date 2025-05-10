(function () {
    const scripts = document.getElementsByTagName('script');
    const myScript = scripts[scripts.length - 1];

    const restaurantId = myScript?.getAttribute('data-restaurant-id');
    if (!restaurantId) {
        console.error('[MaxMenu] Falta el atributo obligatorio data-restaurant-id.');
        return;
    }

    const container = document.getElementById('maxmenu-menuContainer');
    if (!container) {
        console.error('[MaxMenu] Contenedor con ID "maxmenu-menuContainer" no encontrado.');
        return;
    }

    // Función para cargar los estilos solo una vez
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

    // Inyectar todos los estilos requeridos
    const cssFiles = [
        'https://menu.maxmenu.com/menu/styles/view-items.css',
        'https://menu.maxmenu.com/menu/styles/view-categorias.css',
        'https://menu.maxmenu.com/menu/styles/view-plataformas.css',
        'https://menu.maxmenu.com/menu/styles/view-idiomas.css',
        'https://menu.maxmenu.com/menu/styles/view-logo.css',
        'https://menu.maxmenu.com/menu/styles/view-menu.css'
    ];
    cssFiles.forEach(injectStylesheet);

    // Cargar el HTML desde menu-widget.php
    const url = `https://menu.maxmenu.com/menu-widget.php?id=${encodeURIComponent(restaurantId)}`;

    fetch(url)
        .then(res => {
            if (!res.ok) throw new Error('[MaxMenu] Error al obtener el menú embebido');
            return res.text();
        })
        .then(html => {
            container.innerHTML = html;

            // Reejecutar los <script> embebidos en el HTML recibido
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            tempDiv.querySelectorAll('script').forEach(oldScript => {
                const newScript = document.createElement('script');
                if (oldScript.src) {
                    newScript.src = oldScript.src;
                    newScript.async = false;
                } else {
                    newScript.textContent = oldScript.textContent;
                }
                document.body.appendChild(newScript);
            });
        })
        .catch(err => {
            console.error('[MaxMenu] Error al cargar el widget:', err);
        });
})();