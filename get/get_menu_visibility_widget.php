<?php
// File: /var/www/html/config/menu-visibility.php

global $restaurantData, $restaurantId;

// 🔒 Evaluamos flags de visibilidad
$isActive       = !empty($restaurantData['is_active']);
$isActiveForAll = !empty($restaurantData['is_active_for_all']);

// Caso menú oculto (ninguno activo)
if (!$isActive && !$isActiveForAll) {
    echo "<style>
        /* Evitar scroll del body */
        body {
            overflow: hidden !important;
        }

        /* Overlay oscuro */
        #menu-locked-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5); /* negro con 50% opacidad */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999; /* siempre encima */
        }

        /* Caja del modal */
        #menu-locked-modal {
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            font-family: sans-serif;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        #menu-locked-modal h1 {
            font-size: 1.6em;
            margin-bottom: 15px;
        }

        #menu-locked-modal p {
            color: #555;
            font-size: 1.1em;
            margin-bottom: 25px;
        }

        #menu-locked-modal a {
            display: inline-block;
            background: #007BFF;
            color: #fff;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
            transition: background 0.2s;
        }

        #menu-locked-modal a:hover {
            background: #0056b3;
        }
    </style>
    <div id='menu-locked-overlay'>
        <div id='menu-locked-modal'>
            <h1>Este menú no está disponible temporalmente</h1>
            <p>Gracias por tu paciencia.</p>
            <a href='https://maxmenu.com'>Cerrar</a>
        </div>
    </div>";
}