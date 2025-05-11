// 1) Inyectar la CSS para la rotación dentro del propio JavaScript
(function injectRotateCSS() {
    var style = document.createElement('style');
    style.textContent = `
        .arrow-rotate {
            transition: transform 0.3s ease;
        }
        .arrow-rotate.rotate {
            transform: rotate(-90deg);
        }
    `;
    document.head.appendChild(style);
})();

// 2) Funciones de scroll
function scrollToCategory(categoryId) {
    var element = document.getElementById('category-' + categoryId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

function scrollToSubcategory(categoryId, subcategoryId) {
    var element = document.getElementById('subcategory-' + subcategoryId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

// 3) Toggle de subcategorías + rotación de flecha
function toggleSubcategories(categoryId) {
    var subcatDiv = document.getElementById('subcategories-' + categoryId);
    var arrow = document.getElementById('arrow-' + categoryId); // flecha con id="arrow-categoriaID"
    
    if (subcatDiv) {
        // Mostrar u ocultar
        var isHidden = (subcatDiv.style.display === 'none' || subcatDiv.style.display === '');
        subcatDiv.style.display = isHidden ? 'block' : 'none';
        
        // Rotar flecha si existe
        if (arrow) {
            // Aseguramos que la flecha tenga la clase base
            arrow.classList.add('arrow-rotate');
            // Si se despliega, rotamos; si se oculta, desrotamos
            if (isHidden) {
                arrow.classList.add('rotate');
            } else {
                arrow.classList.remove('rotate');
            }
        }
    }
}

