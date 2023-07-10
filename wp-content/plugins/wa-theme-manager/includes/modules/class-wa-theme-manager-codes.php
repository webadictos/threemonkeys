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

if (!class_exists('Wa_Theme_Manager_Codes')) {

    class Wa_Theme_Manager_Codes
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

        protected $codes = array();

        /**
         * Initialize the plugin.
         */
        function __construct()
        {

            // add_action('wp_head', array($this, 'printCodigosHead'));
            // add_action('wp_footer', array($this, 'printCodigosFooter'));
            // add_action('wp_body_open', array($this, 'printCodigosBody'));
            add_filter('wa_theme_set_options_page', array($this, 'initOptionsPage'), 11, 1);

            $this->codes = $this->get_codes();

            $this->process_insertions_codes();
        }

        public function initOptionsPage($optionsPage)
        {

            $codeOptionsPage = array();

            $codeFields = array(
                $this->prefix . 'codes' => array(
                    'id'          => $this->prefix . 'codes',
                    'type'        => 'group',
                    'title' => 'Custom Codes',
                    'desc'       => 'Agrega los códigos que desees insertar en la posición deseada',
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
                            'desc' => 'Si estás insertando un javascript asegúrate de que tenga las etiquetas &lt;script&gt;&lt;/script&gt;',
                            'id'   => 'codigo',
                            'type' => 'textarea_small',
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
                                'single'   => 'En todos los artículos',
                                'limited_articles'   => 'En algunos artículos',
                                'articulos_excepto'   => 'En todos los artículos excepto',
                                'contenidos_canal'   => 'En artículos de estas categorías',
                                'page' => 'En todas las páginas',
                                'algunas_paginas' => 'En algunas páginas',
                                'paginas_excepto' => 'En algunas páginas excepto',
                                'archive' => 'En todas las categorías',
                                'algunas_categorias' => 'En algunas categorías',
                                'category_limited' => 'En todas las categorías excepto',
                                'search' => 'En los resultados de búsqueda',

                            )
                        ),

                        'post_ids' => array(
                            'name'             => 'Artículos',
                            'id'               => 'posts_ids',
                            'type'        => 'post_search_text',
                            'post_type'   => array('post'),
                            'select_type' => 'checkbox',
                            'select_behavior' => 'add',
                            'desc' => "Escribe las IDS de los posts donde se debe mostrar",
                            'attributes'    => array(
                                'data-conditional-id'     => 'mostrar_en',
                                'data-conditional-value'  => wp_json_encode(array('limited_articles', 'articulos_excepto')),
                            ),
                        ),
                        'page_ids' => array(
                            'name'             => 'Páginas',
                            'id'               => 'page_ids',
                            'type'        => 'post_search_text',
                            'post_type'   => array('page'),
                            'select_type' => 'checkbox',
                            'select_behavior' => 'add',
                            'desc' => "Escribe las IDS de las páginas donde se debe mostrar",
                            'attributes'    => array(
                                'data-conditional-id'     => 'mostrar_en',
                                'data-conditional-value'  => wp_json_encode(array('algunas_paginas', 'paginas_excepto')),
                            ),
                        ),
                        'category_ids' => array(
                            'name'             => 'Categorías',
                            'id'               => 'category_ids',
                            'type'        => 'taxonomy_multicheck_hierarchical',
                            'taxonomy'   => 'category',
                            'desc' => "Selecciona las categorías donde debe mostrarse",
                            'attributes'    => array(
                                'data-conditional-id'     => 'mostrar_en',
                                'data-conditional-value'  => wp_json_encode(array('category_limited', 'contenidos_canal')),
                            ),
                        ),
                        'posicion' => array(
                            'name'             => 'Posición',
                            'desc'             => 'Selecciona el lugar donde se insertará el código.',
                            'id'               => 'posicion',
                            'type'             => 'select',
                            'show_option_none' => false,
                            // 'default'          => 'wp_head',
                            'options'          => apply_filters('wa_get_codes_positions', array(
                                'wp_head' => __('Antes del &lt/head&gt;', 'cmb2'),
                                'wp_body_open' => __('Al inicio del body', 'cmb2'),
                                'wp_footer'   => __('En el footer', 'cmb2'),
                                'wa_the_content'   => __('Dentro del texto', 'cmb2'),
                            )),
                        ),
                        'parrafo' => array(
                            'name' => 'Después de qué párrafo',
                            'desc' => 'Indica después de qué párrafo debe ser insertado el código.',
                            'id'   => 'parrafo',
                            'type' => 'text',
                            'default' => 3,
                            'attributes' => array(
                                'type' => 'number',
                                'pattern' => '\d*',
                                'data-conditional-id'     => 'posicion',
                                'data-conditional-value'  => wp_json_encode(array('wa_the_content')),
                            ),
                        ),
                        'enable_code' => array(
                            'name' => 'Habilitar',
                            'id'   => 'enable_code',
                            'type' => 'checkbox',
                            'desc' => "Marque si desea activar este código",
                        ),



                    )),

                ),

            );


            $codeOptionsPage = array(
                'id'           => $this->prefix . 'custom_codes',
                'title'        => esc_html__('Custom Codes', 'cmb2'),
                'object_types' => array('options-page'),
                'option_key'   => $this->prefix . 'custom_codes',
                'parent_slug'  => 'wa_theme_options',
                'capability'      => 'manage_options',
                'vertical_tabs' => false,
                'has_tabs' => false,
                'wa_fields' => apply_filters('wa_theme_get_wa_theme_options_custom_codes_page_fields', $codeFields),
            );

            $optionsPage[$this->prefix . 'custom_codes_page'] = $codeOptionsPage;

            return $optionsPage;
        }

        public function get_codes()
        {
            $codes = Wa_Theme_Manager::get_opciones('wa_theme_options_custom_codes', 'wa_theme_options_codes');

            return $codes;
        }

        public function process_insertions_codes()
        {

            if (!is_admin() && !is_feed()) {

                if (!is_array($this->codes)) return;

                foreach ($this->codes as $code) {

                    if (isset($code['enable_code']) && $code['enable_code'] == "on") {


                        if ($code['posicion'] === "wa_the_content") {

                            add_filter('the_content', function ($content) use ($code) {

                                if (is_singular() && $this->can_be_inserted($code)) {


                                    $codigo_personalizado = "<!--" . $code['identificador'] . "-->\n";
                                    $codigo_personalizado .= $code['codigo'] . "\n";
                                    $codigo_personalizado .= "<!-- Fin " . $code['identificador'] . "-->\n";

                                    $parrafo = intval($code['parrafo']);

                                    $content = WA_Theme_Manager::insertAdAfterParagraph($codigo_personalizado, $parrafo, $content);
                                }

                                return $content;
                            }, 10);
                        } else {
                            add_action($code['posicion'], function () use ($code) {

                                if ($this->can_be_inserted($code)) {

                                    echo "<!--" . $code['identificador'] . "-->\n";
                                    echo $code['codigo'] . "\n";
                                    echo "<!-- Fin " . $code['identificador'] . "-->\n";
                                }
                            }, 10);
                        }
                    }
                }
            }
        }
        function insertAdAfterParagraph($insertion, $paragraph_id, $content)
        {
            $closing_p = '</p>';
            $paragraphs = explode($closing_p, $content);
            foreach ($paragraphs as $index => $paragraph) {
                // Only add closing tag to non-empty paragraphs
                if (trim($paragraph)) {
                    // Adding closing markup now, rather than at implode, means insertion
                    // is outside of the paragraph markup, and not just inside of it.
                    $paragraphs[$index] .= $closing_p;
                }

                // + 1 allows for considering the first paragraph as #1, not #0.
                if ($paragraph_id == $index + 1) {
                    $paragraphs[$index] .=  $insertion;
                }
            }
            return implode('', $paragraphs);
        }




        private function can_be_inserted($code)
        {


            $show_in = $code['mostrar_en'] ?? "";

            /*
                               'ros'   => 'Todo el sitio',
                                'single'   => 'En todos los artículos',
                                'limited_articles'   => 'En algunos artículos',
                                'articulos_excepto'   => 'En todos los artículos excepto',
                                'page' => 'En todas las páginas',
                                'algunas_paginas' => 'En algunas páginas',
                                'paginas_excepto' => 'En algunas páginas excepto',
                                'archive' => 'En todas las categorías',
                                'category_limited' => 'En todas las categorías excepto',
                                'search' => 'En los resultados de búsqueda',
            */

            $ids_to_show = explode(",", $code['posts_ids'] ?? '');
            $category_ids = $code['category_ids'] ?? array();


            switch ($show_in) {

                case "ros":
                    return true;
                    break;

                case "single":
                    // var_dump(is_singular());
                    return is_single();
                    break;
                case "limited_articles":
                    if (is_array($ids_to_show) && count($ids_to_show) > 0) {
                        if (is_single($ids_to_show)) return true;
                    }
                    break;
                case "articulos_excepto":
                    if (is_array($ids_to_show) && count($ids_to_show) > 0) {
                        if (is_single() && !is_single($ids_to_show)) return true;
                    } else {
                        return is_single();
                    }
                    break;

                case "contenidos_canal":
                    if (is_single() && has_category($category_ids)) return true;
                    break;

                case "page":

                    return is_page();
                    break;

                case "algunas_paginas":
                    if (is_array($ids_to_show) && count($ids_to_show) > 0) {
                        if (is_page($ids_to_show)) return true;
                    }
                    break;

                case "paginas_excepto":
                    if (is_array($ids_to_show) && count($ids_to_show) > 0) {
                        if (is_page() && !is_page($ids_to_show)) return true;
                    } else {
                        return is_page();
                    }
                    break;
                case "archive":
                    return is_category();
                    break;

                case "algunas_categorias":
                    if (is_array($category_ids) && count($category_ids) > 0) {
                        return is_category($category_ids);
                    }
                    break;

                case "category_limited":
                    if (is_array($category_ids) && count($category_ids) > 0) {
                        if (is_category() && !is_category($category_ids)) return true;
                    } else {
                        return is_category();
                    }
                    break;

                case "search":
                    return is_search();
                    break;
            }



            return false;
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

    $codeLoader = new Wa_Theme_Manager_Codes();
}
