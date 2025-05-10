  // Estas variables ya tienen los valores correctos
  const originalFlagUrl = "<?php echo htmlspecialchars($banderaUrlOriginal, ENT_QUOTES, 'UTF-8'); ?>";
  const originalLanguageName = "<?php echo htmlspecialchars($originalLanguageName, ENT_QUOTES, 'UTF-8'); ?>";
  // o si quieres en mayúsculas:
 





  // Modal idiomas y traducciones 

document.addEventListener('DOMContentLoaded', () => {
    const btnTranslate = document.getElementById('BtnTranslateMenu');
    const modal = document.getElementById('translateItemModalMenu');
    const closeBtn = modal.querySelector('.close');
    const btnViewOriginal = document.getElementById('BtnViewOriginal');
    

    console.log("🔹 Script cargado correctamente");

    // 📌 Guardar los textos originales al cargar la página
    document.querySelectorAll("[data-translate]").forEach(element => {
        element.setAttribute("data-original-text", element.textContent);
    });

    // 📌 Verificar si `globalTranslations` tiene datos
    console.log("🔹 globalTranslations:", globalTranslations);

    // 📌 Cargar el idioma guardado en localStorage al iniciar
    const savedLanguageId = localStorage.getItem('selectedLanguageId');
    const savedFlagUrl = localStorage.getItem('selectedFlagUrl');

    if (savedLanguageId) {
        console.log("🔹 Cargando idioma guardado:", savedLanguageId);
        cambiarIdioma(savedLanguageId, savedFlagUrl, false); // No cerrar modal
    }

    // 📌 Evento para abrir el modal
    if (btnTranslate) {
        btnTranslate.addEventListener('click', () => {
            console.log("🔹 Botón de idioma presionado: Abriendo modal");
            modal.style.display = 'block';
        });
    }

    // 📌 Evento para cerrar el modal
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            console.log("🔹 Cerrando modal de idiomas");
            modal.style.display = 'none';
        });
    }

    // 📌 Evento para restaurar el idioma original
    if (btnViewOriginal) {
        btnViewOriginal.addEventListener('click', (e) => {
            e.preventDefault();
            console.log("🔹 Botón de idioma original presionado");
            cargarIdiomaOriginal();
        });
    }

    // 📌 Delegación de eventos para botones de idioma
    document.getElementById('idiomasContainer').addEventListener('click', (e) => {
        const button = e.target.closest('.idioma-btn');
        if (button) {
            e.preventDefault();
            const selectedLanguageId = button.getAttribute('data-idioma');
            const selectedFlag = button.getAttribute('data-flag');

            console.log("🔹 Selección de idioma:", selectedLanguageId);
            cambiarIdioma(selectedLanguageId, selectedFlag, true);
        }
    });
});

/**
 * 🔥 Cambia el idioma sin recargar la página
 * @param {string} languageId - ID del idioma seleccionado
 * @param {string} flagUrl - URL de la bandera del idioma
 * @param {boolean} closeModal - Indica si debe cerrarse el modal
 */
function cambiarIdioma(languageId, flagUrl, closeModal = true) {
    console.log(`🔹 Cambiando idioma a: ${languageId}`);

    if (typeof globalTranslations !== 'undefined' && globalTranslations[languageId]) {
        const data = globalTranslations[languageId];

        // 📌 Actualizar el menú con las traducciones
        actualizarMenuConTraducciones(data);

        // 📌 Actualizar la bandera seleccionada
        const banderaElement = document.querySelector('#BtnTranslateMenu img');
        if (banderaElement && flagUrl) {
            banderaElement.src = flagUrl;
        }

        // 📌 Guardar la selección en localStorage para futuras visitas
        localStorage.setItem('selectedLanguageId', languageId);
        localStorage.setItem('selectedFlagUrl', flagUrl);

        console.log("✅ Idioma cambiado exitosamente");
    } else {
        console.error('❌ No se encontraron traducciones para el language_id:', languageId);
    }

    // 📌 Cerrar el modal si corresponde
    if (closeModal) {
        modalClose();
    }
}

/**
 * 🔄 Cierra el modal de traducción
 */
function modalClose() {
    console.log("🔹 Cerrando modal de idioma");
    document.getElementById('translateItemModalMenu').style.display = 'none';
}

/**
 * 🔄 Restaura el idioma original del menú sin recargar la página
 */
function cargarIdiomaOriginal() {
    console.log("🔹 Restaurando idioma original...");

    // Restaurar textos originales
    document.querySelectorAll("[data-translate]").forEach(element => {
        const originalText = element.getAttribute("data-original-text");
        if (originalText) {
            element.textContent = originalText;
        }
    });

    // Restaurar la bandera original y el alt
    const banderaElement = document.querySelector('#BtnTranslateMenu img');
    if (banderaElement) {
        banderaElement.src = originalFlagUrl;      // variable inyectada en JS
        banderaElement.alt = originalLanguageName; // variable inyectada en JS
    }

    // Limpiar localStorage
    localStorage.removeItem('selectedLanguageId');
    localStorage.removeItem('selectedFlagUrl');

    // Cerrar el modal
    modalClose();
    console.log("✅ Idioma original restaurado correctamente");
}
/**
 * 🔄 Aplica las traducciones al menú
 */
function actualizarMenuConTraducciones(data) {
    if (data && Array.isArray(data.categories)) {
        data.categories.forEach(category => {
            const catId = category.category_id;
            const translatedCategoryName = category.translated_category_name;

            // 📌 Actualizar el nombre de la categoría en el menú principal
            let categoryElement = document.querySelector(`[data-category-id="${catId}"][data-translate="category"]`);
            if (categoryElement) {
                categoryElement.textContent = translatedCategoryName;
            }

            // 📌 Actualizar el nombre de la categoría en el atajo de navegación
            let categoryShortcutElement = document.querySelector(`#category-${catId}-shortcut span[data-translate="category"]`);
            if (categoryShortcutElement) {
                categoryShortcutElement.textContent = translatedCategoryName;
            }

            // 📌 Actualizar los ítems dentro de la categoría
            if (Array.isArray(category.items)) {
                category.items.forEach(item => {
                    const itemId = item.item_id;
                    let itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
                    if (itemElement) {
                        let titleElement = itemElement.querySelector('.titulo');
                        let descriptionElement = itemElement.querySelector('.descripcion');
                        if (titleElement) titleElement.textContent = item.translated_title;
                        if (descriptionElement) descriptionElement.textContent = item.translated_description;
                    }
                });
            }

            // 📌 Actualizar los nombres de las subcategorías
            if (Array.isArray(category.subcategories)) {
                category.subcategories.forEach(subcat => {
                    const subcatId = subcat.subcategory_id;
                    const translatedSubcategoryName = subcat.translated_subcategory_name;

                    // Subcategoría en el menú principal
                    let subcatElement = document.querySelector(`[data-subcategory-id="${subcatId}"][data-translate="subcategory"]`);
                    if (subcatElement) {
                        subcatElement.textContent = translatedSubcategoryName;
                    }

                    // Subcategoría en los atajos
                    let subcatShortcutElement = document.querySelector(`[data-subcategory-id="${subcatId}"] span[data-translate="subcategory"]`);
                    if (subcatShortcutElement) {
                        subcatShortcutElement.textContent = translatedSubcategoryName;
                    }
                });
            }
        });
    } else {
        console.error('❌ Estructura de traducción inválida:', data);
    }
}


// Funciones de scroll y toggle
function scrollToCategory(categoryId) {
  const element = document.getElementById('category-' + categoryId);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
}

function scrollToSubcategory(categoryId, subcategoryId) {
  const element = document.getElementById('subcategory-' + subcategoryId);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
}

(function injectRotateCSS() {
  const style = document.createElement('style');
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

function toggleSubcategories(categoryId) {
  const subcatDiv = document.getElementById('subcategories-' + categoryId);
  const arrow = document.getElementById('arrow-' + categoryId);
  if (subcatDiv) {
    const isHidden = (subcatDiv.style.display === 'none' || subcatDiv.style.display === '');
    subcatDiv.style.display = isHidden ? 'block' : 'none';
    if (arrow) {
      arrow.classList.add('arrow-rotate');
      if (isHidden) {
        arrow.classList.add('rotate');
      } else {
        arrow.classList.remove('rotate');
      }
    }
  }
}













  // Modal idiomas y traducciones 

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










 // ampliar imagen de los items, menu del dia y brunch

document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("image-modal");
  const modalImg = document.getElementById("image-modal-content");
  const closeBtn = document.getElementById("image-modal-close");

  document.querySelectorAll("img.expandable-image").forEach(function (img) {
    console.log("🖼️ Imagen lista:", img.id);

    img.addEventListener("click", function () {
      console.log("🚀 Click en imagen:", this.id);
      modalImg.src = this.src;
      modal.classList.add("show");
    });
  });

  closeBtn.addEventListener("click", function () {
    modal.classList.remove("show");
    modalImg.src = "";
  });

  modal.addEventListener("click", function (event) {
    if (event.target === modal) {
      modal.classList.remove("show");
      modalImg.src = "";
    }
  });

  // Swipe to close (móviles)
  let touchStartY = 0;
  modal.addEventListener("touchstart", function (e) {
    touchStartY = e.changedTouches[0].screenY;
  });

  modal.addEventListener("touchend", function (e) {
    let touchEndY = e.changedTouches[0].screenY;
    if (touchEndY - touchStartY > 100) {
      modal.classList.remove("show");
      modalImg.src = "";
    }
  });
});







 // Inyectamos la variable PHP $colores en JavaScript 

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


