<?php
// =============================
// get_typography.php (final corregido)
// =============================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Defaults si no hay tipografías guardadas.
 */

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

/**
 * Catálogo oficial MaxMenu (familias y pesos reales)
 */
$MM_FONT_CATALOG = [
  'Cormorant SC' => [300,400,500,600,700],
  'Tangerine'    => [400,700],
  'Outfit'       => [300,400,500,600,700,800,900],
  'Marcellus SC' => [400],
  'Lexend Exa'   => [300,400,500,600,700,800,900],
];

// Si no tenemos restaurantId, salimos
if (!isset($restaurantId)) {
  error_log("⚠️ [get_typography] restaurantId no definido en la sesión.");
  return;
}

/**
 * Fuente de datos:
 * - preferente: $menuTypography (desde usuario-service.php o latest.json)
 */
$source = $menuTypography ?? ($restaurantData['menu_typography'] ?? []);

/**
 * 🧩 CORRECCIÓN CLAVE
 * Si el JSON devuelve una lista (array indexado), tomamos el primer registro.
 */
if (isset($source[0]) && is_array($source[0])) {
    $source = $source[0];
}

// Helper para detectar arrays asociativos
$isAssoc = static function($v) {
  return is_array($v) && array_keys($v) !== range(0, count($v) - 1);
};

// Helpers de normalización
$clampSize = static function($v, $def) {
  $n = (int)($v ?? $def);
  if ($n < 10) $n = 10;
  if ($n > 99) $n = 99;
  return $n;
};
$pickFontSafe = static function($font, $catalog, $def) {
  $f = trim((string)($font ?? ''));
  return array_key_exists($f, $catalog) ? $f : $def;
};
$nearestWeight = static function($w, array $allowed, $def) {
  $w = (int)($w ?? $def);
  if (in_array($w, $allowed, true)) return $w;
  $best = $allowed[0];
  $bestDiff = abs($w - $best);
  foreach ($allowed as $opt) {
    $d = abs($w - $opt);
    if ($d < $bestDiff) {
      $best = $opt;
      $bestDiff = $d;
    }
  }
  return $best;
};

// Mapper principal
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

// Procesar fuente de datos
if ($source && is_array($source)) {
  if ($isAssoc($source)) {
    $tipografias = $map($source);
  } else {
    error_log("⚠️ [get_typography] Estructura inesperada en menu_typography.");
  }
} else {
  error_log("ℹ️ [get_typography] Sin tipografías, usando defaults.");
}