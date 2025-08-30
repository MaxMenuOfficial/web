<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

/* ─── Parámetros via GET ─── */
$restaurantId = $_GET['id'] ?? null;
$version      = $_GET['version'] ?? null;

if (!$restaurantId) { http_response_code(400); exit('Missing restaurant ID'); }
if (!$version)      { http_response_code(400); exit('Missing version'); }

/* ─── Dependencias ─── */
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

$originalLanguageName = $restaurantData['original_language'] ?? '';
$originalLanguageCode = $restaurantData['language_code'] ?? '';
$banderaUrlOriginal   = $banderas[$originalLanguageCode] ?? 'menu/img/flags/default.png';

// Asegura arrays
$brunches = (isset($brunches) && is_array($brunches)) ? $brunches : [];
$estructuraMenu = $estructuraMenu ?? [];
$dailyMenu = $dailyMenu ?? [];
$plataformasExistentes = $plataformasExistentes ?? [];
$logos = $logos ?? [];
$colores = $colores ?? [];
$allTranslations = $allTranslations ?? [];
?>
<!--
  ===========================
  MaxMenu Widget v2 (CSS API)
  ===========================
  CSS API pública (no tocamos desde MaxMenu):
  - .mmx-root, .mmx-header, .mmx-logo-list, .mmx-logo, .mmx-platforms, .mmx-platform,
    .mmx-lang-trigger, .mmx-lang-modal, .mmx-lang-list, .mmx-categories-shortcuts,
    .mmx-category-shortcut, .mmx-subcategory-shortcuts, .mmx-carousel, .mmx-category,
    .mmx-category-title, .mmx-items, .mmx-item, .mmx-item-info, .mmx-item-title,
    .mmx-item-desc, .mmx-item-allergens, .mmx-item-price, .mmx-item-image,
    .mmx-brunch-section, .mmx-dailymenu-section, .mmx-image-modal, etc.
  Además, data-* semánticos: data-restaurant-id, data-category-id, data-subcategory-id, data-item-id
-->
<div class="maxmenu-root mmx-root"
     data-restaurant-id="<?php echo htmlspecialchars($restaurantId, ENT_QUOTES, 'UTF-8'); ?>"
     data-version="<?php echo htmlspecialchars($version, ENT_QUOTES, 'UTF-8'); ?>">

  <!-- Volver arriba -->
  <div class="flecha-up maxmenu-up mmx-back-to-top" id="maxmenu-up">
    <a class="enlace enlace-flecha mmx-back-to-top-link" href="#BtnTranslateMenu" aria-label="Ir arriba">
      <img src="https://menu.maxmenu.com/assets/css/widget/img/up.png" alt="" />
    </a>
  </div>

  <!-- Header / Logos -->
  <header class="logo-container mmx-header" role="banner">
    <?php if (!empty($logos) && is_array($logos)): ?>
      <ul class="mmx-logo-list" aria-label="Logos del restaurante">
        <?php foreach ($logos as $logoItem): ?>
          <li class="logo-item maxmenu-logo mmx-logo">
            <?php if (!empty($logoItem['logo_url'])): ?>
              <img src="<?php echo htmlspecialchars($logoItem['logo_url']); ?>"
                   alt="Logo del restaurante" class="mmx-logo-img" />
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </header>

  <!-- Plataformas externas -->
  <?php if (!empty($plataformasExistentes)): ?>
    <nav class="añadidas categorias mmx-platforms" aria-label="Plataformas">
      <?php foreach ($plataformasExistentes as $plataforma): ?>
        <a class="categoria maxmenu-plataformas mmx-platform"
           href="<?php echo htmlspecialchars($plataforma['platform_url']); ?>"
           target="_blank" rel="nofollow noopener"
           data-platform-name="<?php echo htmlspecialchars($plataforma['platform_name']); ?>">
          <!-- hook visual vía CSS: .mmx-platform[data-platform-name="instagram"] {...} -->
          <span class="mmx-platform-label">
            <?php echo htmlspecialchars($plataforma['platform_name']); ?>
          </span>
        </a>
      <?php endforeach; ?>
    </nav>
  <?php endif; ?>

  <!-- Selector de idioma (trigger) -->
  <div class="container-menu-buttom-translate mmx-lang-trigger">
    <button id="BtnTranslateMenu" class="mmx-lang-button" aria-haspopup="dialog" aria-controls="translateItemModalMenu">
      <img id="maxmenu-img-flag" class="mmx-lang-flag"
           src="<?php echo htmlspecialchars($_SESSION['flag_selected'] ?? $banderaUrlOriginal, ENT_QUOTES, 'UTF-8'); ?>"
           alt="Idioma actual" />
      <span class="mmx-lang-current"><?php echo htmlspecialchars($originalLanguageName, ENT_QUOTES, 'UTF-8'); ?></span>
    </button>
  </div>

  <!-- Modal de idiomas -->
  <div id="translateItemModalMenu" class="modal mmx-lang-modal" role="dialog" aria-modal="true" aria-labelledby="mmx-lang-title" style="display:none;">
    <div id="modal-content-translate" class="modal-content mmx-lang-modal-content">
      <button id="close-btn-modal-translate" class="close mmx-lang-close" aria-label="Cerrar">&times;</button>
      <h2 id="mmx-lang-title" class="mmx-lang-title">Selecciona idioma</h2>

      <div id="idiomasContainer" class="maxmenu-languaguesContainer mmx-lang-list" role="listbox">
        <!-- Original -->
        <div class="form-flag mmx-lang-option" role="option" aria-selected="false">
          <button class="form-flag-button translate-buttom mmx-lang-btn"
                  type="button" id="BtnViewOriginal" onclick="cargarIdiomaOriginal()"
                  data-language-code="<?php echo htmlspecialchars($originalLanguageCode, ENT_QUOTES, 'UTF-8'); ?>">
            <img class="idioma-btn-flag mmx-lang-flag"
                 src="<?php echo htmlspecialchars($banderaUrlOriginal, ENT_QUOTES, 'UTF-8'); ?>"
                 alt="Original" />
            <span class="mmx-lang-name"><?php echo htmlspecialchars($originalLanguageName, ENT_QUOTES, 'UTF-8'); ?></span>
          </button>
        </div>

        <!-- Idiomas activos -->
        <?php foreach ($languages as $langRow):
              if (empty($langRow['is_active'])) { continue; }
              $languageId   = $langRow['language_id']   ?? '';
              $languageName = $langRow['language_name'] ?? '';
              $languageCode = $langRow['language_code'] ?? '';
              $banderaUrl   = $banderas[$languageCode] ?? 'menu/img/flags/default.png';
        ?>
          <div class="form-flag mmx-lang-option" role="option" aria-selected="false">
            <button class="idioma-btn translate-buttom mmx-lang-btn"
                    type="button"
                    data-idioma="<?php echo htmlspecialchars($languageId, ENT_QUOTES, 'UTF-8'); ?>"
                    data-language-code="<?php echo htmlspecialchars($languageCode, ENT_QUOTES, 'UTF-8'); ?>"
                    data-flag="<?php echo htmlspecialchars($banderaUrl, ENT_QUOTES, 'UTF-8'); ?>">
              <img class="idioma-btn-flag mmx-lang-flag"
                   src="<?php echo htmlspecialchars($banderaUrl, ENT_QUOTES, 'UTF-8'); ?>"
                   alt="<?php echo htmlspecialchars($languageName, ENT_QUOTES, 'UTF-8'); ?>" />
              <span class="mmx-lang-name"><?php echo htmlspecialchars($languageName, ENT_QUOTES, 'UTF-8'); ?></span>
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Atajos por categoría y subcategoría -->
  <?php if (!empty($estructuraMenu)): ?>
    <div class="category-shortcuts mmx-categories-shortcuts" aria-label="Atajos de categorías">
      <?php foreach ($estructuraMenu as $categoria): ?>
        <div class="category-shortcut mmx-category-shortcut">
          <div class="category-container mmx-category-shortcut-row" data-category-id="<?php echo $categoria['category_id']; ?>">
            <button
              id="category-<?php echo $categoria['category_id']; ?>-shortcut"
              class="category-button-atajo menu-icon mmx-category-button"
              data-category-id="<?php echo $categoria['category_id']; ?>"
              onclick="scrollToCategory('<?php echo $categoria['category_id']; ?>')">
              <span class="nombre-categoria menu-icon mmx-category-button-label" data-translate="category">
                <?php echo htmlspecialchars($categoria['category_name'], ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </button>

            <?php if (!empty($categoria['subcategorias'])): ?>
              <button
                class="subcategory-toggle mmx-subcategory-toggle"
                type="button"
                aria-expanded="false"
                aria-controls="subcategories-<?php echo $categoria['category_id']; ?>"
                data-category-id="<?php echo $categoria['category_id']; ?>"
                onclick="toggleSubcategories('<?php echo $categoria['category_id']; ?>')">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"
                     fill="currentColor" class="arrow-icon-left menu-icon mmx-subcategory-toggle-icon"
                     id="arrow-<?php echo $categoria['category_id']; ?>">
                  <path d="M400-80 0-480l400-400 71 71-329 329 329 329-71 71Z"/>
                </svg>
              </button>
            <?php else: ?>
              <span class="arrow-placeholder mmx-subcategory-toggle-placeholder" aria-hidden="true"></span>
            <?php endif; ?>
          </div>

          <?php if (!empty($categoria['subcategorias'])): ?>
            <div id="subcategories-<?php echo $categoria['category_id']; ?>"
                 class="subcategory-shortcuts mmx-subcategory-shortcuts"
                 data-category-id="<?php echo $categoria['category_id']; ?>"
                 hidden>
              <?php foreach ($categoria['subcategorias'] as $subcategoria): ?>
                <button
                  class="subcategory-button-atajo menu-icon mmx-subcategory-button"
                  data-subcategory-id="<?php echo $subcategoria['subcategory_id']; ?>"
                  onclick="scrollToSubcategory('<?php echo $categoria['category_id']; ?>','<?php echo $subcategoria['subcategory_id']; ?>')">
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
  <?php endif; ?>

  <!-- Carrusel / Categorías -->
  <section class="añadidas mmx-content">
    <div id="category-carousel" class="carousel " aria-live="polite">
      <div id="items-container" class="carousel-track ">
        <?php foreach ($estructuraMenu as $categoria): ?>
          <article id="category-<?php echo $categoria['category_id']; ?>"
                   class="carousel-category mmx-category"
                   data-category-id="<?php echo $categoria['category_id']; ?>">
            <h3 class="h3 mmx-category-title">
              <span class="nombre-categoria menu-icon mmx-category-title-text"
                    id="maxmenu-categories-name"
                    data-translate="category"
                    data-category-id="<?php echo $categoria['category_id']; ?>">
                <?php echo htmlspecialchars($categoria['category_name'], ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </h3>

            <?php
              $itemsSinSubcategoria = [];
              if (!empty($categoria['items']) && is_array($categoria['items'])) {
                $itemsSinSubcategoria = array_filter($categoria['items'], fn($it)=> empty($it['subcategory_id']));
              }

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

            <div class="category-items mmx-items">

              <!-- Ítems sin subcategoría -->
              <?php if (!empty($itemsSinSubcategoria)): ?>
                <?php foreach ($itemsSinSubcategoria as $item): ?>
                  <div class="item-container mmx-item" data-item-id="<?php echo $item['item_id']; ?>">
                    <div class="item-img-texto mmx-item-row">
                      <div class="item-info mmx-item-info">
                        <h4 class="titulo menu-title h3 mmx-item-title" data-translate="item-title">
                          <?php echo safe_output($item['title']); ?>
                        </h4>
                        <p class="descripcion menu-description mmx-item-desc" data-translate="item-description">
                          <?php echo safe_output($item['description']); ?>
                        </p>

                        <?php if (!empty($item['allergens'])): ?>
                          <div class="alergenos-items mmx-item-allergens">
                            <div class="alergenos-show mmx-item-allergens-list" role="list">
                              <?php
                                $carpetaAlergenos = "https://menu.maxmenu.com/assets/css/menu/alergenos" . intval($diseñoAlergenos);
                                $alergenos = explode(',', $item['allergens']);
                                foreach ($alergenos as $alergeno):
                                  $alergeno_trimmed = strtolower(trim($alergeno));
                              ?>
                                <img role="listitem"
                                     class="mmx-item-allergen"
                                     src="<?php echo htmlspecialchars($carpetaAlergenos . '/' . $alergeno_trimmed . '.svg', ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo htmlspecialchars($alergeno_trimmed, ENT_QUOTES, 'UTF-8'); ?>"
                                     width="30" height="30" />
                              <?php endforeach; ?>
                            </div>
                          </div>
                        <?php endif; ?>

                        <div class="item-agrupar-precios mmx-item-prices">
                          <?php if (!empty($item['price'])): ?>
                            <div class="item-image-doble mmx-item-price-wrap">
                              <div class="item-image-simple mmx-item-price-box">
                                <h5 class="menu-price h3 mmx-item-price">
                                  <?php echo mostrarPrecio($item['price'], $simbolo_moneda, $moneda); ?>
                                </h5>
                              </div>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>

                      <div class="item-image mmx-item-image <?php echo empty($item['image']) ? 'no-image' : ''; ?>">
                        <?php if (!empty($item['image'])): ?>
                          <img class="expandable-image mmx-item-image-img"
                               src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>"
                               alt="Imagen del producto"
                               style="max-width: 100px; min-width: 100px; min-height: 100px; max-height: 100px; object-fit: cover; border-radius: 30px; margin: 5px;">
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>

              <!-- Brunch -->
              <?php if (!empty($brunchesDeEstaCategoria)): ?>
                <section class="brunch-section mmx-brunch-section">
                  <?php foreach ($brunchesDeEstaCategoria as $brunch): ?>
                    <div class="item-container mmx-item mmx-brunch" data-brunch-id="<?php echo htmlspecialchars($brunch['brunch_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                      <div class="item-img-texto mmx-item-row">
                        <div class="item-info mmx-item-info">
                          <h4 class="titulo-item menu-title h3 mmx-item-title">
                            <span class="mmx-brunch-time"><?php echo safe_output($brunch['horarios'] ?? 'Brunch'); ?></span>
                          </h4>

                          <?php if (!empty($brunch['description'])): ?>
                            <p class="menu-description mmx-item-desc"><?php echo safe_output($brunch['description']); ?></p>
                          <?php endif; ?>

                          <?php if (!empty($brunch['infusions_items'])): ?>
                            <strong class="menu-title mmx-brunch-group-title">Infusiones:</strong>
                            <ul class="mmx-brunch-group-list">
                              <?php foreach ($brunch['infusions_items'] as $inf): ?>
                                <li class="menu-description mmx-brunch-item"><?php echo safe_output($inf); ?></li>
                              <?php endforeach; ?>
                            </ul>
                          <?php endif; ?>

                          <?php if (!empty($brunch['main_course_items'])): ?>
                            <strong class="menu-title mmx-brunch-group-title">Plato Principal:</strong>
                            <ul class="mmx-brunch-group-list">
                              <?php foreach ($brunch['main_course_items'] as $plato): ?>
                                <li class="menu-description mmx-brunch-item"><?php echo safe_output($plato); ?></li>
                              <?php endforeach; ?>
                            </ul>
                          <?php endif; ?>

                          <?php if (!empty($brunch['bakery_items'])): ?>
                            <strong class="menu-title mmx-brunch-group-title">Panadería &amp; Pastelería:</strong>
                            <ul class="mmx-brunch-group-list">
                              <?php foreach ($brunch['bakery_items'] as $bakery): ?>
                                <li class="menu-description mmx-brunch-item"><?php echo safe_output($bakery); ?></li>
                              <?php endforeach; ?>
                            </ul>
                          <?php endif; ?>

                          <?php if (!empty($brunch['drinks_items'])): ?>
                            <strong class="menu-title mmx-brunch-group-title">Bebidas &amp; Cócteles:</strong>
                            <ul class="mmx-brunch-group-list">
                              <?php foreach ($brunch['drinks_items'] as $drink): ?>
                                <li class="menu-description mmx-brunch-item"><?php echo safe_output($drink); ?></li>
                              <?php endforeach; ?>
                            </ul>
                          <?php endif; ?>

                          <?php if (!empty($brunch['additional_items'])): ?>
                            <strong class="menu-title mmx-brunch-group-title">Adicionales:</strong>
                            <ul class="mmx-brunch-group-list">
                              <?php foreach ($brunch['additional_items'] as $add): ?>
                                <li class="menu-description mmx-brunch-item"><?php echo safe_output($add); ?></li>
                              <?php endforeach; ?>
                            </ul>
                          <?php endif; ?>

                          <?php if (isset($brunch['price'])): ?>
                            <p class="menu-price mmx-item-price"><?php echo mostrarPrecio($brunch['price'], $simbolo_moneda, $moneda); ?></p>
                          <?php endif; ?>

                          <?php if (!empty($brunch['allergens'])): ?>
                            <div class="alergenos-items mmx-item-allergens">
                              <div class="alergenos-show mmx-item-allergens-list" role="list">
                                <?php
                                  $carpetaAlergenos = "https://menu.maxmenu.com/assets/css/menu/alergenos" . intval($diseñoAlergenos);
                                  $alergenos = explode(',', $brunch['allergens']);
                                  foreach ($alergenos as $alergeno):
                                    $alergeno_trimmed = strtolower(trim($alergeno));
                                ?>
                                  <img role="listitem"
                                       class="mmx-item-allergen"
                                       src="<?php echo htmlspecialchars($carpetaAlergenos . '/' . $alergeno_trimmed . '.svg', ENT_QUOTES, 'UTF-8'); ?>"
                                       alt="<?php echo htmlspecialchars($alergeno_trimmed, ENT_QUOTES, 'UTF-8'); ?>"
                                       width="30" height="30" />
                                <?php endforeach; ?>
                              </div>
                            </div>
                          <?php endif; ?>
                        </div>

                        <div class="item-image mmx-item-image <?php echo empty($brunch['image_url']) ? 'no-image' : ''; ?>">
                          <?php if (!empty($brunch['image_url'])): ?>
                            <img class="expandable-image mmx-item-image-img"
                                 src="<?php echo htmlspecialchars($brunch['image_url']); ?>"
                                 alt="Imagen del brunch"
                                 style="max-width: 100px; min-width: 100px; min-height: 100px; max-height: 100px; object-fit: cover; border-radius: 30px; margin: 5px;">
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </section>
              <?php endif; ?>

              <!-- Menú del día -->
              <?php if (!empty($filteredDailyMenu)): ?>
                <section class="daily-menu-section mmx-dailymenu-section">
                  <?php foreach ($filteredDailyMenu as $menu): ?>
                    <div class="item-container mmx-item mmx-dailymenu" data-dailymenu-id="<?php echo htmlspecialchars($menu['daily_menu_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                      <div class="item-img-texto mmx-item-row">
                        <div class="item-info mmx-item-info">
                          <h4 class="titulo-item h3 mmx-item-title">
                            <span class="menu-title mmx-dailymenu-time"><?php echo safe_output($menu['schedules'] ?? 'Menú del Día'); ?></span>
                          </h4>

                          <?php if (!empty($menu['description'])): ?>
                            <p class="menu-description mmx-item-desc"><?php echo safe_output($menu['description']); ?></p>
                          <?php endif; ?>

                          <?php
                            $groups = [
                              'starter_options' => 'Entrantes:',
                              'main_options'    => 'Plato Principal:',
                              'second_options'  => 'Segundo Plato:',
                              'dessert_options' => 'Postres:',
                              'drinks_items'    => 'Bebidas:',
                              'additional_items'=> 'Adicionales:'
                            ];
                            foreach ($groups as $key => $label):
                              if (!empty($menu[$key])):
                          ?>
                            <strong class="menu-title mmx-dailymenu-group-title"><?php echo $label; ?></strong>
                            <ul class="mmx-dailymenu-group-list">
                              <?php foreach ($menu[$key] as $it): ?>
                                <li class="menu-description mmx-dailymenu-item"><?php echo safe_output($it); ?></li>
                              <?php endforeach; ?>
                            </ul>
                          <?php
                              endif;
                            endforeach;
                          ?>

                          <?php if (isset($menu['price'])): ?>
                            <p class="menu-price mmx-item-price"><?php echo mostrarPrecio($menu['price'], $simbolo_moneda, $moneda); ?></p>
                          <?php endif; ?>

                          <?php if (!empty($menu['allergens'])): ?>
                            <div class="alergenos-items mmx-item-allergens">
                              <div class="alergenos-show mmx-item-allergens-list" role="list">
                                <?php
                                  $carpetaAlergenos = "https://menu.maxmenu.com/assets/css/menu/alergenos" . intval($diseñoAlergenos);
                                  $alergenos = explode(',', $menu['allergens']);
                                  foreach ($alergenos as $alergeno):
                                    $alergeno_trimmed = strtolower(trim($alergeno));
                                ?>
                                  <img role="listitem"
                                       class="mmx-item-allergen"
                                       src="<?php echo htmlspecialchars($carpetaAlergenos . '/' . $alergeno_trimmed . '.svg', ENT_QUOTES, 'UTF-8'); ?>"
                                       alt="<?php echo htmlspecialchars($alergeno_trimmed, ENT_QUOTES, 'UTF-8'); ?>"
                                       width="30" height="30" />
                                <?php endforeach; ?>
                              </div>
                            </div>
                          <?php endif; ?>
                        </div>

                        <div class="item-image mmx-item-image <?php echo empty($menu['image']) ? 'no-image' : ''; ?>">
                          <?php if (!empty($menu['image'])): ?>
                            <img class="expandable-image mmx-item-image-img"
                                 src="<?php echo htmlspecialchars($menu['image']); ?>"
                                 alt="Imagen del Menú del Día"
                                 style="max-width: 100px; min-width: 100px; min-height: 100px; max-height: 100px; object-fit: cover; border-radius: 30px; margin: 5px;">
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </section>
              <?php endif; ?>

              <!-- Ítems con subcategoría -->
              <?php if (!empty($categoria['subcategorias'])): ?>
                <?php foreach ($categoria['subcategorias'] as $subcategoria): ?>
                  <section id="subcategory-<?php echo $subcategoria['subcategory_id']; ?>"
                           class="subcategory-container mmx-subcategory"
                           data-subcategory-id="<?php echo $subcategoria['subcategory_id']; ?>">
                    <h4 class="mmx-subcategory-title">
                      <span class="nombre-subcategoria menu-icon mmx-subcategory-title-text"
                            data-translate="subcategory"
                            data-subcategory-id="<?php echo $subcategoria['subcategory_id']; ?>">
                        <?php echo htmlspecialchars($subcategoria['subcategory_name'], ENT_QUOTES, 'UTF-8'); ?>
                      </span>
                    </h4>

                    <?php foreach ($subcategoria['items'] as $item): ?>
                      <div class="item-container mmx-item" data-item-id="<?php echo $item['item_id']; ?>">
                        <div class="item-img-texto mmx-item-row">
                          <div class="item-info mmx-item-info">
                            <h5 class="titulo menu-title h3 mmx-item-title" data-translate="item-title">
                              <?php echo safe_output($item['title']); ?>
                            </h5>
                            <p class="descripcion menu-description mmx-item-desc" data-translate="item-description">
                              <?php echo safe_output($item['description']); ?>
                            </p>

                            <?php if (!empty($item['allergens'])): ?>
                              <div class="alergenos-items mmx-item-allergens">
                                <div class="alergenos-show mmx-item-allergens-list" role="list">
                                  <?php
                                    $carpetaAlergenos = "https://menu.maxmenu.com/assets/css/menu/alergenos" . intval($diseñoAlergenos);
                                    $alergenos = explode(',', $item['allergens']);
                                    foreach ($alergenos as $alergeno):
                                      $alergeno_trimmed = strtolower(trim($alergeno));
                                  ?>
                                    <img role="listitem"
                                         class="mmx-item-allergen"
                                         src="<?php echo htmlspecialchars($carpetaAlergenos . '/' . $alergeno_trimmed . '.svg', ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="<?php echo htmlspecialchars($alergeno_trimmed, ENT_QUOTES, 'UTF-8'); ?>"
                                         width="30" height="30" />
                                  <?php endforeach; ?>
                                </div>
                              </div>
                            <?php endif; ?>

                            <?php if (!empty($item['price'])): ?>
                              <div class="item-agrupar-precios mmx-item-prices">
                                <div class="item-image-doble mmx-item-price-wrap">
                                  <div class="item-image-simple mmx-item-price-box">
                                    <span class="menu-price h3 mmx-item-price">
                                      <?php echo mostrarPrecio($item['price'], $simbolo_moneda, $moneda); ?>
                                    </span>
                                  </div>
                                </div>
                              </div>
                            <?php endif; ?>
                          </div>

                          <div class="item-image mmx-item-image <?php echo empty($item['image']) ? 'no-image' : ''; ?>">
                            <?php if (!empty($item['image'])): ?>
                              <img class="expandable-image mmx-item-image-img"
                                   src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>"
                                   alt="Imagen del producto"
                                   style="max-width: 100px; min-width: 100px; min-height: 100px; max-height: 100px; object-fit: cover; border-radius: 30px; margin: 5px;">
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </section>
                <?php endforeach; ?>
              <?php endif; ?>

            </div> <!-- /.mmx-items -->
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Modal de imagen ampliada -->
  <div id="image-modal" class="mmx-image-modal" role="dialog" aria-modal="true" aria-label="Imagen ampliada" hidden>
    <button id="image-modal-close" class="mmx-image-modal-close" aria-label="Cerrar">&times;</button>
    <img id="image-modal-content" class="mmx-image-modal-content" alt="Imagen ampliada" />
  </div>

  <!-- Config global -->
  <script>
    window.MaxMenuConfig = {
      globalTranslations: <?php echo json_encode($allTranslations, JSON_UNESCAPED_UNICODE); ?>,
      originalFlagUrl: "<?php echo htmlspecialchars($banderaUrlOriginal, ENT_QUOTES, 'UTF-8'); ?>",
      originalLanguageName: "<?php echo htmlspecialchars($originalLanguageName, ENT_QUOTES, 'UTF-8'); ?>",
      menuColors: <?php echo json_encode($colores); ?>
    };
  </script>

  <!-- JS del widget -->
  <script src="https://menu.maxmenu.com/assets/js/widget/colors.js"></script>
  <script src="https://menu.maxmenu.com/assets/js/widget/image.js"></script>
  <script src="https://menu.maxmenu.com/assets/js/widget/language.js"></script>
  <script src="https://menu.maxmenu.com/assets/js/widget/subcategories.js"></script>
</div>