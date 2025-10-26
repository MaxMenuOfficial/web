<?php
// File: /var/www/html/config/menu-visibility-widget.php

global $restaurantData, $restaurantId;

// 🔒 Flags de visibilidad actuales
$isActive       = !empty($restaurantData['is_active']);
$isActiveForAll = !empty($restaurantData['is_active_for_all']);

// 🏷️ Tier del plan (puede ser null). Normalizamos a minúsculas.
$planTier = strtolower(trim((string)($restaurantData['plan_tier'] ?? '')));

// 📌 Regla de negocio: el widget SOLO se muestra si:
//  - el restaurante está activo (is_active || is_active_for_all)
//  - y el plan es MAX **o** ELITE
$canShowWidget = ($isActive || $isActiveForAll) && in_array($planTier, ['max', 'elite'], true);

// Si NO está activo → mensaje original
if (!$isActive && !$isActiveForAll) {
    echo "<style>
        #menu-widget-unavailable {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 150px;
            background: #fff5f5; /* rojo muy claro */
            color: #c53030;      /* rojo fuerte */
            font-family: sans-serif;
            text-align: center;
            padding: 20px;
            box-sizing: border-box;
        }
        #menu-widget-unavailable h2 {
            margin: 0;
            font-size: 1.2em;
            font-weight: bold;
        }
    </style>
    <div id='menu-widget-unavailable'>
      <h2>This menu is not available. To resolve it, log in to your private area | MaxMenu</h2>
</div>";
    exit;
}

// Si está activo pero NO es MAX ni ELITE → bloquear widget y sugerir upgrade
if (!$canShowWidget) {
    echo "<style>
        #menu-widget-upgrade {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 150px;
            background: #f7fafc; /* gris azulado muy claro */
            color: #2d3748;      /* gris oscuro */
            font-family: sans-serif;
            text-align: center;
            padding: 20px;
            box-sizing: border-box;
            border: 1px dashed #a0aec0;
        }
        #menu-widget-upgrade h2 {
            margin: 0;
            font-size: 1.1em;
            font-weight: bold;
        }
    </style>
  <div id='menu-widget-upgrade'>
    <h2>The embeddable widget is available only on the Max or Elite plan. Upgrade your plan to activate it.</h2>
</div>";
    exit;
}

// ✅ Si llegamos aquí, el widget puede renderizarse normalmente.
