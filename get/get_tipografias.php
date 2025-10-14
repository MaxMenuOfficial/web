<?php
// =============================
// get_tipografias.php (final para producción)
// =============================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if (!isset($restaurantId)) {
  error_log("⚠️ [get_tipografias] restaurantId no definido.");
  return;
}

$source = $menuTypography ?? ($restaurantData['menu_typography'] ?? []);
if (isset($source[0]) && is_array($source[0])) $source = $source[0];

$isAssoc = static function($v) {
  return is_array($v) && array_keys($v) !== range(0, count($v) - 1);
};

$clampSize = static function($v, $def) {
  $n = (int)($v ?? $def);
  return max(10, min(99, $n));
};

$pick = static function($font, $def) {
  $f = trim((string)($font ?? ''));
  return $f !== '' ? $f : $def;
};

$getWeight = static function($w, $def) {
  $w = (int)($w ?? $def);
  return ($w >= 100 && $w <= 1000) ? $w : $def;
};

$map = function(array $row) use ($tipografias, $pick, $getWeight, $clampSize) {
  return [
    'titleFont'   => $pick($row['title_font'], $tipografias['titleFont']),
    'titleWeight' => $getWeight($row['title_weight'], $tipografias['titleWeight']),
    'titleSize'   => $clampSize($row['title_size'], $tipografias['titleSize']),
    'bodyFont'    => $pick($row['body_font'],  $tipografias['bodyFont']),
    'bodyWeight'  => $getWeight($row['body_weight'], $tipografias['bodyWeight']),
    'bodySize'    => $clampSize($row['body_size'],  $tipografias['bodySize']),
    'priceFont'   => $pick($row['price_font'], $tipografias['priceFont']),
    'priceWeight' => $getWeight($row['price_weight'], $tipografias['priceWeight']),
    'priceSize'   => $clampSize($row['price_size'], $tipografias['priceSize']),
  ];
};

if ($source && is_array($source)) {
  if ($isAssoc($source)) {
    $tipografias = $map($source);
  } else {
    error_log("⚠️ [get_tipografias] Estructura inesperada.");
  }
} else {
  error_log("ℹ️ [get_tipografias] Sin tipografías. Usando defaults.");
}
?>