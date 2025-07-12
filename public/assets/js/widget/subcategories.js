(function waitForSubcategoriesReady() {
    if (!document.getElementById('maxmenu-menuContainer')) {
      return requestAnimationFrame(waitForSubcategoriesReady);
    }
  
    // 1️⃣ Inyectar la CSS para rotación de flechas
    (function injectRotateCSS() {
      if (document.getElementById('maxmenu-rotate-style')) return;
  
      const style = document.createElement('style');
      style.id = 'maxmenu-rotate-style';
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
  
    // 2️⃣ Scroll a categoría
    window.scrollToCategory = function (categoryId) {
      const element = document.getElementById('category-' + categoryId);
      if (element) element.scrollIntoView({ behavior: 'smooth' });
    };
  
    // 3️⃣ Scroll a subcategoría
    window.scrollToSubcategory = function (categoryId, subcategoryId) {
      const element = document.getElementById('subcategory-' + subcategoryId);
      if (element) element.scrollIntoView({ behavior: 'smooth' });
    };
  
    // 4️⃣ Mostrar/Ocultar subcategorías y rotar flechas
    window.toggleSubcategories = function (categoryId) {
      const subcatDiv = document.getElementById('subcategories-' + categoryId);
      const arrow = document.getElementById('arrow-' + categoryId);
  
      if (!subcatDiv) return;
  
      const isHidden = (subcatDiv.style.display === '' || subcatDiv.style.display === 'none');
      subcatDiv.style.display = isHidden ? 'block' : 'none';
  
      if (arrow) {
        arrow.classList.add('arrow-rotate');
        arrow.classList.toggle('rotate', isHidden);
      }
    };
  
  })();