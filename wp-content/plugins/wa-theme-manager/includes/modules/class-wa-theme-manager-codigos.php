<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://webadictos.com
 * @since      1.0.0
 *
 * @package    Wa_Theme_Manager_Portada
 * @subpackage Wa_Theme_Manager/includes
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

/**
 * Incluye scripts y códigos externos en el sitio.
 *
 * @package WA Theme Manager
 * @author  Daniel Medina <dmedina@forbes.com.mx>
 */

if (!class_exists('Wa_Theme_Manager_Codigos')) {

    class Wa_Theme_Manager_Codigos
    {

        /**
         * Plugin version.
         *
         * @var string
         */
        const VERSION = '1.0';

        /**
         * Instance of this class.
         *
         * @var object
         */
        protected static $instance = null;

        protected $prefix = "wa_theme_options_";

        /**
         * Initialize the plugin.
         */
        function __construct()
        {

            add_action('wp_head', array($this, 'printCodigosHead'));
            add_action('wp_footer', array($this, 'printCodigosFooter'));
            add_action('wp_body_open', array($this, 'printCodigosBody'));
            add_filter('wa_theme_set_options_page', array($this, 'initOptionsPage'), 11, 1);
        }

        public function initOptionsPage($optionsPage)
        {

            $codeOptionsPage = array();

            $codeFields = array(
                $this->prefix . 'codes_header' => array(
                    'id'          => $this->prefix . 'codes_header',
                    'type'        => 'group',
                    'title' => 'Códigos antes del &lt;/head&gt;',
                    'desc'       => 'Códigos antes del  &lt;/head&gt;',
                    'repeatable'  => true,
                    'options'     => array(
                        'group_title'   => 'Código {#}',
                        'add_button'    => 'Agregar Código',
                        'remove_button' => 'Quitar Código',
                        'closed'        => true,
                        'sortable'      => true,
                    ),
                    'tab_icon' => 'dashicons-editor-code',
                    'tab_name' => 'Header',
                    'wa_group_fields' => apply_filters('wa_theme_get_' . $this->prefix . 'codes_header_fields', array(
                        'identificador' => array(
                            'name' => 'Identificador',
                            'desc' => 'Escribe un identificador para el código',
                            'id'   => 'identificador',
                            'type' => 'text',
                        ),
                        'codigo' =>  array(
                            'name' => 'Código',
                            'desc' => 'Código a insertar antes del &lt;/head&gt;',
                            'id'   => 'codigo',
                            'type' => 'textarea',
                            'sanitization_cb' => array('Wa_Theme_Manager', 'accept_html_values_sanitize'),
                        ),
                        'mostrar_en' => array(
                            'name'             => 'Mostrar en',
                            'id'               => 'mostrar_en',
                            'type'             => 'select',
                            'desc' => "Selecciona donde se debe desplegar el código",
                            'show_option_none' => false,
                            'options'          => array(
                                'ros'   => 'Todo el sitio',
                                'single'   => 'En artículos solamente',
                                'archive' => 'En canales solamente',
                                'page' => 'En páginas',
                                'search' => 'En búsquedas',

                            )
                        ),
                        'post_ids' => array(
                            'name'             => 'Post IDS',
                            'id'               => 'posts_ids',
                            'type'        => 'post_search_text',
                            'post_type'   => array('post', 'page'),
                            'select_type' => 'checkbox',
                            'select_behavior' => 'add',
                            'desc' => "Escribe las IDS de los posts/páginas donde se debe mostrar",
                        ),
                        'enable_code' => array(
                            'name' => 'Habilitar',
                            'id'   => 'enable_code',
                            'type' => 'checkbox',
                            'desc' => "Marque si desea activar este código",
                        ),



                    )),

                ),

                $this->prefix . 'codes_body' => array(
                    'id'          => $this->prefix . 'codes_body',
                    'type'        => 'group',
                    'title' => 'Códigos al inicio de &lt;body&gt;',
                    'desc'       => 'Códigos al inicio de &lt;body&gt;',
                    'repeatable'  => true,
                    'options'     => array(
                        'group_title'   => 'Código {#}',
                        'add_button'    => 'Agregar Código',
                        'remove_button' => 'Quitar Código',
                        'closed'        => true,  // Repeater fields closed by default - neat & compact.
                        'sortable'      => true,  // Allow changing the order of repeated groups.
                    ),
                    'tab_icon' => 'dashicons-editor-code',
                    'tab_name' => 'Body',
                    'wa_group_fields' => apply_filters('wa_theme_get_' . $this->prefix . 'codes_body_fields', array(
                        'identificador' => array(
                            'name' => 'Identificador',
                            'desc' => 'Escribe un identificador para el código',
                            'id'   => 'identificador',
                            'type' => 'text',
                        ),
                        'codigo' =>  array(
                            'name' => 'Código',
                            'desc' => 'Código a insertar antes del &lt;/head&gt;',
                            'id'   => 'codigo',
                            'type' => 'textarea',
                            'sanitization_cb' => array('Wa_Theme_Manager', 'accept_html_values_sanitize'),
                        ),
                        'mostrar_en' => array(
                            'name'             => 'Mostrar en',
                            'id'               => 'mostrar_en',
                            'type'             => 'select',
                            'desc' => "Selecciona donde se debe desplegar el código",
                            'show_option_none' => false,
                            'options'          => array(
                                'ros'   => 'Todo el sitio',
                                'single'   => 'En artículos solamente',
                                'archive' => 'En canales solamente',
                                'page' => 'En páginas',
                                'search' => 'En búsquedas',

                            )
                        ),
                        'post_ids' => array(
                            'name'             => 'Post IDS',
                            'id'               => 'posts_ids',
                            'type'        => 'post_search_text', // This field type
                            // post type also as array
                            'post_type'   => array('post', 'page'),
                            // Default is 'checkbox', used in the modal view to select the post type
                            'select_type' => 'checkbox',
                            // Will replace any selection with selection from modal. Default is 'add'
                            'select_behavior' => 'add',
                            'desc' => "Escribe las IDS de los posts/páginas donde se debe mostrar",
                        ),
                        'enable_code' => array(
                            'name' => 'Habilitar',
                            'id'   => 'enable_code',
                            'type' => 'checkbox',
                            'desc' => "Marque si desea activar este código",
                        ),



                    )),

                ),
                $this->prefix . 'codes_footer' => array(
                    'id'          => $this->prefix . 'codes_footer',
                    'type'        => 'group',
                    'title' => 'Códigos antes de &lt;/body&gt;',
                    'desc'       => 'Códigos antes de &lt;/body&gt;',
                    'repeatable'  => true,
                    'options'     => array(
                        'group_title'   => 'Código {#}',
                        'add_button'    => 'Agregar Código',
                        'remove_button' => 'Quitar Código',
                        'closed'        => true,  // Repeater fields closed by default - neat & compact.
                        'sortable'      => true,  // Allow changing the order of repeated groups.
                    ),
                    'tab_icon' => 'dashicons-editor-code',
                    'tab_name' => 'Footer',
                    'wa_group_fields' => apply_filters('wa_theme_get_' . $this->prefix . 'codes_footer_fields', array(
                        'identificador' => array(
                            'name' => 'Identificador',
                            'desc' => 'Escribe un identificador para el código',
                            'id'   => 'identificador',
                            'type' => 'text',
                        ),
                        'codigo' =>  array(
                            'name' => 'Código',
                            'desc' => 'Código a insertar antes del &lt;/head&gt;',
                            'id'   => 'codigo',
                            'type' => 'textarea',
                            'sanitization_cb' => array('Wa_Theme_Manager', 'accept_html_values_sanitize'),
                        ),
                        'mostrar_en' => array(
                            'name'             => 'Mostrar en',
                            'id'               => 'mostrar_en',
                            'type'             => 'select',
                            'desc' => "Selecciona donde se debe desplegar el código",
                            'show_option_none' => false,
                            'options'          => array(
                                'ros'   => 'Todo el sitio',
                                'single'   => 'En artículos solamente',
                                'archive' => 'En canales solamente',
                                'page' => 'En páginas',
                                'search' => 'En búsquedas',

                            )
                        ),
                        'post_ids' => array(
                            'name'             => 'Post IDS',
                            'id'               => 'posts_ids',
                            'type'        => 'post_search_text',
                            'post_type'   => array('post', 'page'),
                            'select_type' => 'checkbox',
                            'select_behavior' => 'add',
                            'desc' => "Escribe las IDS de los posts/páginas donde se debe mostrar",
                        ),
                        'enable_code' => array(
                            'name' => 'Habilitar',
                            'id'   => 'enable_code',
                            'type' => 'checkbox',
                            'desc' => "Marque si desea activar este código",
                        ),



                    )),
                )
            );


            $codeOptionsPage = array(
                'id'           => $this->prefix . 'codes_page',
                'title'        => esc_html__('Códigos', 'cmb2'),
                'object_types' => array('options-page'),
                'option_key'   => $this->prefix . 'codes',
                'parent_slug'  => 'wa_theme_options',
                'capability'      => 'manage_options',
                'vertical_tabs' => true,
                'has_tabs' => true,
                'wa_fields' => apply_filters('wa_theme_get_wa_theme_options_codes_page_fields', $codeFields),
            );

            $optionsPage[$this->prefix . 'codes_page'] = $codeOptionsPage;

            return $optionsPage;
        }



        public function printCodigosHead()
        {

            $codes = Wa_Theme_Manager::get_opciones('wa_theme_options_codes', 'wa_theme_options_codes_header');

            if (is_array($codes)) {
                foreach ($codes as $code) {

                    switch ($code['mostrar_en']) {
                        case "ros":
                            if (isset($code['enable_code']) && $code['enable_code'] == "on") {
                                echo "<!--" . $code['identificador'] . "-->\n";
                                echo $code['codigo'] . "\n";
                                echo "<!-- Fin " . $code['identificador'] . "-->\n";
                            }

                            break;

                        case "single":
                            if (is_single()) {


                                $mustShow = true;
                                $idsToShow = explode(",", $code['posts_ids']);
                                if (is_array($idsToShow) && count($idsToShow) > 0) {
                                    $mustShow = false;
                                    if (in_array(get_the_ID(), $idsToShow)) {
                                        $mustShow = true;
                                    }
                                }



                                if (isset($code['enable_code']) && $code['enable_code'] == "on" && $mustShow) {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;

                        case "archive":
                            if (is_archive()) {
                                if (isset($code['enable_code']) && $code['enable_code'] == "on") {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;

                        case "page":
                            if (is_page()) {
                                $mustShow = true;
                                $idsToShow = explode(",", $code['posts_ids']);
                                if (is_array($idsToShow) && count($idsToShow) > 0) {
                                    $mustShow = false;
                                    if (in_array(get_the_ID(), $idsToShow)) {
                                        $mustShow = true;
                                    }
                                }

                                if (isset($code['enable_code']) && $code['enable_code'] == "on" && $mustShow) {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;

                        case "search":
                            if (is_search()) {
                                if (isset($code['enable_code']) && $code['enable_code'] == "on") {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;
                    }
                }
            }
        }



        public function printCodigosFooter()
        {

            $codes = Wa_Theme_Manager::get_opciones('wa_theme_options_codes', 'wa_theme_options_codes_footer');

            if (is_array($codes)) {
                foreach ($codes as $code) {
                    switch ($code['mostrar_en']) {
                        case "ros":
                            if (isset($code['enable_code']) && $code['enable_code'] == "on") {

                                echo "<!--" . $code['identificador'] . "-->\n";
                                echo $code['codigo'] . "\n";
                                echo "<!-- Fin " . $code['identificador'] . "-->\n";
                            }

                            break;

                        case "single":
                            if (is_single()) {
                                if (isset($code['enable_code']) && $code['enable_code'] == "on") {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;

                        case "archive":
                            if (is_archive()) {
                                if (isset($code['enable_code']) && $code['enable_code'] == "on") {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;

                        case "page":
                            if (is_page()) {
                                if (isset($code['enable_code']) && $code['enable_code'] == "on") {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;

                        case "search":
                            if (is_search()) {
                                if (isset($code['enable_code']) && $code['enable_code'] == "on") {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;
                    }
                }
            }
        }




        public function printCodigosBody()
        {

            $codes = Wa_Theme_Manager::get_opciones('wa_theme_options_codes', 'wa_theme_options_codes_body');


            if (is_array($codes)) {
                foreach ($codes as $code) {
                    switch ($code['mostrar_en']) {
                        case "ros":
                            if (isset($code['enable_code']) && $code['enable_code'] == "on") {

                                echo "<!--" . $code['identificador'] . "-->\n";
                                echo $code['codigo'] . "\n";
                                echo "<!-- Fin " . $code['identificador'] . "-->\n";
                            }

                            break;

                        case "single":
                            if (is_single()) {
                                if (isset($code['enable_code']) && $code['enable_code'] == "on") {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;

                        case "archive":
                            if (is_archive()) {
                                if (isset($code['enable_code']) && $code['enable_code'] == "on") {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;

                        case "page":
                            if (is_page()) {
                                if (isset($code['enable_code']) && $code['enable_code'] == "on") {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;

                        case "search":
                            if (is_search()) {
                                if (isset($code['enable_code']) && $code['enable_code'] == "on") {
                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }
                            break;
                    }
                }
            }
        }
    }

    $codeLoader = new Wa_Theme_Manager_Codigos();
}
