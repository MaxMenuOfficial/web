<?php
// === Detección automática de moneda según país (Cloudflare) ===
$country = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? 'US';
$country = strtoupper(trim($country));

// --- Definición de grupos ---
$EU_COUNTRIES = [
    'AT','BE','BG','HR','CY','CZ','DK','EE','FI','FR','DE','GR','HU','IE','IT',
    'LV','LT','LU','MT','NL','PL','PT','RO','SK','SI','ES','SE'
];

$GBP_COUNTRIES = ['GB','UK','GG','JE','IM'];
$CHF_COUNTRIES = ['CH','LI'];
$USD_COUNTRIES = ['US','CA','MX','AR','CL','CO','PE','UY','PY','BO','CR','EC','GT','HN','NI','PA','DO','VE'];

// --- Selección de símbolo y separador ---
if (in_array($country, $EU_COUNTRIES, true)) {
    $currencySymbol = '€';
    $currencyCode = 'EUR';
    $decimalSeparator = ',';
} elseif (in_array($country, $GBP_COUNTRIES, true)) {
    $currencySymbol = '£';
    $currencyCode = 'GBP';
    $decimalSeparator = '.';
} elseif (in_array($country, $CHF_COUNTRIES, true)) {
    $currencySymbol = 'CHF';
    $currencyCode = 'CHF';
    $decimalSeparator = '.';
} else {
    $currencySymbol = '$';
    $currencyCode = 'USD';
    $decimalSeparator = '.';
}

// --- Helper para renderizar correctamente el precio ---
function renderLocalizedPrice(string $price, string $symbol, string $sep, string $code): string {
    $formatted = str_replace('.', $sep, number_format((float)$price, 2, $sep, ''));
    $space = '&nbsp;';
    if (in_array($code, ['GBP','CHF','USD'], true)) {
        return $symbol . $space . $formatted;
    }
    return $formatted . $space . $symbol;
}
?>

<main class="contenedor-principal">

  <div class="back-edit">
    <h2 class="titulo"></h2>

     <div class="section-header">
      <div class="section-tag">Suscricion + Comisiones por transacion</div>
      <h2>Descubre nuestras tarifas disponibles para cada pais</h2>
   
    </div>

  </div>

  

  <div class="planes-contenedor">

    <!-- FREE -->
    <article class="pricing-option" id="plan-free">

      <a href="https://manage.maxmenu.com/login" class="btn btn-free">Empieza gratis</a>

      <h2>Playground de diseño gratuito</h2>
      <h3>Diseña el menú completo sin límites. Publica después, cuando estés listo.</h3>

      <p class="price">
        <?php echo renderLocalizedPrice('0.00', $currencySymbol, $decimalSeparator, $currencyCode); ?>
        <span>/ mes</span>
      </p>

      <ul class="features">
        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Acceso completo al diseñador de MaxMenu: colores, paneles, tipografía, bordes, espaciado, alérgenos e imágenes.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Crea categorías, subcategorías, secciones de brunch, menús del día y artículos ilimitados.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Configura todos los idiomas, alérgenos e iconos exactamente igual que en los planes de pago (solo diseño).</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Previsualiza el menú en vista de escritorio y móvil tantas veces como quieras.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Control visual completo: colores, múltiples paletas de acento, familias tipográficas, pesos y tamaños para títulos, descripciones y precios.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Bordes y radio a nivel de detalle: bordes de tarjetas, separadores, contornos y radio de esquina por superficie.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Controles de layout y espaciado: densidad, ritmo vertical entre categorías, artículos y secciones.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Activa/desactiva artículos en tiempo real (agotados, platos temporales, eventos) sin editar la estructura.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Ediciones en tiempo real: los comensales siempre ven la última versión sin recargar códigos QR.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Guarda todo tu trabajo y sigue iterando en el diseño sin límites de tiempo.</span>
        </li>

        <li class="criteria">
          <i class="fa fa-times icon"></i>
          <span>Sin menú público: sin código QR, sin URL pública, los comensales no pueden acceder al menú hasta que actualices tu plan.</span>
        </li>
      </ul>

    </article>

    <!-- BASIC -->
    <article class="pricing-option" id="plan-basic">

      <a href="https://manage.maxmenu.com/login" class="btn btn-basic">Empezar Basic</a>

      <h2>Basic</h2>
      <h3>Publica un menú totalmente personalizado en una URL de MaxMenu, con traducciones automáticas a 13 idiomas.</h3>

      <p class="price">
        <?php echo renderLocalizedPrice('13.99', $currencySymbol, $decimalSeparator, $currencyCode); ?>
        <span>/ mes</span>
      </p>

      <ul class="features">
        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Todo lo del playground gratuito, pero ahora en vivo para tus comensales.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Menú público alojado en MaxMenu con una URL limpia y compartible para tu restaurante.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Traducción automática del menú completo a 13 idiomas.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Escaneos QR ilimitados apuntando a tu URL de MaxMenu.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>URL dedicada.</span>
        </li>
      </ul>

    </article>

    <!-- MAX -->
    <article class="pricing-option" id="plan-pro">

      <a href="https://manage.maxmenu.com/login" class="btn btn-basic">Empezar Max</a>

      <h2>Max</h2>
      <h3>La experiencia MaxMenu completa: widget embebible, elección de dominio y traducción automática a 22 idiomas.</h3>

      <p class="price">
        <?php echo renderLocalizedPrice('19.99', $currencySymbol, $decimalSeparator, $currencyCode); ?>
        <span>/ mes</span>
      </p>

      <ul class="features">
        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Todo lo de Basic, más el widget embebible completo para tu propia web.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Elige cómo publicas: embebe el widget en tu dominio, usa la URL de MaxMenu, o usa ambos a la vez.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Traducción automática a 22 idiomas, incluyendo asiáticos y RTL, listo para zonas turísticas.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Control de dominio para el widget: solo dominios autorizados pueden mostrar tu menú.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Mismo motor de diseño a nivel de píxel que en Basic, pero optimizado para menús embebibles que se sienten nativos de tu web.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Imágenes transparentes y a sangre: soporte para platos PNG sin fondo sobre paneles neutros o con color de marca.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Artículos, categorías, secciones de brunch y menús del día ilimitados, sincronizados entre el widget y la URL alojada.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Escaneos QR ilimitados que pueden apuntar a la URL de MaxMenu o directamente al widget en tu propio dominio.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Preparado para el futuro: listo para conectar con pagos de artículos y fidelización cuando decidas activarlos.</span>
        </li>
      </ul>

    </article>

  </div>

</main>

<!-- ========================= -->
<!-- ORDER & PAY: INFRAESTRUCTURA -->
<!-- ========================= -->
<div class="section">
  <div class="section-inner">

    <div class="section-header">
      <div class="section-tag">Order &amp; Pay · Infraestructura</div>
      <h2>Pagos integrados en el menú. Comisión fija. Sin porcentajes.</h2>
      <p>
        Order &amp; Pay se activa sobre cualquier plan de pago (Basic o Max). No tiene coste mensual adicional.
        Solo pagas una comisión fija por cada transacción exitosa. Sin porcentajes sobre el volumen.
        Sin éxito, sin comisión.
      </p>
    </div>

    <!-- ======== FLUJO DE PAGO ======== -->
    <div class="op-flow">
      <div class="op-flow-header">
        <h3>Flujo de una transacción</h3>
        <p>Cada pago sigue un flujo determinista: del checkout al banco del restaurante, pasando por la comisión fija de MaxMenu.</p>
      </div>

      <div class="op-flow-steps">
        <div class="op-flow-step">
          <div class="op-flow-number">1</div>
          <div class="op-flow-content">
            <strong>El cliente paga en tu web</strong>
            <span>Apple Pay, Google Pay o tarjeta — dentro del widget embebido, en tu dominio y con tu marca.</span>
          </div>
        </div>
        <div class="op-flow-connector"></div>
        <div class="op-flow-step">
          <div class="op-flow-number">2</div>
          <div class="op-flow-content">
            <strong>MaxMenu deduce su comisión fija</strong>
            <span>Una cantidad fija por transacción según la moneda del restaurante. Sin porcentaje. Sin sorpresas.</span>
          </div>
        </div>
        <div class="op-flow-connector"></div>
        <div class="op-flow-step">
          <div class="op-flow-number">3</div>
          <div class="op-flow-content">
            <strong>El pedido se enruta a la estación</strong>
            <span>Cocina, barra, cócteles… cada artículo llega donde debe. Estado: Preparando → Listo → Completado.</span>
          </div>
        </div>
        <div class="op-flow-connector"></div>
        <div class="op-flow-step">
          <div class="op-flow-number">4</div>
          <div class="op-flow-content">
            <strong>El dinero llega a tu cuenta bancaria</strong>
            <span>El neto tras comisiones se deposita automáticamente en la cuenta del restaurante. MaxMenu no toca tu dinero.</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>


<!-- ========================= -->
<!-- MONEDAS SOPORTADAS        -->
<!-- ========================= -->
<div class="section">
  <div class="section-inner">

    <div class="section-header">
      <div class="section-tag">Monedas</div>
      <h2>19 monedas. Una comisión fija por cada una.</h2>
      <p>
        La comisión de MaxMenu se adapta a cada divisa para reflejar su valor real.
        Cada restaurante opera en la moneda de su país. Sin conversión, sin markup.
      </p>
    </div>

    <div class="op-currency-grid">

      <!-- EUR -->
      <div class="op-currency-card op-currency-card--featured">
        <div class="op-currency-flag">€</div>
        <div class="op-currency-info">
          <div class="op-currency-code">EUR</div>
          <div class="op-currency-name">Euro</div>
        </div>
        <div class="op-currency-fee">0,15&nbsp;€</div>
      </div>

      <!-- GBP -->
      <div class="op-currency-card">
        <div class="op-currency-flag">£</div>
        <div class="op-currency-info">
          <div class="op-currency-code">GBP</div>
          <div class="op-currency-name">Libra esterlina</div>
        </div>
        <div class="op-currency-fee">0,15&nbsp;£</div>
      </div>

      <!-- CHF -->
      <div class="op-currency-card">
        <div class="op-currency-flag">Fr</div>
        <div class="op-currency-info">
          <div class="op-currency-code">CHF</div>
          <div class="op-currency-name">Franco suizo</div>
        </div>
        <div class="op-currency-fee">0,16&nbsp;CHF</div>
      </div>

      <!-- USD -->
      <div class="op-currency-card op-currency-card--featured">
        <div class="op-currency-flag">$</div>
        <div class="op-currency-info">
          <div class="op-currency-code">USD</div>
          <div class="op-currency-name">Dólar estadounidense</div>
        </div>
        <div class="op-currency-fee">0,19&nbsp;$</div>
      </div>

      <!-- CAD -->
      <div class="op-currency-card">
        <div class="op-currency-flag">C$</div>
        <div class="op-currency-info">
          <div class="op-currency-code">CAD</div>
          <div class="op-currency-name">Dólar canadiense</div>
        </div>
        <div class="op-currency-fee">0,24&nbsp;CA$</div>
      </div>

      <!-- AUD -->
      <div class="op-currency-card">
        <div class="op-currency-flag">A$</div>
        <div class="op-currency-info">
          <div class="op-currency-code">AUD</div>
          <div class="op-currency-name">Dólar australiano</div>
        </div>
        <div class="op-currency-fee">0,24&nbsp;AU$</div>
      </div>

      <!-- NZD -->
      <div class="op-currency-card">
        <div class="op-currency-flag">NZ$</div>
        <div class="op-currency-info">
          <div class="op-currency-code">NZD</div>
          <div class="op-currency-name">Dólar neozelandés</div>
        </div>
        <div class="op-currency-fee">0,26&nbsp;NZ$</div>
      </div>

      <!-- JPY -->
      <div class="op-currency-card">
        <div class="op-currency-flag">¥</div>
        <div class="op-currency-info">
          <div class="op-currency-code">JPY</div>
          <div class="op-currency-name">Yen japonés</div>
        </div>
        <div class="op-currency-fee">24&nbsp;¥</div>
      </div>

      <!-- SGD -->
      <div class="op-currency-card">
        <div class="op-currency-flag">S$</div>
        <div class="op-currency-info">
          <div class="op-currency-code">SGD</div>
          <div class="op-currency-name">Dólar singapurense</div>
        </div>
        <div class="op-currency-fee">0,22&nbsp;S$</div>
      </div>

      <!-- HKD -->
      <div class="op-currency-card">
        <div class="op-currency-flag">HK$</div>
        <div class="op-currency-info">
          <div class="op-currency-code">HKD</div>
          <div class="op-currency-name">Dólar hongkonés</div>
        </div>
        <div class="op-currency-fee">1,25&nbsp;HK$</div>
      </div>

      <!-- MYR -->
      <div class="op-currency-card">
        <div class="op-currency-flag">RM</div>
        <div class="op-currency-info">
          <div class="op-currency-code">MYR</div>
          <div class="op-currency-name">Ringgit malayo</div>
        </div>
        <div class="op-currency-fee">0,72&nbsp;RM</div>
      </div>

      <!-- SEK -->
      <div class="op-currency-card">
        <div class="op-currency-flag">kr</div>
        <div class="op-currency-info">
          <div class="op-currency-code">SEK</div>
          <div class="op-currency-name">Corona sueca</div>
        </div>
        <div class="op-currency-fee">1,70&nbsp;kr</div>
      </div>

      <!-- NOK -->
      <div class="op-currency-card">
        <div class="op-currency-flag">kr</div>
        <div class="op-currency-info">
          <div class="op-currency-code">NOK</div>
          <div class="op-currency-name">Corona noruega</div>
        </div>
        <div class="op-currency-fee">1,70&nbsp;kr</div>
      </div>

      <!-- DKK -->
      <div class="op-currency-card">
        <div class="op-currency-flag">kr</div>
        <div class="op-currency-info">
          <div class="op-currency-code">DKK</div>
          <div class="op-currency-name">Corona danesa</div>
        </div>
        <div class="op-currency-fee">1,10&nbsp;kr</div>
      </div>

      <!-- PLN -->
      <div class="op-currency-card">
        <div class="op-currency-flag">zł</div>
        <div class="op-currency-info">
          <div class="op-currency-code">PLN</div>
          <div class="op-currency-name">Złoty polaco</div>
        </div>
        <div class="op-currency-fee">0,65&nbsp;zł</div>
      </div>

      <!-- CZK -->
      <div class="op-currency-card">
        <div class="op-currency-flag">Kč</div>
        <div class="op-currency-info">
          <div class="op-currency-code">CZK</div>
          <div class="op-currency-name">Corona checa</div>
        </div>
        <div class="op-currency-fee">3,50&nbsp;Kč</div>
      </div>

      <!-- HUF -->
      <div class="op-currency-card">
        <div class="op-currency-flag">Ft</div>
        <div class="op-currency-info">
          <div class="op-currency-code">HUF</div>
          <div class="op-currency-name">Forinto húngaro</div>
        </div>
        <div class="op-currency-fee">55&nbsp;Ft</div>
      </div>

      <!-- RON -->
      <div class="op-currency-card">
        <div class="op-currency-flag">lei</div>
        <div class="op-currency-info">
          <div class="op-currency-code">RON</div>
          <div class="op-currency-name">Leu rumano</div>
        </div>
        <div class="op-currency-fee">0,70&nbsp;lei</div>
      </div>

      <!-- BGN -->
      <div class="op-currency-card">
        <div class="op-currency-flag">лв</div>
        <div class="op-currency-info">
          <div class="op-currency-code">BGN</div>
          <div class="op-currency-name">Lev búlgaro</div>
        </div>
        <div class="op-currency-fee">0,29&nbsp;лв</div>
      </div>

    </div>

  </div>
</div>


<!-- ========================= -->
<!-- PAÍSES DONDE OPERAMOS     -->
<!-- ========================= -->
<div class="section">
  <div class="section-inner">

    <div class="section-header">
      <div class="section-tag">Cobertura</div>
      <h2>Países con pagos activos. Plug &amp; play.</h2>
      <p>
        Los restaurantes ubicados en estos países pueden activar Order &amp; Pay de forma inmediata.
        Solo los países de esta lista pueden recibir pagos a través del menú.
      </p>
    </div>

    <!-- Mapa de regiones -->
    <div class="op-regions">

      <!-- EUROZONA -->
      <div class="op-region-card">
        <div class="op-region-header">
          <div class="op-region-label">Zona Euro</div>
          <div class="op-region-currency">EUR · 0,15&nbsp;€ / txn</div>
        </div>
        <div class="op-country-grid">
          <div class="op-country">Austria</div>
          <div class="op-country">Bélgica</div>
          <div class="op-country">Croacia</div>
          <div class="op-country">Chipre</div>
          <div class="op-country">Estonia</div>
          <div class="op-country">Finlandia</div>
          <div class="op-country">Francia</div>
          <div class="op-country">Alemania</div>
          <div class="op-country">Grecia</div>
          <div class="op-country">Irlanda</div>
          <div class="op-country">Italia</div>
          <div class="op-country">Letonia</div>
          <div class="op-country">Lituania</div>
          <div class="op-country">Luxemburgo</div>
          <div class="op-country">Malta</div>
          <div class="op-country">Países Bajos</div>
          <div class="op-country">Portugal</div>
          <div class="op-country">Eslovaquia</div>
          <div class="op-country">Eslovenia</div>
          <div class="op-country">España</div>
        </div>
        <div class="op-region-count">20 países</div>
      </div>

      <!-- EUROPA NO EUR -->
      <div class="op-region-card">
        <div class="op-region-header">
          <div class="op-region-label">Europa (fuera del euro)</div>
          <div class="op-region-currency">Moneda local</div>
        </div>
        <div class="op-country-detail-grid">
          <div class="op-country-detail">
            <span class="op-country-detail-name">Reino Unido</span>
            <span class="op-country-detail-fee">GBP · 0,15&nbsp;£</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Suiza</span>
            <span class="op-country-detail-fee">CHF · 0,16&nbsp;CHF</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Liechtenstein</span>
            <span class="op-country-detail-fee">CHF · 0,16&nbsp;CHF</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Suecia</span>
            <span class="op-country-detail-fee">SEK · 1,70&nbsp;kr</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Noruega</span>
            <span class="op-country-detail-fee">NOK · 1,70&nbsp;kr</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Dinamarca</span>
            <span class="op-country-detail-fee">DKK · 1,10&nbsp;kr</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Polonia</span>
            <span class="op-country-detail-fee">PLN · 0,65&nbsp;zł</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">República Checa</span>
            <span class="op-country-detail-fee">CZK · 3,50&nbsp;Kč</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Hungría</span>
            <span class="op-country-detail-fee">HUF · 55&nbsp;Ft</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Rumanía</span>
            <span class="op-country-detail-fee">RON · 0,70&nbsp;lei</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Bulgaria</span>
            <span class="op-country-detail-fee">BGN · 0,29&nbsp;лв</span>
          </div>
        </div>
        <div class="op-region-count">11 países</div>
      </div>

      <!-- AMÉRICAS -->
      <div class="op-region-card">
        <div class="op-region-header">
          <div class="op-region-label">Américas</div>
          <div class="op-region-currency">Moneda local</div>
        </div>
        <div class="op-country-detail-grid">
          <div class="op-country-detail">
            <span class="op-country-detail-name">Estados Unidos</span>
            <span class="op-country-detail-fee">USD · 0,19&nbsp;$</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Canadá</span>
            <span class="op-country-detail-fee">CAD · 0,24&nbsp;CA$</span>
          </div>
        </div>
        <div class="op-region-count">2 países</div>
      </div>

      <!-- ASIA-PACÍFICO -->
      <div class="op-region-card">
        <div class="op-region-header">
          <div class="op-region-label">Asia-Pacífico</div>
          <div class="op-region-currency">Moneda local</div>
        </div>
        <div class="op-country-detail-grid">
          <div class="op-country-detail">
            <span class="op-country-detail-name">Australia</span>
            <span class="op-country-detail-fee">AUD · 0,24&nbsp;AU$</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Nueva Zelanda</span>
            <span class="op-country-detail-fee">NZD · 0,26&nbsp;NZ$</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Japón</span>
            <span class="op-country-detail-fee">JPY · 24&nbsp;¥</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Singapur</span>
            <span class="op-country-detail-fee">SGD · 0,22&nbsp;S$</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Hong Kong</span>
            <span class="op-country-detail-fee">HKD · 1,25&nbsp;HK$</span>
          </div>
          <div class="op-country-detail">
            <span class="op-country-detail-name">Malasia</span>
            <span class="op-country-detail-fee">MYR · 0,72&nbsp;RM</span>
          </div>
        </div>
        <div class="op-region-count">6 países</div>
      </div>

    </div>

    <!-- Total -->
    <div class="op-total-bar">
      <div class="op-total-number">39</div>
      <div class="op-total-text">
        <strong>países con pagos activos</strong>
        <span>19 monedas · 4 regiones · comisión fija por transacción</span>
      </div>
    </div>

  </div>
</div>


<!-- ========================= -->
<!-- HERRAMIENTAS DE CONTROL   -->
<!-- ========================= -->
<div class="section">
  <div class="section-inner">

    <div class="section-header">
      <div class="section-tag">Control del restaurante</div>
      <h2>Recargo de servicio. Pedido mínimo. Tus reglas.</h2>
      <p>
        Cada restaurante controla cómo y cuándo se procesan pagos desde su panel.
        Dos herramientas diseñadas para que la economía de cada pedido tenga sentido.
      </p>
    </div>

    <div class="op-control-grid">

      <div class="op-control-card">
        <div class="op-control-icon">+</div>
        <h3>Recargo de servicio</h3>
        <p>
          Añade un importe fijo o porcentaje al total del pedido. El cliente lo ve como línea
          separada en el checkout. Cubre las comisiones o genera un margen extra. Tú decides el importe.
        </p>
        <div class="op-control-example">
           <div class="op-control-example-line">
            <span>Latte</span>
            <span>3,50&nbsp;€</span>
          </div>
          <div class="op-control-example-line">
            <span>Avocado Toast</span>
            <span>12,50&nbsp;€</span>
          </div>
          <div class="op-control-example-line op-control-example-line--fee">
            <span>Order &amp; Pay</span>
            <span>0,45&nbsp;€</span>
          </div>
          <div class="op-control-example-line op-control-example-line--total">
            <span>Total</span>
            <span>16,45&nbsp;€</span>
          </div>
        </div>
      </div>

      <div class="op-control-card">
        <div class="op-control-icon">≥</div>
        <h3>Pedido mínimo</h3>
        <p>
          Establece un importe mínimo de cesta. Si el cliente no lo alcanza, el botón de pago
          se bloquea y se le invita a añadir más artículos. Protege márgenes en cestas pequeñas.
        </p>
        <div class="op-control-combos">
          <div class="op-control-combo">
            <strong>Mínimo 8&nbsp;€ + recargo 0,40&nbsp;€</strong>
            <span>Comisiones cubiertas, márgenes intactos</span>
          </div>
          <div class="op-control-combo">
            <strong>Mínimo 10&nbsp;€ + recargo 1,00&nbsp;€</strong>
            <span>Generas ingreso extra por cada pedido</span>
          </div>
          <div class="op-control-combo">
            <strong>Sin mínimo + sin recargo</strong>
            <span>También funciona si tu ticket medio ya es alto</span>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>


<!-- ========================= -->
<!-- ALTO VOLUMEN              -->
<!-- ========================= -->
<div class="section">
  <div class="section-inner">

    <div class="op-volume-card">
      <h3>¿Alto volumen? Tarifas personalizadas.</h3>
      <p>
        Si tu restaurante o cadena procesa un volumen alto de transacciones, podemos negociar tarifas
        adaptadas a tu operación. Contacta con nosotros para una propuesta a medida.
      </p>
      <div class="op-volume-pills">
        <div class="stack-pill">Cadenas y grupos — volumen agregado</div>
        <div class="stack-pill">Agencias con muchos clientes — condiciones especiales</div>
        <div class="stack-pill">Contacto directo — sin formularios</div>
      </div>
    </div>

  </div>
</div>


<!-- ========================= -->
<!-- PAÍS NO DISPONIBLE        -->
<!-- ========================= -->
<div class="section">
  <div class="section-inner">

    <div class="op-fallback-card">
      <h3>¿Tu país no está en la lista?</h3>
      <p>
        El menú de MaxMenu (sin pagos) funciona en casi todos los paises, Si tu país no aparece arriba,
        puedes usar MaxMenu como menú digital completo — con personalización, alérgenos, traducciones y widget embebible —
        pero sin la funcionalidad de Order &amp; Pay.
      </p>
    </div>

  </div>
</div>


<style>

  /* ============================================
   ORDER & PAY — PRICING SECTIONS ONLY
   Extends existing dark theme
   ============================================ */

/* ── Section base ── */
.section {
  padding: 80px 24px;
  overflow-x: hidden;
}

.section-inner {
  max-width: 1120px;
  margin: 0 auto;
  overflow-x: hidden;
}

.section-header {
  max-width: 720px;
  margin-bottom: 48px;
}

.section-header h2 {
  font-size: 28px;
  font-weight: 900;
  line-height: 1.2;
  letter-spacing: -0.02em;
  margin: 0 0 14px;
}

.section-header p {
  font-size: 15px;
  opacity: 0.5;
  line-height: 1.7;
  margin: 0;
}

.section-tag {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #872cffff;
  background: rgba(100, 0, 222, 0.08);
  border: 1px solid rgba(63, 0, 200, 0.2);
  padding: 5px 14px;
  border-radius: 100px;
  margin-bottom: 16px;
}

/* ── Flow steps ── */
.op-flow {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 20px;
  padding: 36px 32px;
}

.op-flow-header h3 {
  font-size: 18px;
  font-weight: 800;
  margin-bottom: 6px;
}

.op-flow-header p {
  font-size: 14px;
  opacity: 0.55;
  margin-bottom: 28px;
}

.op-flow-steps {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.op-flow-step {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 16px 18px;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 12px;
}

.op-flow-number {
  width: 32px;
  height: 32px;
  min-width: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background:#872cffff;
  color: #000;
  font-size: 14px;
  font-weight: 800;
  border-radius: 50%;
}

.op-flow-content strong {
  display: block;
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 3px;
}

.op-flow-content span {
  font-size: 13px;
  opacity: 0.55;
  line-height: 1.5;
}

.op-flow-connector {
  width: 2px;
  height: 16px;
  background: rgba(255,255,255,0.08);
  margin-left: 33px;
}


/* ── Currency grid ── */
.op-currency-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(min(260px, 100%), 1fr));
  gap: 10px;
}

.op-currency-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 18px;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 12px;
  transition: border-color 0.2s, background 0.2s;
  min-width: 0;
  overflow: hidden;
}

.op-currency-card:hover {
  border-color: rgba(255,255,255,0.15);
  background: rgba(255, 255, 255, 0.05);
}

.op-currency-card--featured {
  border-color: rgba(87, 0, 200, 0.3);
  background: rgba(67, 0, 200, 0.04);
}

.op-currency-card--featured:hover {
  border-color: rgba(70, 0, 200, 0.5);
  background: rgba(87, 0, 200, 0.07);
}

.op-currency-flag {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 10px;
  font-size: 16px;
  font-weight: 700;
  color: rgba(255,255,255,0.7);
  flex-shrink: 0;
}

.op-currency-card--featured .op-currency-flag {
  background: rgba(66, 0, 180, 0.1);
  border-color: rgba(89, 6, 222, 0.25);
  color: #872cffff;
}

.op-currency-info {
  flex: 1;
  min-width: 0;
}

.op-currency-code {
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 0.03em;
}

.op-currency-name {
  font-size: 12px;
  opacity: 0.45;
  overflow: hidden;
  text-overflow: ellipsis;
}

.op-currency-fee {
  font-size: 15px;
  font-weight: 800;
  color: #872cffff;
  white-space: nowrap;
  letter-spacing: -0.01em;
}


/* ── Region cards (countries) ── */
.op-regions {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.op-region-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 16px;
  padding: 28px 24px;
  display: flex;
  flex-direction: column;
}

.op-region-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 20px;
  padding-bottom: 14px;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}

.op-region-label {
  font-size: 16px;
  font-weight: 800;
}

.op-region-currency {
  font-size: 12px;
  font-weight: 600;
  color: #872cffff;
  background: rgba(0,200,83,0.08);
  border: 1px solid rgba(63, 0, 200, 0.2);
  padding: 3px 10px;
  border-radius: 100px;
  letter-spacing: 0.02em;
}

.op-country-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 16px;
}

.op-country {
  font-size: 12px;
  font-weight: 500;
  padding: 5px 12px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 100px;
  opacity: 0.75;
  transition: opacity 0.2s, border-color 0.2s;
}

.op-country:hover {
  opacity: 1;
  border-color: rgba(255,255,255,0.15);
}

.op-country-detail-grid {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 16px;
}

.op-country-detail {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 14px;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 10px;
  transition: border-color 0.2s;
}

.op-country-detail:hover {
  border-color: rgba(255,255,255,0.12);
}

.op-country-detail-name {
  font-size: 13px;
  font-weight: 600;
}

.op-country-detail-fee {
  font-size: 12px;
  font-weight: 700;
  color: #872cffff;
  letter-spacing: 0.01em;
}

.op-region-count {
  font-size: 11px;
  font-weight: 600;
  opacity: 0.35;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-top: auto;
}


/* ── Total bar ── */
.op-total-bar {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-top: 24px;
  padding: 24px 28px;
  background: rgba(86, 0, 225, 0.05);
  border: 1px solid rgba(96, 1, 237, 0.18);
  border-radius: 14px;
}

.op-total-number {
  font-size: 42px;
  font-weight: 900;
  color: #872cffff;
  line-height: 1;
  letter-spacing: -0.03em;
}

.op-total-text strong {
  display: block;
  font-size: 15px;
  font-weight: 700;
  margin-bottom: 2px;
}

.op-total-text span {
  font-size: 13px;
  opacity: 0.5;
}


/* ── Control cards (surcharge + minimum) ── */
.op-control-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.op-control-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 16px;
  padding: 28px 24px;
}

.op-control-icon {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 10px;
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 16px;
  color: rgba(255,255,255,0.6);
}

.op-control-card h3 {
  font-size: 16px;
  font-weight: 800;
  margin-bottom: 8px;
}

.op-control-card p {
  font-size: 13px;
  opacity: 0.55;
  line-height: 1.6;
  margin-bottom: 20px;
}

/* Surcharge receipt example */
.op-control-example {
  background: rgba(0,0,0,0.3);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 10px;
  padding: 14px 16px;
}

.op-control-example-line {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  padding: 6px 0;
  opacity: 0.6;
}

.op-control-example-line--fee {
  opacity: 0.45;
  font-style: italic;
}

.op-control-example-line--total {
  opacity: 1;
  font-weight: 800;
  padding-top: 10px;
  margin-top: 6px;
  border-top: 1px solid rgba(255,255,255,0.08);
  color: #872cffff;
}

/* Minimum combos */
.op-control-combos {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.op-control-combo {
  padding: 12px 14px;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 10px;
}

.op-control-combo strong {
  display: block;
  font-size: 13px;
  font-weight: 700;
  margin-bottom: 2px;
}

.op-control-combo span {
  font-size: 12px;
  opacity: 0.45;
}


/* ── Volume card ── */
.op-volume-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 16px;
  padding: 32px 28px;
}

.op-volume-card h3 {
  font-size: 18px;
  font-weight: 800;
  margin-bottom: 8px;
}

.op-volume-card p {
  font-size: 14px;
  opacity: 0.55;
  line-height: 1.65;
  margin-bottom: 18px;
}

.op-volume-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.op-volume-pills .stack-pill,
.stack-pill {
  font-size: 12px;
  font-weight: 500;
  padding: 6px 14px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 100px;
  opacity: 0.65;
}


/* ── Fallback card ── */
.op-fallback-card {
  background: rgba(255,255,255,0.02);
  border: 1px dashed rgba(255,255,255,0.1);
  border-radius: 16px;
  padding: 28px 24px;
}

.op-fallback-card h3 {
  font-size: 16px;
  font-weight: 800;
  margin-bottom: 8px;
}

.op-fallback-card p {
  font-size: 13px;
  opacity: 0.5;
  line-height: 1.65;
}


/* ── Responsive ── */
@media (max-width: 768px) {
  .op-regions,
  .op-control-grid {
    grid-template-columns: 1fr;
  }

  .op-currency-grid {
    grid-template-columns: 1fr 1fr;
  }

  .op-total-bar {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }
}

@media (max-width: 480px) {
  .op-currency-grid {
    grid-template-columns: 1fr;
  }
}

</style>