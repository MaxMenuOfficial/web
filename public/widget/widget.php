<?php

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

/* ───── PARAMETROS VÍA GET (ya añadidos por mod_rewrite) ───── */

$restaurantId = $_GET['id'] ?? null;
$version      = $_GET['version']     ?? null;

// 🛑 Validaciones básicas
if (!$restaurantId) {
    http_response_code(400);
    exit('Missing restaurant ID ');
}

if (!$version) {
  http_response_code(400);
  exit('Missing version ');
}

/* ───── DEPENDENCIAS ───── */
require_once __DIR__.'/../../config/menu-service.php';
require_once __DIR__.'/../../get/get_menu_visibility_widget.php';
require_once __DIR__.'/../../get/get_domains.php';
require_once __DIR__.'/../../get/get_logo.php';
require_once __DIR__.'/../../get/get_idiomas.php';
require_once __DIR__.'/../../get/get_categoria.php';
require_once __DIR__.'/../../get/get_plataformas.php';
require_once __DIR__.'/../../get/get_restaurant_moneda.php';
require_once __DIR__.'/../../get/get_idiomas_for_items.php';
require_once __DIR__.'/../../get/get_simbolo_moneda.php';
require_once __DIR__.'/../../get/get_cat_and_subcat_for_item.php';
require_once __DIR__.'/../../get/get_brunch.php';
require_once __DIR__.'/../../get/get_daily_menu.php';
require_once __DIR__.'/../../get/get_traducciones.php';
require_once __DIR__.'/../../get/get_alergenos.php';
require_once __DIR__.'/../../get/get_colors.php';
require_once __DIR__.'/../../get/get_bordes.php';
require_once __DIR__.'/../../get/get_tipografias.php';

?>


  <!DOCTYPE html>
  <html lang="es">
  <head>

      <meta charset="UTF-8">
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <style>
  /* Fondo SIEMPRE transparente */
      #maxmenu-menuContainer,
      .maxmenu-root, .mmx-root, body {
        background: transparent !important;
      }

  /* Variables de color con fallbacks elegantes (evitan texto negro/transparent) */
  #maxmenu-menuContainer {
    --mm-title:       #ffffff;
    --mm-body:        #e6e6e6;
    --mm-price:       #ffffff;
    --mm-icon:        #ffffff;
  }

  /* Usa variables por defecto; luego el JS las actualizará */
  .menu-title       { color: var(--mm-title) !important; }
  .menu-description { color: var(--mm-body)  !important; }
  .menu-price       { color: var(--mm-price) !important; }
  .menu-icon        { color: var(--mm-icon)  !important; border-color: var(--mm-icon) !important; }

  /* Botones/inputs suelen forzar negro en iOS; neutralizamos */
  button, .translate-buttom, .translate-buttom-mmx,
  .category-button-atajo, .subcategory-button-atajo {
    -webkit-appearance: none;
    appearance: none;
    background: transparent; /* el fondo lo seteará el JS para el botón de traducción */
    color: inherit;
  }

  /* Evita que títulos H3/H4 globales nos pongan negro */
  h1,h2,h3,h4,h5,h6 { color: inherit; }
</style>

</head>


 <div class="maxmenu-root .mmx-root">

       <div class="flecha-up maxmenu-up mmx-back-to-top" id="maxmenu-up">
          <a class="enlace enlace-flecha" href="#BtnTranslateMenu"><img class="mmx-back-to-top-link "src="https://menu.maxmenu.com/assets/css/widget/img/up.png" alt=""></a>
       </div>


            <div class="logo-container mmx-logo-container">

                <?php if (!empty($logos) && is_array($logos)): ?>
                    <?php foreach ($logos as $logoItem): ?>
                        <div id="maxmenu-logo" class="logo-item maxmenu-logo mmx-logo">
                            <?php if (!empty($logoItem['logo_url'])): ?>
                                <img  class="mmx-logo-img" src="<?php echo htmlspecialchars($logoItem['logo_url']); ?>" alt="Logo del restaurante">
                            <?php else: ?>
                             
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                   
                <?php endif; ?>
            </div>


            <div class="mmx-platforms">  <!-- NUEVO wrapper específico -->
              <div class="categorias">
                <?php if (!empty($plataformasExistentes)):
                  foreach ($plataformasExistentes as $plataforma): 
                    $name = strtolower($plataforma['platform_name']); ?>
                    <div class="categoria">
                      <a href="<?php echo htmlspecialchars($plataforma['platform_url']); ?>"
                        class="visitar-btn <?php echo htmlspecialchars($name); ?>"
                        aria-label="<?php echo htmlspecialchars($plataforma['platform_name']); ?>"
                        target="_blank" rel="noopener noreferrer"></a>
                    </div>
                <?php endforeach; endif; ?>
              </div>
            </div>

    <!-- 2) Botón para abrir el modal de selección de idioma -->
    <div class="container-menu-buttom-translate ">
        <button id="BtnTranslateMenu" >
            <!-- Muestra la bandera seleccionada en sesión o, si no existe, la del idioma original -->
           <img 
                id="maxmenu-img-flag" 
                src="<?php echo htmlspecialchars($_SESSION['flag_selected'] ?? $banderaUrlOriginal, ENT_QUOTES, 'UTF-8'); ?>" 
                alt="Current Flag"
            >
        </button>
    </div>

    <!-- Modal para la selección de idioma -->
<div id="translateItemModalMenu" class="modal " style="display:none;">
  <div id="modal-content-translate" class="modal-content mmx-lang-modal-content">
    <span id="close-btn-modal-translate .mmx-lang-close" class="close">&times;</span>
    <br><br>
    <div id="idiomasContainer" class="maxmenu-languaguesContainer " >
      <!-- Botón para ver el idioma original -->
        <div class="form-flag ">

        <button class="form-flag-button translate-buttom translate-buttom-mmx"  type="button" id="BtnViewOriginal" onclick="cargarIdiomaOriginal()">
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

            <button class="idioma-btn translate-buttom translate-buttom-mmx" 

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


<div class="category-shortcuts mmx-categories-shortcuts">
  <?php foreach ($estructuraMenu as $categoria): ?>
    <div class="category-shortcut mmx-category-shortcut">
      <!-- Botón de la categoría -->
      <div class="category-container mmx-category-shortcut-row" id="maxmenu-category-container" >
        <button 
          id="category-<?php echo $categoria['category_id']; ?>-shortcut" 
          class="category-button-atajo menu-icon " 
          data-category-id="<?php echo $categoria['category_id']; ?>" 
          onclick="scrollToCategory('<?php echo $categoria['category_id']; ?>')"
        >
          <!-- Título de la categoría con data-translate -->
          <span id="maxmenu-category-buttom" class="nombre-categoria menu-icon mmx-category-button-label" data-translate="category">
            <?php echo htmlspecialchars($categoria['category_name'], ENT_QUOTES, 'UTF-8'); ?>
          </span>
        </button>

        <?php if (!empty($categoria['subcategorias'])): ?>
          <button 
            id="maxmenu-subcategory-buttom"
            class="subcategory-toggle mmx-subcategory-toggle" 
            data-category-id="<?php echo $categoria['category_id']; ?>" 
            onclick="toggleSubcategories('<?php echo $categoria['category_id']; ?>')"
          >
          <svg xmlns="http://www.w3.org/2000/svg" 
                height="24px" 
                viewBox="0 -960 960 960" 
                width="24px" 
                fill="currentColor" 
                class="arrow-icon-left menu-icon mmx-subcategory-toggle-icon" 
                id="arrow-<?php echo $categoria['category_id']; ?>">
                <path d="M400-80 0-480l400-400 71 71-329 329 329 329-71 71Z"/>
          </svg>
 
          </button>
        <?php else: ?>
          <!-- Espacio reservado para mantener alineación -->
          <span class="arrow-placeholder mmx-subcategory-toggle-placeholder"></span>
        <?php endif; ?>
      </div>

      <?php if (!empty($categoria['subcategorias'])): ?>
        <!-- Contenedor oculto de subcategorías -->
        <div 
          id="subcategories-<?php echo $categoria['category_id']; ?>" 
          class="subcategory-shortcuts mmx-subcategory-shortcuts" 
          style="display: none;"
        >
          <?php foreach ($categoria['subcategorias'] as $subcategoria): ?>
            <button 
              id="maxmenu-subcategory-buttom" 
              class="subcategory-button-atajo menu-icon mmx-subcategory-button" 
              data-subcategory-id="<?php echo $subcategoria['subcategory_id']; ?>" 
              onclick="scrollToSubcategory('<?php echo $categoria['category_id']; ?>','<?php echo $subcategoria['subcategory_id']; ?>')"
            >
              <!-- Título de la subcategoría con data-translate -->
              <span class="nombre-subcategoria menu-icon mmx-subcategory-button-label" data-translate="subcategory">
                <?php echo htmlspecialchars($subcategoria['subcategory_name'], ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>


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

<div class="añadidas mmx-category-container">
  <div id="category-carousel" class="carousel mmx-category-section">
    <!-- Contenedor del carrusel para las categorías -->
    <div id="items-container mmx-items" class="carousel-track">
      <?php foreach ($estructuraMenu as $categoria): ?>
        <div 
          id="category-<?php echo $categoria['category_id']; ?>" 
          class="carousel-category" 
          data-category-id="<?php echo $categoria['category_id']; ?>"
        >
        
          <!-- Título de la categoría -->
            <h3 class="h3">
                <span class="nombre-categoria menu-icon mmx-category-titles" id="maxmenu-categories-name" data-translate="category" data-category-id="<?php echo $categoria['category_id']; ?>">
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
                <div class="item-container mmx-items" data-item-id="<?php echo $item['item_id']; ?>">
                  <div class="item-img-texto mmx-item">
                    <div class="item-info mmx-item-info">

                      <h3 class="titulo menu-title " data-translate="item-title"><?php echo safe_output($item['title']); ?></h3>
                      <p class="descripcion menu-description mmx-item-desc" data-translate="item-description"><?php echo safe_output($item['description']); ?></p>

                      <?php if (!empty($item['allergens'])): ?>
                        
                        <div class="alergenos-items mmx-item-allergens">
                        <div class="alergenos-show mmx-item-allergens-list">
                                <?php 
                                // 📌 Determinar la carpeta de alérgenos según el diseño configurado en la BD
                                $carpetaAlergenos = "https://menu.maxmenu.com/assets/css/menu/alergenos" . intval($diseñoAlergenos); 

                                // 📌 Verificar si hay alérgenos en el ítem
                                if (!empty($item['allergens'])):
                                    $alergenos = explode(',', $item['allergens']);
                                    foreach ($alergenos as $alergeno):
                                        $alergeno_trimmed = strtolower(trim($alergeno));
                                ?>
                                        <img class="mmx-item-allergen"
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
                            <div class="item-image-simple mmx-item-price-box">
                              <h3 class="menu-price h3 mmx-item-price" ><?php echo mostrarPrecio($item['price'], $simbolo_moneda, $moneda); ?></h3>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div> <!-- /.item-info -->
                    <div class="mmx-item-image item-image <?php echo empty($item['image']) ? 'no-image' : ''; ?>">
                      <?php if (!empty($item['image'])): ?>
                        <img 
                             class="expandable-image mmx-item-image-img"
                             src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" 
                             alt="Imagen del item" 
                             style="max-width: 100px; min-width: 100px; min-height: 100px; max-height: 100px; object-fit: cover; border-radius: 30px; ">
                      <?php else: ?>
                        <p></p>
                      <?php endif; ?>
                    </div>
                  </div> <!-- /.item-img-texto -->
               
                </div> <!-- /.item-container -->
              <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($brunchesDeEstaCategoria)): ?>
    <div class="brunch-section ">
        <?php foreach ($brunchesDeEstaCategoria as $brunch): ?>
            <div class="item-container mmx-brunch-section">
                <div class="item-img-texto mmx-brunch">
                    <div class="item-info mmx-brunch-time">
                        <!-- Horario como título -->
                        <h3 class="titulo-item menu-title h3 mmx-brunch-group-title">
                            <p><?php echo safe_output($brunch['horarios'] ?? 'Brunch'); ?></p>
                        </h3>

                        <!-- Descripción del brunch -->
                        <?php if (!empty($brunch['description'])): ?>
                            <p class="menu-description mmx-brunch-group-list" ><?php echo safe_output($brunch['description']); ?></p>
                        <?php endif; ?>

                        <!-- Arrays de ítems: infusions, main_course, etc. -->
                        <?php if (!empty($brunch['infusions_items'])): ?>
                            <strong class="menu-title mmx-brunch-group-title">Infusiones:</strong>
                            <ul>
                                <?php foreach ($brunch['infusions_items'] as $inf): ?>
                                    <li class="menu-description mmx-brunch-group-list"><?php echo safe_output($inf); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($brunch['main_course_items'])): ?>
                            <strong class="menu-title mmx-brunch-group-title" >Plato Principal:</strong>
                            <ul>
                                <?php foreach ($brunch['main_course_items'] as $plato): ?>
                                    <li class="menu-description mmx-brunch-group-list" ><?php echo safe_output($plato); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($brunch['bakery_items'])): ?>
                            <strong class="menu-title mmx-brunch-group-title" >Panadería & Pastelería:</strong>
                            <ul>
                                <?php foreach ($brunch['bakery_items'] as $bakery): ?>
                                    <li class="menu-description mmx-brunch-group-list" ><?php echo safe_output($bakery); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($brunch['drinks_items'])): ?>
                            <strong class="menu-title mmx-brunch-group-title" >Bebidas & Cócteles:</strong>
                            <ul>
                                <?php foreach ($brunch['drinks_items'] as $drink): ?>
                                    <li class="menu-description mmx-brunch-group-list" ><?php echo safe_output($drink); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($brunch['additional_items'])): ?>
                            <strong class="menu-title mmx-brunch-group-title" >Adicionales:</strong>
                            <ul>
                                <?php foreach ($brunch['additional_items'] as $add): ?>
                                    <li class="menu-description mmx-brunch-group-list"><?php echo safe_output($add); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <!-- Precio -->
                        <?php if (isset($brunch['price'])): ?>
                            <p class="menu-price mmx-brunch-group-title"><?php echo mostrarPrecio($brunch['price'], $simbolo_moneda, $moneda); ?></p>
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
  <div class="daily-menu-section mmx-dailymenu-section">
    <?php foreach ($filteredDailyMenu as $menu): ?>
      <div class="item-container mmx-dailymenu">
        <div class="item-img-texto mmx-dailymenu-time">
          <div class="item-info mmx-dailymenu-container">
            <!-- Mostrar el schedule (horarios) como título -->
            <h3 class="titulo-item h3 mmx-dailymenu-group-title">
              <p class="menu-title"><?php echo safe_output($menu['schedules'] ?? 'Menú del Día'); ?></p>
            </h3>

            <!-- Mostrar descripción si existe (puedes tener una columna description si la usas) -->
            <?php if (!empty($menu['description'])): ?>
              <p class="menu-description mmx-dailymenu-group-list " ><?php echo safe_output($menu['description']); ?></p>
            <?php endif; ?>

            <!-- Mostrar los arrays de ítems -->
            <?php if (!empty($menu['starter_options'])): ?>
              <strong class="menu-title mmx-dailymenu-group-title" >Entrantes:</strong>
              <ul>
                <?php foreach ($menu['starter_options'] as $item): ?>
                  <li class="menu-description mmx-dailymenu-group-list "><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($menu['main_options'])): ?>
              <strong class="menu-title mmx-dailymenu-group-title" >Plato Principal:</strong>
              <ul>
                <?php foreach ($menu['main_options'] as $item): ?>
                  <li class="menu-description mmx-dailymenu-group-list " ><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($menu['second_options'])): ?>
              <strong class="menu-title mmx-dailymenu-group-title" >Segundo Plato:</strong>
              <ul>
                <?php foreach ($menu['second_options'] as $item): ?>
                  <li class="menu-description mmx-dailymenu-group-list "><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($menu['dessert_options'])): ?>
              <strong class="menu-title mmx-dailymenu-group-title">Postres:</strong>
              <ul>
                <?php foreach ($menu['dessert_options'] as $item): ?>
                  <li class="menu-description mmx-dailymenu-group-list " ><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($menu['drinks_items'])): ?>
              <strong class="menu-title mmx-dailymenu-group-title">Bebidas:</strong>
              <ul>
                <?php foreach ($menu['drinks_items'] as $item): ?>
                  <li class="menu-description mmx-dailymenu-group-list "><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($menu['additional_items'])): ?>
              <strong class="menu-title mmx-dailymenu-group-title" >Adicionales:</strong>
              <ul>
                <?php foreach ($menu['additional_items'] as $item): ?>
                  <li class="menu-description mmx-dailymenu-group-list"><?php echo safe_output($item); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <!-- Mostrar Precio -->
            <?php if (isset($menu['price'])): ?>
              <p class="menu-price mmx-dailymenu-group-title" ><?php echo mostrarPrecio($menu['price'], $simbolo_moneda, $moneda); ?></p>
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
                    <span class="nombre-subcategoria menu-icon mmx-subcategory-titles" data-translate="subcategory" data-subcategory-id="<?php echo $subcategoria['subcategory_id']; ?>">
                        <?php echo htmlspecialchars($subcategoria['subcategory_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </h4>

                  <?php foreach ($subcategoria['items'] as $item): ?>
                    <div class="item-container" data-item-id="<?php echo $item['item_id']; ?>">
                      <div class="item-img-texto">
                        <div class="item-info">
                        <h3 class="titulo menu-title h3 mmx-item-title " data-translate="item-title"><?php echo safe_output($item['title']); ?></h3>
                         <p class="descripcion menu-description mmx-item-desc" data-translate="item-description"><?php echo safe_output($item['description']); ?></p>
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
                                  <h3 class="menu-price h3 mmx-item-price" ><?php echo mostrarPrecio($item['price'], $simbolo_moneda, $moneda); ?></h3>
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



<div id="image-modal">
  <span id="image-modal-close">&times;</span>
  <img id="image-modal-content" alt="Imagen ampliada">
</div>


<script>
  window.MaxMenuConfig = {
    globalTranslations: <?php echo json_encode($allTranslations ?? [], JSON_UNESCAPED_UNICODE); ?>,
    originalFlagUrl: "<?php echo htmlspecialchars($banderaUrlOriginal, ENT_QUOTES, 'UTF-8'); ?>",
    originalLanguageName: "<?php echo htmlspecialchars($originalLanguageName, ENT_QUOTES, 'UTF-8'); ?>",
    menuColors: <?php echo json_encode($colores ?? [], JSON_UNESCAPED_UNICODE); ?>,
    menuTypography: <?php echo json_encode($tipografias ?? [], JSON_UNESCAPED_UNICODE); ?>,
    menuBorders: <?php echo json_encode($menuBorders ?? [], JSON_UNESCAPED_UNICODE); ?>
  };
</script>

<!-- Lógica del widget separada por responsabilidad -->

<!-- Después de esto, ahora sí puedes cargar los archivos que usan esas variables -->
<script src="https://menu.maxmenu.com/assets/js/widget/colors.js"></script>
<script src="https://menu.maxmenu.com/assets/js/widget/image.js"></script>
<script src="https://menu.maxmenu.com/assets/js/widget/language.js"></script>
<script src="https://menu.maxmenu.com/assets/js/widget/subcategories.js"></script>


</div>