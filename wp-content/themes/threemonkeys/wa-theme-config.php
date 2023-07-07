<?php
/*
* Author: Daniel Medina
* Author URI: https://webadictos.com
* Description: Archivo de configuración del tema
*Version: 1.0.0
*/
$GLOBALS['theme_helpers'] = array(
    'main-term',
    'utils',
    'articles',
);

/**
 * Se declara una variable global con los módulos que queremos activar en el tema.
 * Los módulos deben cargarse dentro del directorio inc/modules del tema
 */

$GLOBALS['theme_modules'] = array(
    'infinite-scroll' => array(
        'active' => true,
        'config' => array(
            'show_in_front' => true,
        ),
    ),
    'ads' => array(
        'active' => true,
        'config' => array(),
    ),
    'social' => array(
        'active' => true,
        'config' => array(),
    ),
    'optimizacion' => array(
        'active' => true,
        'config' => array(
            'disable_photon_opengraph' => false,
        ),
    ),
    'shortcodes' => array(
        'active' => true,
        'config' => array(),
    ),
    'promoted' => array(
        'active' => true,
        'config' => array(
            'show_in_front' => true,
        ),
    ),
    'video-channel' => array(
        'active' => true,
        'config' => array(),
    ),
    'maps' => array(
        'active' => true,
        'config' => array(),
    ),
);
