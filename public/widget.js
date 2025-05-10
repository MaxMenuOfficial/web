(function () {
    const scripts = document.getElementsByTagName('script');
    const myScript = scripts[scripts.length - 1];

    const restaurantId = myScript.getAttribute('data-restaurant-id');
    if (!restaurantId) {
        console.error('[MaxMenu] Falta el atributo obligatorio data-restaurant-id.');
        return;
    }

    const menuUrl = 'https://menu.maxmenu.com/menu-widget.php?id=' + encodeURIComponent(restaurantId);

    const container = document.getElementById('maxmenu-menuContainer');
    if (!container) {
        console.error('[MaxMenu] Contenedor con ID "maxmenu-menuContainer" no encontrado.');
        return;
    }

    const stylesheets = [
        'https://menu.maxmenu.com/menu/styles/view-items.css',
        'https://menu.maxmenu.com/menu/styles/view-categorias.css',
        'https://menu.maxmenu.com/menu/styles/view-plataformas.css',
        'https://menu.maxmenu.com/menu/styles/view-idiomas.css',
        'https://menu.maxmenu.com/menu/styles/view-logo.css',
        'https://menu.maxmenu.com/menu/styles/view-menu.css'
    ];

    stylesheets.forEach(href => {
        if (!document.querySelector(`link[href="${href}"]`)) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = href;
            document.head.appendChild(link);
        }
    });

    fetch(menuUrl)
        .then(res => res.text())
        .then(html => {
            const temp = document.createElement('div');
            temp.innerHTML = html;

            // Extraer los scripts antes de borrar el innerHTML
            const scripts = temp.querySelectorAll('script');
            const scriptContents = [];

            scripts.forEach(script => {
                if (script.src) {
                    const s = document.createElement('script');
                    s.src = script.src;
                    s.defer = true;
                    document.body.appendChild(s);
                } else {
                    scriptContents.push(script.textContent);
                }
                script.remove();
            });

            // Insertar el HTML visual
            container.innerHTML = temp.innerHTML;

            // Ejecutar scripts inline manualmente
            scriptContents.forEach(code => {
                try {
                    const s = document.createElement('script');
                    s.textContent = code;
                    document.body.appendChild(s);
                } catch (e) {
                    console.error('[MaxMenu] Error ejecutando script inline:', e);
                }
            });
        })
        .catch(err => {
            console.error('[MaxMenu] Error cargando widget:', err);
        });
})();

