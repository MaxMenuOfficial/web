<?php
// File: /var/www/html/config/menu-visibility-widget.php

global $restaurantData, $restaurantId;

// 🔒 Evaluamos flags de visibilidad
$isActive       = !empty($restaurantData['is_active']);
$isActiveForAll = !empty($restaurantData['is_active_for_all']);

// Caso menú oculto (ninguno activo)
if (!$isActive && !$isActiveForAll) {
    echo "<style>
        #menu-widget-unavailable {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 150px; /* fallback si el contenedor no define altura */
            background: #fff5f5; /* rojo muy claro */
            color: #c53030;       /* rojo fuerte */
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
        <h2> Este menú no está disponible, Para resolverlo ingresa en tu area privada | MaxMenu </h2>
    </div>";
    exit;
}