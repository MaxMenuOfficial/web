<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 🔡 Default tipografías
$tipografias = [
  'titleFont'   => 'Cormorant SC',
  'titleWeight' => 600,
  'titleSize'   => 20,
  'bodyFont'    => 'Outfit',
  'bodyWeight'  => 400,
  'bodySize'    => 15,
  'priceFont'   => 'Lexend Exa',
  'priceWeight' => 600,
  'priceSize'   => 16,
];

// 🎨 Catálogo oficial MaxMenu
$MM_FONT_CATALOG = [
  'Cormorant SC' => [300,400,500,600,700],
  'Tangerine'    => [400,700],
  'Outfit'       => [300,400,500,600,700,800,900],
  'Marcellus SC' => [400],
  'Lexend Exa'   => [300,400,500,600,700,800,900],
];

// 🛑 Comprobación
if (!isset($restaurantId)) {
  error_log("⚠️ [get_typography] restaurantId no definido en la sesión.");
  return;
}

// 🧬 Fuente de datos
$source = $menuTypography ?? ($restaurantData['menu_typography'] ?? []);

// 🔍 Log previo
echo "<h3>🧩 RAW desde base de datos:</h3><pre>";
print_r($source);
echo "</pre>";

// 🚨 Corrección si es array indexado
if (isset($source[0]) && is_array($source[0])) {
  $source = $source[0];
  echo "<h3>✅ Normalizado a primer objeto:</h3><pre>";
  print_r($source);
  echo "</pre>";
}

// 🎯 Helpers
$isAssoc = static fn($v) => is_array($v) && array_keys($v) !== range(0, count($v) - 1);
$clampSize = static fn($v, $def) => max(10, min(99, (int)($v ?? $def)));
$pickFontSafe = static fn($font, $catalog, $def) => array_key_exists(trim((string)($font ?? '')), $catalog) ? trim((string)$font) : $def;
$nearestWeight = static function($w, array $allowed, $def) {
  $w = (int)($w ?? $def);
  if (in_array($w, $allowed, true)) return $w;
  $best = $allowed[0];
  foreach ($allowed as $opt) {
    if (abs($w - $opt) < abs($w - $best)) $best = $opt;
  }
  return $best;
};

// 🧠 Map principal
$map = function(array $row) use ($tipografias, $MM_FONT_CATALOG, $pickFontSafe, $nearestWeight, $clampSize) {
  $titleFont = $pickFontSafe($row['title_font'] ?? null, $MM_FONT_CATALOG, $tipografias['titleFont']);
  $bodyFont  = $pickFontSafe($row['body_font']  ?? null, $MM_FONT_CATALOG, $tipografias['bodyFont']);
  $priceFont = $pickFontSafe($row['price_font'] ?? null, $MM_FONT_CATALOG, $tipografias['priceFont']);

  $titleWeight = $nearestWeight($row['title_weight'] ?? null, $MM_FONT_CATALOG[$titleFont], $tipografias['titleWeight']);
  $bodyWeight  = $nearestWeight($row['body_weight']  ?? null, $MM_FONT_CATALOG[$bodyFont],  $tipografias['bodyWeight']);
  $priceWeight = $nearestWeight($row['price_weight'] ?? null, $MM_FONT_CATALOG[$priceFont], $tipografias['priceWeight']);

  $titleSize = $clampSize($row['title_size'] ?? null, $tipografias['titleSize']);
  $bodySize  = $clampSize($row['body_size']  ?? null, $tipografias['bodySize']);
  $priceSize = $clampSize($row['price_size'] ?? null, $tipografias['priceSize']);

  return [
    'titleFont'   => $titleFont,
    'titleWeight' => $titleWeight,
    'titleSize'   => $titleSize,
    'bodyFont'    => $bodyFont,
    'bodyWeight'  => $bodyWeight,
    'bodySize'    => $bodySize,
    'priceFont'   => $priceFont,
    'priceWeight' => $priceWeight,
    'priceSize'   => $priceSize,
  ];
};

// 🛠 Aplicar mapeo
if ($source && is_array($source)) {
  if ($isAssoc($source)) {
    $tipografias = $map($source);

    echo "<h3>🎯 Tipografías mapeadas:</h3><pre>";
    print_r($tipografias);
    echo "</pre>";
  } else {
    echo "<h3>⚠️ Estructura inesperada:</h3><pre>";
    print_r($source);
    echo "</pre>";
  }
} else {
  echo "<h3>ℹ️ Sin datos, usando defaults</h3>";
}