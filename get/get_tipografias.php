<?php
// =============================
// get_typography.php (final)
// =============================


/**
 * Defaults por si no hay tipografías guardadas.
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

// Si no tenemos restaurantId, mantenemos defaults y salimos
if (!isset($restaurantId)) {
  error_log("⚠️ [get_typography] restaurantId no definido en la sesión.");
  return;
}

/**
 * Fuente de datos:
 * - preferente: $menuTypography (traído desde usuario-service.php)
 * - fallback: $restaurantData['menu_typography'] (latest.json)
 */
$source = $menuTypography ?? null;
if (!$source && isset($restaurantData['menu_typography'])) {
  $source = $restaurantData['menu_typography'];
}

// Helper: verificar si un array es asociativo
$isAssoc = static function($v){
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
  // Si el peso no es válido, usamos el más cercano permitido
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

// Mapper principal: normaliza cada campo y devuelve tipografías correctas
$map = function(array $row) use ($tipografias, $MM_FONT_CATALOG, $pickFontSafe, $nearestWeight, $clampSize) {
  // Fonts
  $titleFont = $pickFontSafe($row['title_font'] ?? null, $MM_FONT_CATALOG, $tipografias['titleFont']);
  $bodyFont  = $pickFontSafe($row['body_font']  ?? null, $MM_FONT_CATALOG, $tipografias['bodyFont']);
  $priceFont = $pickFontSafe($row['price_font'] ?? null, $MM_FONT_CATALOG, $tipografias['priceFont']);

  // Pesos por familia
  $titleWeight = $nearestWeight($row['title_weight'] ?? null, $MM_FONT_CATALOG[$titleFont], $tipografias['titleWeight']);
  $bodyWeight  = $nearestWeight($row['body_weight']  ?? null, $MM_FONT_CATALOG[$bodyFont],  $tipografias['bodyWeight']);
  $priceWeight = $nearestWeight($row['price_weight'] ?? null, $MM_FONT_CATALOG[$priceFont], $tipografias['priceWeight']);

  // Tamaños
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
if ($source) {
  if (is_array($source)) {
    if ($isAssoc($source)) {
      // latest.json
      $tipografias = $map($source);
    } else {
      // usuario-service.php: lista de filas
      $rows = array_values(array_filter($source, function($row) use ($restaurantId){
        return isset($row['restaurant_id']) ? $row['restaurant_id'] === $restaurantId : true;
      }));
      if (!empty($rows)) {
        $tipografias = $map($rows[0]);
      } else {
        error_log("ℹ️ [get_typography] No se encontró fila para {$restaurantId}. Usando defaults.");
      }
    }
  } else {
    error_log("⚠️ [get_typography] Fuente inválida (no array). Usando defaults.");
  }
} else {
  error_log("ℹ️ [get_typography] Sin fuente (menu_typography). Usando defaults.");
}