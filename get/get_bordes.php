<?php
// get_borders.php — normaliza datos de bordes

$bordes = [
  'borderStyle' => 'round', // round | semi | square
  'borderWidth' => 2,       // clamp 0..20
];

if (!isset($restaurantId)) {
  error_log("⚠️ [get_borders] restaurantId no definido en la sesión.");
  return;
}

// Fuente: primero $menuBorders (de menu-service.php), luego latest.json
$source = $menuBorders ?? null;
if (!$source && isset($restaurantData['menu_borders'])) {
  $source = $restaurantData['menu_borders'];
}

// Nada → defaults
if (!$source) {
  error_log("ℹ️ [get_borders] Sin fuente (menu_borders). Usando defaults.");
  return;
}

// Helper: asociativo vs lista
$isAssoc = static function($v){
  return is_array($v) && array_keys($v) !== range(0, count($v) - 1);
};

// Normalizar a una sola fila
$row = null;
if (is_array($source)) {
  if ($isAssoc($source)) {
    $row = $source;
  } else {
    // Lista (ARRAY<STRUCT>), tomar primera coincidencia
    $rows = array_values(array_filter($source, function($r) use ($restaurantId){
      return isset($r['restaurant_id']) ? $r['restaurant_id'] === $restaurantId : true;
    }));
    $row = $rows[0] ?? null;
  }
}

if ($row) {
  $style = $row['border_style'] ?? $bordes['borderStyle'];
  $width = (int)($row['border_width'] ?? $bordes['borderWidth']);
  if (!in_array($style, ['round','semi','square'], true)) $style = 'round';
  if ($width < 0) $width = 0;
  if ($width > 20) $width = 20;

  $bordes = [
    'borderStyle' => $style,
    'borderWidth' => $width,
  ];
} else {
  error_log("ℹ️ [get_borders] No se encontró fila para {$restaurantId}. Defaults.");
}
