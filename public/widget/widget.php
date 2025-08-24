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

?>

  
       <div class="flecha-up" id="maxmenu-up">
          <a class="enlace enlace-flecha" href="#BtnTranslateMenu"><img src="https://menu.maxmenu.com/assets/css/widget/img/up.png" alt=""></a>
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
<script src="https://menu.maxmenu.com/assets/js/widget/colors.js"></script>
<script src="https://menu.maxmenu.com/assets/js/widget/image.js"></script>
<script src="https://menu.maxmenu.com/assets/js/widget/language.js"></script>
<script src="https://menu.maxmenu.com/assets/js/widget/subcategories.js"></script>


