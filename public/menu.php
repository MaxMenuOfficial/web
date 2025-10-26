<?php
// ================================
// 📦 CARGA DE DATOS DEL MENÚ
// ================================
require_once __DIR__ . '/../config/menu-service.php';
//require_once __DIR__ . '/../get/get_menu_visibility.php';
require_once __DIR__ . '/../get/get_restaurant_id.php';
require_once __DIR__ . '/../get/get_logo.php';
require_once __DIR__ . '/../get/get_idiomas.php'; 
require_once __DIR__ . '/../get/get_categoria.php';
require_once __DIR__ . '/../get/get_plataformas.php';
require_once __DIR__ . '/../get/get_restaurant_moneda.php';
require_once __DIR__ . '/../get/get_idiomas_for_items.php';
require_once __DIR__ . '/../get/get_simbolo_moneda.php';
require_once __DIR__ . '/../get/get_cat_and_subcat_for_item.php';
require_once __DIR__ . '/../get/get_brunch.php';
require_once __DIR__ . '/../get/get_daily_menu.php';
require_once __DIR__ . '/../get/get_traducciones.php';
require_once __DIR__ . '/../get/get_alergenos.php';
require_once __DIR__ . '/../get/get_bordes.php';
require_once __DIR__ . '/../get/get_tipografias.php';
require_once __DIR__ . '/../get/get_colors.php';


// ================================
// 🔤 CONSTRUCCIÓN DE URL DE GOOGLE FONTS
// ================================

$familias = array_unique([
  $tipografias['titleFont'],
  $tipografias['bodyFont'],
  $tipografias['priceFont'],
]);

$familiasEncoded = [];

foreach ($familias as $font) {
  $encodedFont = str_replace(' ', '+', trim($font));
  $familiasEncoded[] = "family={$encodedFont}";
}

$googleFontsUrl = 'https://fonts.googleapis.com/css2?' . implode('&', $familiasEncoded) . '&display=swap';
// Canonical
$restaurantId = strtolower($_GET['id'] ?? '');
echo '<link rel="canonical" href="https://menu.maxmenu.com/' . htmlspecialchars($restaurantId) . '" />';
?>

<!DOCTYPE html>
<html lang="es">
<head>

  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Favicon -->
  <link rel="icon" href="assets/css/menu/img/logo-app.png">

  <!-- Título -->
  <title>MaxMenu | Menu</title>

  <!-- Estilos del menú -->
  <link rel="stylesheet" href="https://menu.maxmenu.com/assets/css/menu/styles/view-items.css">
  <link rel="stylesheet" href="https://menu.maxmenu.com/assets/css/menu/styles/view-plataformas.css">
  <link rel="stylesheet" href="https://menu.maxmenu.com/assets/css/menu/styles/view-idiomas.css">
  <link rel="stylesheet" href="https://menu.maxmenu.com/assets/css/menu/styles/view-logo.css">
  <link rel="stylesheet" href="https://menu.maxmenu.com/assets/css/menu/styles/view-menu.css">

  <!-- Tipografías personalizadas del restaurante -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?= htmlspecialchars($googleFontsUrl) ?>" rel="stylesheet">

  <style>
  .js-font {
    font-family: '<?= $tipografias['titleFont'] ?>', <?= "'" . $tipografias['titleFont'] . "'" ?>;
    font-weight: <?= $tipografias['titleWeight'] ?>;
    font-size: <?= $tipografias['titleSize'] ?>px;
  }

  .js-body {
    font-family: '<?= $tipografias['bodyFont'] ?>', <?= "'" . $tipografias['bodyFont'] . "'" ?>;
    font-weight: <?= $tipografias['bodyWeight'] ?>;
    font-size: <?= $tipografias['bodySize'] ?>px;
  }

  .js-price {
    font-family: '<?= $tipografias['priceFont'] ?>', <?= "'" . $tipografias['priceFont'] . "'" ?>;
    font-weight: <?= $tipografias['priceWeight'] ?>;
    font-size: <?= $tipografias['priceSize'] ?>px;
  }
  </style>

</head>

<body id="menu-container">

    <header>
        <!-- Cabecera del sitio, como el logotipo o el nombre del restaurante -->
    </header>

    <main>

    

 
    <div class="flecha-up">
        <a class="enlace enlace-flecha" href="#BtnTranslateMenu"><img src="https://menu.maxmenu.com/assets/css/menu/img/up.png" alt=""></a>
    </div>


 
            <div class="logo-container">

                <?php if (!empty($logos) && is_array($logos)): ?>
                    <?php foreach ($logos as $logoItem): ?>
                        <div class="logo-item">
                            <?php if (!empty($logoItem['logo_url'])): ?>
                                <img src="<?php echo htmlspecialchars($logoItem['logo_url']); ?>" alt="Logo del restaurante">
                            <?php else: ?>
                             
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                   
                <?php endif; ?>
            </div>


    <div class="añadidas">
        <div class="categorias">
            <?php 
                if (!empty($plataformasExistentes)):
                    foreach ($plataformasExistentes as $plataforma): ?>
                    <div class="categoria">
                        <a href="<?php echo htmlspecialchars($plataforma['platform_url']); ?>" 
                           class="visitar-btn <?php echo strtolower(htmlspecialchars($plataforma['platform_name'])); ?>" 
                        ></a>
                    </div>
                    <?php endforeach;
                else: ?>
                <?php endif; ?>
        </div>
    </div>



 <!-- 1) Inyección del objeto global de traducciones -->
    <script>
      // Esta variable debe inyectarse desde tu código PHP,
      // usando algo como: json_encode($allTranslations, JSON_UNESCAPED_UNICODE)
      var globalTranslations = <?php echo json_encode($allTranslations ?? [], JSON_UNESCAPED_UNICODE); ?>;
    </script>

    <!-- 2) Botón para abrir el modal de selección de idioma -->
    <div class="container-menu-buttom-translate">
        <button id="BtnTranslateMenu">
            <!-- Muestra la bandera seleccionada en sesión o, si no existe, la del idioma original -->
            <img 
                src="<?php echo htmlspecialchars($_SESSION['flag_selected'] ?? $banderaUrlOriginal, ENT_QUOTES, 'UTF-8'); ?>" 
                alt="Current Flag"
            >
        </button>
    </div>

    <!-- Modal para la selección de idioma -->
<div id="translateItemModalMenu" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close">&times;</span>
    <br><br>
    <div id="idiomasContainer">
      <!-- Botón para ver el idioma original -->
        <div class="form-flag">

        <button class="form-flag-button translate-buttom-mmx" type="button" id="BtnViewOriginal" onclick="cargarIdiomaOriginal()">
            <img class="idioma-btn-flag" src="<?php echo htmlspecialchars($banderaUrlOriginal, ENT_QUOTES, 'UTF-8'); ?>" alt="Original Flag">
            <?php echo htmlspecialchars($originalLanguageName, ENT_QUOTES, 'UTF-8'); ?>
        </button>

        </div>
      
      <!-- Botones para cambiar de idioma -->
        <?php 
            // Recorremos $languages usando los nombres correctos de las columnas: language_id, language_name, language_code, is_active
            foreach ($languages as $langRow):
            if (empty($langRow['is_active'])) { continue; }
            $languageId   = $langRow['language_id']   ?? '';
            $languageName = $langRow['language_name'] ?? '';
            $languageCode = $langRow['language_code'] ?? '';
            $banderaUrl   = $banderas[$languageCode] ?? 'menu/img/flags/default.png';
        ?>
        
        <div class="form-flag">
            <button class="idioma-btn translate-buttom-mmx" 

                    data-idioma="<?php echo htmlspecialchars($languageId, ENT_QUOTES, 'UTF-8'); ?>" 
                    data-flag="<?php echo htmlspecialchars($banderaUrl, ENT_QUOTES, 'UTF-8'); ?>">
            <img class="idioma-btn-flag" 
                src="<?php echo htmlspecialchars($banderaUrl, ENT_QUOTES, 'UTF-8'); ?>" 
                alt="<?php echo htmlspecialchars($languageName, ENT_QUOTES, 'UTF-8'); ?>">
            <?php echo htmlspecialchars($languageName, ENT_QUOTES, 'UTF-8'); ?>
            
            </button>
        </div>

      <?php endforeach; ?>
    </div>
  </div>
</div>



<!-- JavaScript para manejar el modal y traducciones -->

<?php
$originalLanguageName = $restaurantData['original_language'] ?? '';  
$originalLanguageCode = $restaurantData['language_code'] ?? '';      
$banderaUrlOriginal   = $banderas[$originalLanguageCode] ?? 'menu/img/flags/default.png';
?>
<!-- 2) Ahora inyectas las variables en JS -->
<script>
  // Estas variables ya tienen los valores correctos
  const originalFlagUrl = "<?php echo htmlspecialchars($banderaUrlOriginal, ENT_QUOTES, 'UTF-8'); ?>";
  const originalLanguageName = "<?php echo htmlspecialchars($originalLanguageName, ENT_QUOTES, 'UTF-8'); ?>";
  // o si quieres en mayúsculas:
 
</script>



<script>
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
</script>

<div class="category-shortcuts">
  <?php foreach ($estructuraMenu as $categoria): ?>
    <div class="category-shortcut">
      <!-- Botón de la categoría -->
      <div class="category-container">
        <button 
          id="category-<?php echo $categoria['category_id']; ?>-shortcut" 
          class="category-button-atajo menu-icon" 
          data-category-id="<?php echo $categoria['category_id']; ?>" 
          onclick="scrollToCategory('<?php echo $categoria['category_id']; ?>')"
        >
          <!-- Título de la categoría con data-translate -->
          <span class="nombre-categoria menu-icon" data-translate="category">
            <?php echo htmlspecialchars($categoria['category_name'], ENT_QUOTES, 'UTF-8'); ?>
          </span>
        </button>

        <?php if (!empty($categoria['subcategorias'])): ?>
          <button 
            class="subcategory-toggle" 
            data-category-id="<?php echo $categoria['category_id']; ?>" 
            onclick="toggleSubcategories('<?php echo $categoria['category_id']; ?>')"
          >
          <svg xmlns="http://www.w3.org/2000/svg" 
                height="24px" 
                viewBox="0 -960 960 960" 
                width="24px" 
                fill="currentColor" 
                class="arrow-icon-left menu-icon" 
                id="arrow-<?php echo $categoria['category_id']; ?>">
                <path d="M400-80 0-480l400-400 71 71-329 329 329 329-71 71Z"/>
          </svg>
 
          </button>
        <?php else: ?>
          <!-- Espacio reservado para mantener alineación -->
          <span class="arrow-placeholder"></span>
        <?php endif; ?>
      </div>

      <?php if (!empty($categoria['subcategorias'])): ?>
        <!-- Contenedor oculto de subcategorías -->
        <div 
          id="subcategories-<?php echo $categoria['category_id']; ?>" 
          class="subcategory-shortcuts" 
          style="display: none;"
        >
          <?php foreach ($categoria['subcategorias'] as $subcategoria): ?>
            <button 
              class="subcategory-button-atajo menu-icon" 
              data-subcategory-id="<?php echo $subcategoria['subcategory_id']; ?>" 
              onclick="scrollToSubcategory('<?php echo $categoria['category_id']; ?>','<?php echo $subcategoria['subcategory_id']; ?>')"
            >
              <!-- Título de la subcategoría con data-translate -->
              <span class="nombre-subcategoria menu-icon" data-translate="subcategory">
                <?php echo htmlspecialchars($subcategoria['subcategory_name'], ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </button>

          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<!-- Scripts para scroll, toggle de subcategorías y rotación de flecha -->
<script>
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
</script>

<?php
// ----------------------------------------------------------------------
// Contenedor principal de los ítems (y brunches)
// ----------------------------------------------------------------------

// Aseguramos que la variable $brunches existe y es un array
global $brunches;
if (!isset($brunches) || !is_array($brunches)) {
    $brunches = [];
}
?>

<div class="añadidas">
  <div id="category-carousel" class="carousel">
    <!-- Contenedor del carrusel para las categorías -->
    <div id="items-container" class="carousel-track">
      <?php foreach ($estructuraMenu as $categoria): ?>
        <div 
          id="category-<?php echo $categoria['category_id']; ?>" 
          class="carousel-category" 
          data-category-id="<?php echo $categoria['category_id']; ?>"
        >
          <!-- Título de la categoría -->
            <h3>
                <span class="nombre-categoria menu-icon" data-translate="category" data-category-id="<?php echo $categoria['category_id']; ?>">
                    <?php echo htmlspecialchars($categoria['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </h3>
          
          <div class="category-items">

          <?php
                        // 1. Filtrar los ítems de la categoría que NO tengan subcategoría
                        $itemsSinSubcategoria = [];
                        if (isset($categoria['items']) && is_array($categoria['items'])) {
                            $itemsSinSubcategoria = array_filter($categoria['items'], function($item) {
                                return empty($item['subcategory_id']);
                            });
                        }

                        // 2. Filtrar brunches para la categoría actual
                        $brunchesDeEstaCategoria = array_filter($brunches, function($b) use ($restaurantId, $categoria) {
                            return isset($b['restaurant_id'], $b['category_id']) 
                                && $b['restaurant_id'] == $restaurantId 
                                && $b['category_id'] == $categoria['category_id'];
                        });

                        $filteredDailyMenu = array_filter($dailyMenu, function ($menu) use ($restaurantId, $categoria) {
                            return isset($menu['restaurant_id'], $menu['category_id']) 
                                   && $menu['restaurant_id'] == $restaurantId 
                                   && $menu['category_id'] == $categoria['category_id'];
                        });
                        
         ?>


            <!-- Mostrar ítems sin subcategoría -->
            <?php if (!empty($itemsSinSubcategoria)): ?>
              <?php foreach ($itemsSinSubcategoria as $item): ?>
                <div class="item-container" data-item-id="<?php echo $item['item_id']; ?>">
                  <div class="item-img-texto">
                    <div class="item-info">

                      <h3 class="titulo menu-title" data-translate="item-title"><?php echo safe_output($item['title']); ?></h3>
                      <p class="descripcion menu-description" data-translate="item-description"><?php echo safe_output($item['description']); ?></p>

                      <?php if (!empty($item['allergens'])): ?>
                        
                        <div class="alergenos-items">
                        <div class="alergenos-show">
                                <?php 
                                // 📌 Determinar la carpeta de alérgenos según el diseño configurado en la BD
                                $carpetaAlergenos = "https://menu.maxmenu.com/assets/css/menu/alergenos" . intval($diseñoAlergenos); 

                                // 📌 Verificar si hay alérgenos en el ítem
                                if (!empty($item['allergens'])):
                                    $alergenos = explode(',', $item['allergens']);
                                    foreach ($alergenos as $alergeno):
                                        $alergeno_trimmed = strtolower(trim($alergeno));
                                ?>
                                        <img 
                                            src="<?php echo htmlspecialchars($carpetaAlergenos . '/' . $alergeno_trimmed . '.svg', ENT_QUOTES, 'UTF-8'); ?>" 
                                            alt="<?php echo htmlspecialchars($alergeno_trimmed, ENT_QUOTES, 'UTF-8'); ?>" 
                                            style="width: 30px; height: 30px;"
                                        >
                                <?php 
                                    endforeach; 
                                else:
                                ?>
                                  
                                <?php endif; ?>
                            </div>

                        </div>

                      <?php endif; ?>


                      <div class="item-agrupar-precios">
                        <div class="item-image-doble">
                          <?php if (!empty($item['price'])): ?>
                            <div class="item-image-simple">
                              <h3 class="menu-price" ><?php echo mostrarPrecio($item['price'], $simbolo_moneda, $moneda); ?></h3>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div> <!-- /.item-info -->
                    <div class="item-image <?php echo empty($item['image']) ? 'no-image' : ''; ?>">
                      <?php if (!empty($item['image'])): ?>
                        <img 
                             class="expandable-image"
                             src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" 
                             alt="Imagen del item" 
                             style="max-width: 100px; min-width: 100px; min-height: 100px; max-height: 100px; object-fit: cover; border-radius: 30px; margin: 5px;">
                      <?php else: ?>
                        <p></p>
                      <?php endif; ?>
                    </div>
                  </div> <!-- /.item-img-texto -->
               
                </div> <!-- /.item-container -->
              <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($brunchesDeEstaCategoria)): ?>
    <div class="brunch-section">
        <?php foreach ($brunchesDeEstaCategoria as $brunch): ?>
            <div class="item-container">
                <div class="item-img-texto">
                    <div class="item-info">
                        <!-- Horario como título -->
                        <h3 class="titulo-item menu-title">
                            <p><?php echo safe_output($brunch['horarios'] ?? 'Brunch'); ?></p>
                        </h3>

                        <!-- Descripción del brunch -->
                        <?php if (!empty($brunch['description'])): ?>
                            <p class="menu-description" ><?php echo safe_output($brunch['description']); ?></p>
                        <?php endif; ?>

                        <!-- Arrays de ítems: infusions, main_course, etc. -->
                        <?php if (!empty($brunch['infusions_items'])): ?>
                            <strong class="menu-title">Infusiones:</strong>
                            <ul>
                                <?php foreach ($brunch['infusions_items'] as $inf): ?>
                                    <li class="menu-description"><?php echo safe_output($inf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($brunch['main_course_items'])): ?>
                            <strong class="menu-title" >Plato Principal:</strong>
                            <ul>
                                <?php foreach ($brunch['main_course_items'] as $plato): ?>
                                    <li class="menu-description" ><?php echo safe_output($plato); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($brunch['bakery_items'])): ?>
                            <strong class="menu-title" >Panadería & Pastelería:</strong>
                            <ul>
                                <?php foreach ($brunch['bakery_items'] as $bakery): ?>
                                    <li class="menu-description" ><?php echo safe_output($bakery); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($brunch['drinks_items'])): ?>
                            <strong class="menu-title" >Bebidas & Cócteles:</strong>
                            <ul>
                                <?php foreach ($brunch['drinks_items'] as $drink): ?>
                                    <li class="menu-description" ><?php echo safe_output($drink); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($brunch['additional_items'])): ?>
                            <strong class="menu-title" >Adicionales:</strong>
                            <ul>
                                <?php foreach ($brunch['additional_items'] as $add): ?>
                                    <li class="menu-description"><?php echo safe_output($add); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <!-- Precio -->
                        <?php if (isset($brunch['price'])): ?>
                            <p class="menu-price"><?php echo mostrarPrecio($brunch['price'], $simbolo_moneda, $moneda); ?></p>
                        <?php endif; ?>

                     
                        <?php if (!empty($brunch['allergens'])): ?>
                           <div class="alergenos-items">
                            <div class="alergenos-show">
                                <?php 
                                // 📌 Determinar la carpeta de alérgenos según el diseño configurado en la BD
                                $carpetaAlergenos = "https://menu.maxmenu.com/assets/css/menu/alergenos" . intval($diseñoAlergenos); 

                                // 📌 Verificar si hay alérgenos en el ítem
                                if (!empty($brunch['allergens'])):
                                    $alergenos = explode(',', $brunch['allergens']);
                                    foreach ($alergenos as $alergeno):
                                        $alergeno_trimmed = strtolower(trim($alergeno));
                                ?>
                                        <img 
                                            src="<?php echo htmlspecialchars($carpetaAlergenos . '/' . $alergeno_trimmed . '.svg', ENT_QUOTES, 'UTF-8'); ?>" 
                                            alt="<?php echo htmlspecialchars($alergeno_trimmed, ENT_QUOTES, 'UTF-8'); ?>" 
                                            style="width: 30px; height: 30px;"
                                        >
                                <?php 
                                    endforeach; 
                                else:
                                ?>
                                  
                                <?php endif; ?>
                            </div>

                        </div>

                      <?php endif; ?>




                    </div> <!-- /.item-info -->

                    <!-- Imagen del brunch -->
                    <div class="item-image <?php echo empty($brunch['image_url']) ? 'no-image' : ''; ?>">
                        <?php if (!empty($brunch['image_url'])): ?>
                            <img 
                                 class="expandable-image"
                                 src="<?php echo htmlspecialchars($brunch['image_url']); ?>" 
                                 alt="Imagen del brunch" 
                                 style="max-width: 100px; min-width: 100px; min-height: 100px; max-height: 100px; object-fit: cover; border-radius: 30px; border: 0px solid white; margin: 5px;">
                        <?php else: ?>
                            <p></p>
                        <?php endif; ?>
                    </div>
                </div> <!-- /.item-img-texto -->

            
            </div> <!-- /.item-container -->
        <?php endforeach; ?>
    </div> <!-- /.brunch-section -->
<?php endif; ?>



      

<?php if (!empty($filteredDailyMenu)): ?>
  <div class="daily-menu-section">
    <?php foreach ($filteredDailyMenu as $menu): ?>
      <div class="item-container">
        <div class="item-img-texto">
          <div class="item-info">
            <!-- Mostrar el schedule (horarios) como título -->
            <h3 class="titulo-item">
              <p class="menu-title"><?php echo safe_output($menu['schedules'] ?? 'Menú del Día'); ?></p>
            </h3>

            <!-- Mostrar descripción si existe (puedes tener una columna description si la usas) -->
            <?php if (!empty($menu['description'])): ?>
              <p class="menu-description" ><?php echo safe_output($menu['description']); ?></p>
            <?php endif; ?>

            <!-- Mostrar los arrays de ítems -->
            <?php if (!empty($menu['starter_options'])): ?>
              <strong class="menu-title" >Entrantes:</strong>
              <ul>
                <?php foreach ($menu['starter_options'] as $item): ?>
                  <li class="menu-description"><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($menu['main_options'])): ?>
              <strong class="menu-title" >Plato Principal:</strong>
              <ul>
                <?php foreach ($menu['main_options'] as $item): ?>
                  <li class="menu-description" ><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($menu['second_options'])): ?>
              <strong class="menu-title" >Segundo Plato:</strong>
              <ul>
                <?php foreach ($menu['second_options'] as $item): ?>
                  <li class="menu-description"><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($menu['dessert_options'])): ?>
              <strong class="menu-title">Postres:</strong>
              <ul>
                <?php foreach ($menu['dessert_options'] as $item): ?>
                  <li class="menu-description" ><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($menu['drinks_items'])): ?>
              <strong class="menu-title">Bebidas:</strong>
              <ul>
                <?php foreach ($menu['drinks_items'] as $item): ?>
                  <li class="menu-description"><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($menu['additional_items'])): ?>
              <strong class="menu-title" >Adicionales:</strong>
              <ul>
                <?php foreach ($menu['additional_items'] as $item): ?>
                  <li class="menu-description"><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <!-- Mostrar Precio -->
            <?php if (isset($menu['price'])): ?>
              <p class="menu-price" ><?php echo mostrarPrecio($menu['price'], $simbolo_moneda, $moneda); ?></p>
            <?php endif; ?>

         
              <?php if (!empty($menu['allergens'])): ?>

                           <div class="alergenos-items">
                            <div class="alergenos-show">
                                <?php 
                                // 📌 Determinar la carpeta de alérgenos según el diseño configurado en la BD
                                $carpetaAlergenos = "https://menu.maxmenu.com/assets/css/menu/alergenos" . intval($diseñoAlergenos); 

                                // 📌 Verificar si hay alérgenos en el ítem
                                if (!empty($menu['allergens'])):
                                    $alergenos = explode(',', $menu['allergens']);
                                    foreach ($alergenos as $alergeno):
                                        $alergeno_trimmed = strtolower(trim($alergeno));
                                ?>
                                        <img 
                                            src="<?php echo htmlspecialchars($carpetaAlergenos . '/' . $alergeno_trimmed . '.svg', ENT_QUOTES, 'UTF-8'); ?>" 
                                            alt="<?php echo htmlspecialchars($alergeno_trimmed, ENT_QUOTES, 'UTF-8'); ?>" 
                                            style="width: 30px; height: 30px;"
                                        >
                                <?php 
                                    endforeach; 
                                else:
                                ?>
                                  
                                <?php endif; ?>
                            </div>

                        </div>

                 <?php endif; ?>

          </div> <!-- /.item-info -->

          <!-- Imagen del Menú del Día -->
          <div class="item-image <?php echo empty($menu['image']) ? 'no-image' : ''; ?>">
            <?php if (!empty($menu['image'])): ?>
              <img 

  class="expandable-image"
  src="<?php echo htmlspecialchars($menu['image']); ?>" 
  alt="Imagen del Menú del Día" 
  style="max-width: 100px; min-width: 100px; min-height: 100px; max-height: 100px; object-fit: cover; border-radius: 30px; margin: 5px;">

            <?php else: ?>
              <p></p>
            <?php endif; ?>
          </div>
        </div> <!-- /.item-img-texto -->

       
 
      </div> <!-- /.item-container -->
    <?php endforeach; ?>
  </div> <!-- /.daily-menu-section -->

<?php endif; ?>


            <!-- 4. Mostrar ítems dentro de subcategorías -->
            <?php if (!empty($categoria['subcategorias'])): ?>
              <?php foreach ($categoria['subcategorias'] as $subcategoria): ?>

                <div 
                  id="subcategory-<?php echo $subcategoria['subcategory_id']; ?>" 
                  class="subcategory-container" 
                  data-subcategory-id="<?php echo $subcategoria['subcategory_id']; ?>"
                >
                
                  <h4>
                    <span class="nombre-subcategoria menu-icon" data-translate="subcategory" data-subcategory-id="<?php echo $subcategoria['subcategory_id']; ?>">
                        <?php echo htmlspecialchars($subcategoria['subcategory_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </h4>

                  <?php foreach ($subcategoria['items'] as $item): ?>
                    <div class="item-container" data-item-id="<?php echo $item['item_id']; ?>">
                      <div class="item-img-texto">
                        <div class="item-info">
                        <h3 class="titulo menu-title" data-translate="item-title"><?php echo safe_output($item['title']); ?></h3>
                         <p class="descripcion menu-description" data-translate="item-description"><?php echo safe_output($item['description']); ?></p>
                          <?php if (!empty($item['allergens'])): ?>
                            <div class="alergenos-items">
                            <div class="alergenos-show">
                                <?php 
                                // 📌 Determinar la carpeta de alérgenos según el diseño configurado en la BD
                                $carpetaAlergenos = "https://menu.maxmenu.com/assets/css/menu/alergenos" . intval($diseñoAlergenos); 

                                // 📌 Verificar si hay alérgenos en el ítem
                                if (!empty($item['allergens'])):
                                    $alergenos = explode(',', $item['allergens']);
                                    foreach ($alergenos as $alergeno):
                                        $alergeno_trimmed = strtolower(trim($alergeno));
                                ?>
                                        <img 
                                            src="<?php echo htmlspecialchars($carpetaAlergenos . '/' . $alergeno_trimmed . '.svg', ENT_QUOTES, 'UTF-8'); ?>" 
                                            alt="<?php echo htmlspecialchars($alergeno_trimmed, ENT_QUOTES, 'UTF-8'); ?>" 
                                            style="width: 30px; height: 30px;"
                                        >
                                <?php 
                                    endforeach; 
                                else:
                                ?>
                                  
                                <?php endif; ?>
                            </div>

                            </div>


                          <?php endif; ?>
                          <div class="item-agrupar-precios">
                            <div class="item-image-doble">
                              <?php if (!empty($item['price'])): ?>
                                <div class="item-image-simple">
                                  <h3 class="menu-price" ><?php echo mostrarPrecio($item['price'], $simbolo_moneda, $moneda); ?></h3>
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div> <!-- /.item-info -->
                        <div class="item-image <?php echo empty($item['image']) ? 'no-image' : ''; ?>">
                          <?php if (!empty($item['image'])): ?>
                            <img  class="expandable-image" src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Imagen del item" style="max-width:100px;min-width:100px;min-height:100px;max-height:100px;object-fit:cover;border-radius:30px;margin:5px;">
                          <?php else: ?>
                            <p></p>
                          <?php endif; ?>
                        </div>
                      </div> <!-- /.item-img-texto -->
                    
                    </div> <!-- /.item-container -->
                  <?php endforeach; ?>
                </div> <!-- /.subcategory-container -->
              <?php endforeach; ?>
            <?php else: ?>
              <?php 
            
              ?>
            <?php endif; ?>
          </div> <!-- /.category-items -->
        </div> <!-- /.carousel-category -->
      <?php endforeach; ?>
    </div> <!-- /.carousel-track -->
  </div> <!-- /.carousel -->
</div> <!-- /.añadidas -->
</main>







<div id="image-modal">
  <span id="image-modal-close">&times;</span>
  <img id="image-modal-content" alt="Imagen ampliada">
</div>


<script>

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
</script>










<!-- Inyectamos colores y tipografías -->
<script>
// ===== Colores =====
var menuColors = <?php echo json_encode($colores ?? [], JSON_UNESCAPED_UNICODE); ?>;

// ===== Tipografías =====
var menuTypography = <?php echo json_encode($tipografias ?? [], JSON_UNESCAPED_UNICODE); ?>;

// Pila de fallbacks por familia
var FONT_FALLBACKS = {
  "Cormorant SC": "serif",
  "Tangerine": "cursive",
  "Outfit": "ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif",
  "Marcellus SC": "serif",
  "Lexend Exa": "ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif"
};

document.addEventListener('DOMContentLoaded', function () {
  // ====== COLORES ======
  var menuContainer = document.getElementById('menu-container');
  if (menuContainer && menuColors?.backgroundColor) {
    menuContainer.style.backgroundColor = menuColors.backgroundColor;
  }

  var menuTitles = document.querySelectorAll('.menu-title');
  menuTitles.forEach(function (title) {
    if (menuColors?.titleColor) title.style.color = menuColors.titleColor;
  });

  var menuDescriptions = document.querySelectorAll('.menu-description');
  menuDescriptions.forEach(function (desc) {
    if (menuColors?.descriptionColor) desc.style.color = menuColors.descriptionColor;
  });

  var menuPrices = document.querySelectorAll('.menu-price');
  menuPrices.forEach(function (price) {
    if (menuColors?.priceColor) price.style.color = menuColors.priceColor;
  });

  var menuIcons = document.querySelectorAll('.menu-icon');
  menuIcons.forEach(function (icon) {
    if (menuColors?.iconColor) {
      icon.style.color = menuColors.iconColor;
      icon.style.borderColor = menuColors.iconColor;
    }
  });

  // ====== TIPOGRAFÍAS ======
  if (!menuTypography) return;

  function fontStack(f) {
    return "'" + f + "', " + (FONT_FALLBACKS[f] || "system-ui, sans-serif");
  }
  function px(n) {
    return (typeof n === 'number' ? n : parseInt(n, 10)) + 'px';
  }

  // Títulos
  menuTitles.forEach(function (el) {
    el.style.fontFamily = fontStack(menuTypography.titleFont || 'Cormorant SC');
    el.style.fontWeight = String(menuTypography.titleWeight || 600);
    el.style.fontSize   = px(menuTypography.titleSize || 20);
  });

  // Descripciones
  var bodyFamily = menuTypography.bodyFont || 'Outfit';
  var bodyWeight = menuTypography.bodyWeight || 400;
  var bodySize   = menuTypography.bodySize || 15;

  menuDescriptions.forEach(function (el) {
    el.style.fontFamily = fontStack(bodyFamily);
    el.style.fontWeight = String(bodyWeight);
    el.style.fontSize   = px(bodySize);
  });

  // ✅ NUEVO: Categorías → tipografía como descripción
  document.querySelectorAll('.nombre-categoria').forEach(function (el) {
    el.style.fontFamily = fontStack(bodyFamily);
    el.style.fontWeight = String(bodyWeight);
    el.style.fontSize   = px(bodySize);
  });

  // ✅ NUEVO: Subcategorías → tipografía como descripción
  document.querySelectorAll('.nombre-subcategoria').forEach(function (el) {
    el.style.fontFamily = fontStack(bodyFamily);
    el.style.fontWeight = String(bodyWeight);
    el.style.fontSize   = px(bodySize);
  });

  // Precios
  menuPrices.forEach(function (el) {
    el.style.fontFamily = fontStack(menuTypography.priceFont || 'Lexend Exa');
    el.style.fontWeight = String(menuTypography.priceWeight || 600);
    el.style.fontSize   = px(menuTypography.priceSize || 16);
  });

  // ✅ NUEVO: Botón de traducción → tipografía como descripción
  document.querySelectorAll('.translate-buttom-mmx').forEach(function (el) {
    el.style.fontFamily = fontStack(bodyFamily);
    el.style.fontWeight = String(bodyWeight);
    el.style.fontSize   = px(bodySize);
  });
});
</script>

<script>
// Bordes del menú normalizados (solo los campos que necesitamos)
var menuBorders = <?php echo json_encode([
  'border_style' => $menuBorders['border_style'] ?? 'round', // square | semi | round
  'border_width' => $menuBorders['border_width'] ?? 2        // px
], JSON_UNESCAPED_UNICODE); ?>;

document.addEventListener('DOMContentLoaded', function () {
  if (!menuBorders || typeof menuBorders !== 'object') return;

  // Mapeo estilo → border-radius
  var radiusMap    = { square: '0px', semi: '20px', round: '100px' };
  var borderRadius = radiusMap[menuBorders.border_style] || '0px';
  var borderWidth  = (parseInt(menuBorders.border_width, 10) || 0) + 'px';

  // CATEGORÍAS: aplicar radio + grosor seleccionado
  document.querySelectorAll('.category-button-atajo').forEach(function (el) {
    el.style.borderRadius = borderRadius;
    el.style.borderStyle  = 'solid';
    el.style.borderWidth  = borderWidth;
  });

  // SUBCATEGORÍAS: aplicar solo radio; grosor SIEMPRE 0px
  document.querySelectorAll('.subcategory-button-atajo').forEach(function (el) {
    el.style.borderRadius = borderRadius;
    el.style.borderWidth  = '0px'; // siempre 0
    // (opcional) el.style.borderStyle = 'none';
  });

  // ✅ NUEVO: Botón de traducción → mismo borde que categoría
  document.querySelectorAll('.translate-buttom-mmx').forEach(function (el) {
    el.style.borderRadius = borderRadius;
    el.style.borderStyle  = 'solid';
    el.style.borderWidth  = borderWidth;
  });
});
</script>


</body>
</html>















































