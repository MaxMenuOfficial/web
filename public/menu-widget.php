<?php
// 🎯 CORS universal para widgets embebidos
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: false');

// 🔄 Respuesta a preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    header('Content-Length: 0');
    exit;
}
// 🧾 Tipo de contenido
header('Content-Type: text/html; charset=utf-8');


include '../get/get_restaurant_id.php';
include '../get/get_logo.php';
include '../get/get_idiomas.php'; 
include '../get/get_categoria.php';
include '../get/get_plataformas.php';
include '../get/get_restaurant_moneda.php';
include '../get/get_idiomas_for_items.php';
include '../get/get_simbolo_moneda.php';
include '../get/get_cat_and_subcat_for_item.php';
include '../get/get_brunch.php';
include '../get/get_daily_menu.php';
include '../get/get_traducciones.php';
include '../get/get_alergenos.php';
include '../get/get_colors.php'; 



?>

<body id="#maxmenu-menuContainer">

    <main>
  
      <div class="flecha-up">
          <a class="enlace enlace-flecha" href="#BtnTranslateMenu"><img src="https://menu.maxmenu.com/menu/img/up.png" alt=""></a>
      </div>


            <div class="logo-container">

                <?php if (!empty($logos) && is_array($logos)): ?>
                    <?php foreach ($logos as $logoItem): ?>
                        <div id="maxmenu-logo" class="logo-item">
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
                    <div class="categoria" id="maxmenu-plataformas">
                        <a id="maxmenu-plataforma-button" href="<?php echo htmlspecialchars($plataforma['platform_url']); ?>" 
                           class="visitar-btn <?php echo strtolower(htmlspecialchars($plataforma['platform_name'])); ?>" 
                        ></a>
                    </div>
                    <?php endforeach;
                else: ?>
                <?php endif; ?>
        </div>
    </div>


    <!-- 2) Botón para abrir el modal de selección de idioma -->
    <div class="container-menu-buttom-translate">
        <button id="BtnTranslateMenu">
            <!-- Muestra la bandera seleccionada en sesión o, si no existe, la del idioma original -->
           <img id="maxmenu-img-flag" 
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

        <button class="form-flag-button" type="button" id="BtnViewOriginal" onclick="cargarIdiomaOriginal()">
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
            <button class="idioma-btn" 

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


<div class="category-shortcuts">
  <?php foreach ($estructuraMenu as $categoria): ?>
    <div class="category-shortcut">
      <!-- Botón de la categoría -->
      <div class="category-container" id="maxmenu-category-container" >
        <button 
          id="category-<?php echo $categoria['category_id']; ?>-shortcut" 
          class="category-button-atajo menu-icon" 
          data-category-id="<?php echo $categoria['category_id']; ?>" 
          onclick="scrollToCategory('<?php echo $categoria['category_id']; ?>')"
        >
          <!-- Título de la categoría con data-translate -->
          <span id="maxmenu-category-buttom"class="nombre-categoria menu-icon" data-translate="category">
            <?php echo htmlspecialchars($categoria['category_name'], ENT_QUOTES, 'UTF-8'); ?>
          </span>
        </button>

        <?php if (!empty($categoria['subcategorias'])): ?>
          <button 
            id="maxmenu-subcategory-buttom"
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
              id="maxmenu-subcategory-buttom" 
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
                                $carpetaAlergenos = "https://menu.maxmenu.com/menu/alergenos" . intval($diseñoAlergenos); 

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
                                $carpetaAlergenos = "https://menu.maxmenu.com/menu/alergenos" . intval($diseñoAlergenos); 

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
                                $carpetaAlergenos = "https://menu.maxmenu.com/menu/alergenos" . intval($diseñoAlergenos); 

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
                                $carpetaAlergenos = "https://menu.maxmenu.com/menu/alergenos" . intval($diseñoAlergenos); 

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
              if (empty($itemsSinSubcategoria) && empty($brunchesDeEstaCategoria)) {
                  echo "<p>No hay subcategorías ni ítems disponibles en esta categoría.</p>";
              }
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
  window.MaxMenuConfig = {
    globalTranslations: <?php echo json_encode($allTranslations ?? [], JSON_UNESCAPED_UNICODE); ?>,
    originalFlagUrl: "<?php echo htmlspecialchars($banderaUrlOriginal, ENT_QUOTES, 'UTF-8'); ?>",
    originalLanguageName: "<?php echo htmlspecialchars($originalLanguageName, ENT_QUOTES, 'UTF-8'); ?>",
    menuColors: <?php echo json_encode($colores); ?>
  };
</script>
<!-- Lógica del widget separada por responsabilidad -->

<!-- Después de esto, ahora sí puedes cargar los archivos que usan esas variables -->
<script src="https://menu.maxmenu.com/assets/widget/colors.js"></script>
<script src="https://menu.maxmenu.com/assets/widget/image.js"></script>
<script src="https://menu.maxmenu.com/assets/widget/language.js"></script>
<script src="https://menu.maxmenu.com/assets/widget/subcategories.js"></script>


</body>







