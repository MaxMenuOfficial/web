<?php
// === Detección automática de moneda según país (Cloudflare) ===
$country = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? 'US';
$country = strtoupper(trim($country));

// --- Definición de grupos ---
$EU_COUNTRIES = [
    'AT','BE','BG','HR','CY','CZ','DK','EE','FI','FR','DE','GR','HU','IE','IT',
    'LV','LT','LU','MT','NL','PL','PT','RO','SK','SI','ES','SE'
];

$GBP_COUNTRIES = ['GB','UK','GG','JE','IM']; // Reino Unido + dependencias
$CHF_COUNTRIES = ['CH','LI']; // Suiza + Liechtenstein
$USD_COUNTRIES = ['US','CA','MX','AR','CL','CO','PE','UY','PY','BO','CR','EC','GT','HN','NI','PA','DO','VE']; // América

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

    // Espacio no divisible (mantiene número y símbolo juntos)
    $space = '&nbsp;';

    // Suiza, Reino Unido y América anteponen el símbolo
    if (in_array($code, ['GBP','CHF','USD'], true)) {
        return $symbol . $space . $formatted;
    }
    // Europa (EUR) lo coloca detrás del número
    return $formatted . $space . $symbol;
}
?>

<main class="contenedor-principal">

  <div class="back-edit">
    <h2 class="titulo">Plans built around how far you want to go with your menu.</h2>
  </div>

  <div class="planes-contenedor">
	 
    <!-- FREE -->
    <article class="pricing-option" id="plan-free">

      <a href="https://manage.maxmenu.com/login" class="btn btn-free">Start for free</a>

      <h2>Free design playground</h2>
      <h3>Design the entire menu with no limits. Publish later, when you’re ready.</h3>

      <p class="price">
        <?php echo renderLocalizedPrice('0.00', $currencySymbol, $decimalSeparator, $currencyCode); ?>
        <span>/ month</span>
      </p>

      <ul class="features">
        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Full access to the MaxMenu designer: colors, panels, typography, borders, spacing, allergens and images.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Create unlimited categories, subcategories, brunch sections, daily menus and items.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Configure all languages, allergens and icons exactly as in the paid plans (design side only).</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Preview the menu in desktop and mobile views as many times as you want.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Full visual control: colors, multiple accent palettes, typography families, weights and sizes for titles, descriptions and prices.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Borders and radius at detail level: card edges, separators, outlines and corner radius per surface.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Layout & spacing controls: density, vertical rhythm between categories, items and sections.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Toggle items on/off in real time (out of stock, temporary dishes, events) without editing the structure.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Real-time edits: guests always see the latest version without reloading QR codes.</span>
        </li>
      

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Save all your work and keep iterating on the design without time limits.</span>
        </li>

        <li class="criteria">
          <i class="fa fa-times icon"></i>
          <span>No public menu: no QR code, no public URL, guests cannot access the menu until you upgrade.</span>
        </li>
      </ul>

    </article>
	  
    <!-- BASIC -->
    <article class="pricing-option" id="plan-basic">

      <a href="https://manage.maxmenu.com/login" class="btn btn-basic">Start Basic</a>

      <h2>Basic</h2>
      <h3>Publish a fully customised menu on a MaxMenu URL, with automatic translations to 13 languages.</h3>

      <p class="price">
        <?php echo renderLocalizedPrice('13.99', $currencySymbol, $decimalSeparator, $currencyCode); ?>
        <span>/ month</span>
      </p>

      <ul class="features">
        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Everything in the Free design playground, but now live for your guests.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Public menu hosted on MaxMenu with a clean, shareable URL for your restaurant.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Automatic translation of the entire menu to 13 languages</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Unlimited QR scans pointing to your MaxMenu URL.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Dedicated URL</span>
        </li>
      </ul>

    </article>

    <!-- MAX -->
    <article class="pricing-option" id="plan-pro">

      <a href="https://manage.maxmenu.com/login" class="btn btn-basic">Start Max</a>

      <h2>Max</h2>
      <h3>The full MaxMenu experience: embeddable widget, domain choice and automatic translation to 22 languages.</h3>

      <p class="price">
        <?php echo renderLocalizedPrice('19.99', $currencySymbol, $decimalSeparator, $currencyCode); ?>
        <span>/ month</span>
      </p>
		
      <ul class="features">
        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Everything in Basic, plus the full embeddable widget for your own website.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Choose how you publish: embed the widget on your own domain, use the MaxMenu URL, or use both at the same time.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Automatic translation to 22 languages, including Asian and RTL scripts, ready for tourism-heavy locations.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Domain control for the widget: only authorised domains can display your menu.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Same pixel-level design engine as in Basic, but optimised for embeddable menus that feel native to your website.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Transparent and full-bleed images: support for PNG dishes with no background over neutral or brand-colored panels.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Unlimited items, categories, brunch sections and daily menus, synced across the widget and the hosted URL.</span>
        </li>


        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Unlimited QR scans that can point either to the MaxMenu URL or directly to the widget on your own domain.</span>
        </li>

        <li class="criteria success">
          <i class="fa fa-check icon"></i>
          <span>Future-ready: ready to connect with item payments and loyalty once you decide to activate them.</span>
        </li>
      </ul>

    </article>

  </div>

</main>