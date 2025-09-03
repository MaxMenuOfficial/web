<?php
// Defaults



$bordes = [
  'borderStyle' => 'round', // round | semi | square
  'borderWidth' => 2,       // 0..20
];

if (!isset($restaurantId)) {
  error_log("⚠️ [get_borders] restaurantId no definido en la sesión.");
  return;
}

$source = $menuBorders ?? null;               // preferente: usuario-service.php
if (!$source && isset($restaurantData['menu_borders'])) {
  $source = $restaurantData['menu_borders'];  // fallback: latest.json ya decodificado
}

if (!$source) {
  error_log("ℹ️ [get_borders] Sin fuente (menu_borders). Usando defaults.");
  return;
}

// helper: ¿es un array asociativo (objeto) o lista?
$isAssoc = static function($v){
  return is_array($v) && array_keys($v) !== range(0, count($v) - 1);
};

if (is_array($source)) {
  if ($isAssoc($source)) {
    // Objeto único (latest.json típico)
    $row = $source;
  } else {
    // Lista de filas (usuario-service.php)
    $rows = array_values(array_filter($source, function($row) use ($restaurantId){
      return isset($row['restaurant_id']) ? $row['restaurant_id'] === $restaurantId : true;
    }));
    $row = $rows[0] ?? null;
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
} else {
  error_log("⚠️ [get_borders] Fuente inválida (no array). Defaults.");
}