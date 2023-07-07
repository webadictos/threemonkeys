<?php

class WA_Infinite_Scroll_Module extends WA_Module
{
    protected $api;

    public function init()
    {
        require_once dirname(__FILE__) . '/class-infinite-scroll-api.php';

        // $this->load_config();

        $this->loader->add_filter('wa_theme_get_wa_theme_options_page_fields', $this, 'add_settings', 10, 2);
        $this->loader->add_filter('wa_theme_get_wa_meta_article_metabox_fields', $this, 'add_metabox_fields', 10, 2);
        $this->loader->add_action('rest_api_init', $this, 'register_api', 10, 2);
        // $this->loader->add_filter('article_config', $this, 'article_config_filter', 10, 2);
    }

    public function load_config()
    {
        $scroll_options = array(
            'enableScroll' => true,
            'items' => '',
            'criterio' => '',
            'enablePromoted' => '',
            'promotedTTL' => '',
        );
        $_scroll_options = array();

        if (class_exists('Wa_Theme_Manager')) {
            $scroll_options_cmb = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_scroll');


            if ($scroll_options_cmb)
                $_scroll_options = apply_filters('wa_scroll_settings', $scroll_options_cmb[0]) ?? array();
        }

        $this->module_config = array_merge($scroll_options, $_scroll_options);

        $this->module_config['enableScroll'] = filter_var($this->module_config['enableScroll'], FILTER_VALIDATE_BOOLEAN) ?? false;
        $this->module_config['enablePromoted'] = filter_var($this->module_config['enablePromoted'], FILTER_VALIDATE_BOOLEAN) ?? false;
    }

    public function add_metabox_fields($fields, $prefix)
    {

        $_fields = array(
            $prefix . 'opciones_scroll' => array(
                'name' => 'Scroll Infinito',
                'type' => 'title',
                'id'   => $prefix . 'opciones_scroll',
                'show_on_cb' => array($this, 'show_field_if_enable')
            ),
            // $prefix . 'hide_scroll' => array(
            //     'name'    => 'Ocultar del scroll',
            //     'desc'    => 'Marque si desea ocultar esta nota para que no aparezca en el scroll infinito.',
            //     'id'      => $prefix . 'hide_scroll',
            //     'type' => 'checkbox',
            //     'show_on_cb' => array($this, 'show_field_if_enable')

            // ),
            $prefix . 'disable_scroll' => array(
                'name'    => 'Desactivar Scroll',
                'desc'    => 'Marque para desactivar el scroll en esta nota.',
                'id'      => $prefix . 'disable_scroll',
                'type' => 'checkbox',
                'show_on_cb' => array($this, 'show_field_if_enable')

            ),
            $prefix . 'posts_scroll' => array(
                'name'          => 'Notas siguientes en el scroll',
                'id'            => $prefix . 'posts_scroll',
                'type'          => 'post_search_ajax',
                'desc'            => 'Elige notas que deseas que aparezcan enseguida en el scroll. Estas notas aparecerán primero en el scroll, antes que las predeterminadas',
                // Optional :
                'limit'          => 5,         // Limit selection to X items only (default 1)
                'maxitems'      => 5,
                'sortable'          => true,     // Allow selected items to be sortable (default false)
                'query_args'    => array(
                    'post_type'            => array('post'),
                    'post_status'        => array('publish'),
                    'posts_per_page'    => 5,
                    'date_query' => array(
                        'after' => date('Y-m-d', strtotime('-5 years'))
                    )
                ),
                'show_on_cb' => array($this, 'show_field_if_enable')
            )
        );

        $fields = $_fields;


        return $fields;
    }

    // public function article_config_filter($current_config, $post_id)
    // {
    //     if ($this->config('enableScroll')) {
    //         $disable_scroll = get_post_meta($post_id, 'wa_meta_disable_scroll', true) ?? '';
    //         $posts_scroll = get_post_meta($post_id, 'wa_meta_posts_scroll', true) ?? array();

    //         $current_config['enable_scroll'] = ($disable_scroll === "on") ? false : true;
    //         $current_config['next_posts'] = $posts_scroll;
    //     }
    //     return $current_config;
    // }

    public function show_field_if_enable()
    {
        return $this->config('enableScroll');
    }

    public function add_settings($optionsFields, $prefix = "wa_theme_options_")
    {

        $scrollID = $prefix . "scroll";


        $optionsFields["{$scrollID}"] =
            array(
                'id'          => "{$scrollID}",
                'type'        => 'group',
                'description' => '',
                'repeatable'  => false, // use false if you want non-repeatable group
                'options'     => array(
                    'group_title'       => __('Configuración del scroll infinito', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
                    // 'add_button'        => __( 'Add Another Entry', 'cmb2' ),
                    // 'remove_button'     => __( 'Remove Entry', 'cmb2' ),
                    'sortable'          => false,
                    'closed'         => false, // true to have the groups closed by default
                    // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
                ),
                'tab_name' => 'Scroll infinito',
                'tab_icon' => 'dashicons-text-page',
                'wa_group_fields' => apply_filters(
                    "wa_theme_get_{$scrollID}_fields",
                    array(
                        'enableScroll' => array(
                            'name'             => '¿Habilitar scroll infinito en notas?',
                            'desc'             => 'Indica si deseas activar el scroll infinito en las notas del sitio.',
                            'id'               => 'enableScroll',
                            'type'             => 'select',
                            'show_option_none' => false,
                            'default'          => 1,
                            'options'          => array(
                                0 => __('No', 'cmb2'),
                                1   => __('Si', 'cmb2'),
                            ),
                        ),
                        'items' => array(
                            'name' => 'Notas a mostrar en el scroll infinito',
                            'desc' => 'Indica cuantas notas quieres que aparezcan en el scroll infinito',
                            'id'   => 'items',
                            'type' => 'text',
                            'default' => 5,
                            'attributes' => array(
                                'type' => 'number',
                                'pattern' => '\d*',
                            ),
                        ),
                        'criterio' => array(
                            'name'             => 'Criterio para seleccionar notas para el scroll',
                            'desc'             => 'Selecciona el criterio por medio del cual deseas que se elijan las notas del scroll infinito.',
                            'id'               => 'criterio',
                            'type'             => 'select',
                            'show_option_none' => false,
                            'default'          => "ultimas",
                            'options'          => array(
                                "ultimas" => __('Notas recientes', 'cmb2'),
                                "seccion_principal"  => __('Categoría principal de la nota actual', 'cmb2'),
                                "seccion_todas"  => __('Categorías de la nota actual', 'cmb2'),
                                "tags"  => __('Etiquetas de la nota actual', 'cmb2'),
                            ),
                        ),

                        'enablePromoted' => array(
                            'name'             => '¿Habilitar notas promocionadas en el scroll?',
                            'desc'             => 'Indica si deseas activar las notas promocionadas en el scroll infinito.',
                            'id'               => 'enablePromoted',
                            'type'             => 'select',
                            'show_option_none' => false,
                            'default'          => 1,
                            'options'          => array(
                                0 => __('No', 'cmb2'),
                                1   => __('Si', 'cmb2'),
                            ),
                        ),
                        'promotedTTL' => array(
                            'name' => 'TTL de notas promocionadas',
                            'desc' => 'Indica cuanto tiempo en segundos, un mismo usuario no podrá ver la misma nota promocionada en el scroll infinito.',
                            'id'   => 'promotedTTL',
                            'type' => 'text',
                            'default' => 86400,
                            'attributes' => array(
                                'type' => 'number',
                                'pattern' => '\d*',
                            ),
                        ),
                        array(
                            'name'        => 'Notas excluidas',
                            'desc'        => 'Estas notas no aparecerán en el scroll infinito',
                            'id'          => 'excluded_posts',
                            'type'        => 'post_search_text',
                            'post_type'   => array('post'),
                            'select_type' => 'checkbox',
                            'select_behavior' => 'add',
                        )
                    )
                ),

            );

        return $optionsFields;
    }


    public function get_single_infinite_scroll_settings()
    {
        global $wp_query;

        $loadmore = array();

        $criterio = $this->config('criterio') ?? 'ultimas';
        $promoted = WA_Theme()->module('promoted')->get_config() ?? array();
        $excluded_posts = $this->config('excluded_posts') ?? '';

        $excluded_posts = explode(',', $excluded_posts);

        $posts_scroll = get_post_meta(get_the_ID(), 'wa_meta_posts_scroll', true);

        $notIn = array();

        if (is_array($promoted)) {
            $notIn = array_merge($notIn, $promoted);
        }

        if (is_array($excluded_posts)) {
            $notIn = array_merge($notIn, $excluded_posts);
        }

        $notIn[] = get_the_ID();

        $args = array(
            'post_status' => 'publish',
            'post_type' => get_post_type(get_the_ID()),
            'posts_per_page' => $this->config('items') ?? 5,
            'post__not_in'   => $notIn,
            'fields' => 'ids',
            'cache_results'  => false,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'has_password' => false,
        );

        switch ($criterio) {

            case "seccion_principal":
                $category = apply_filters('get_primary_category', null, get_the_ID()); //get_post_primary_category(get_the_ID());
                $pimary_category = $category['primary_category']->term_id ?? null;
                $cats = array($pimary_category);
                $args['category__in'] = $cats;
                break;

            case "seccion_todas":
                $cats_ids = array();
                $cats = get_the_category(get_the_ID());

                if ($cats) {
                    foreach ($cats as $cat) {
                        $cats_ids[] = $cat->term_id;
                    }
                }
                $args['category__in'] = $cats_ids;
                break;

            case "tags":
                $tags_ids = array();
                $tags = get_the_tags(get_the_ID());
                if ($tags) {
                    foreach ($tags as $tag) {
                        $tags_ids[] = $tag->term_id;
                    }
                }

                $args['tag__in'] = $tags_ids;
                break;
            default:

                break;
        }



        $nextPosts = new WP_Query($args);


        $posts = $nextPosts->get_posts();
        $slug = str_replace(get_home_url(), "", get_the_permalink());

        $postLoadMoreIDs = $posts;

        if (is_array($posts_scroll) && count($posts_scroll) > 0) {
            $postLoadMoreIDs = array();
            $postsScrollIDs = array_map(function ($n) {
                return intval($n);
            }, $posts_scroll);


            $postsMerged = array_unique(array_merge($postsScrollIDs, $posts), SORT_REGULAR);

            foreach ($postsMerged as $postMerged) {
                $postLoadMoreIDs[] = $postMerged;
            }
        }





        $loadmore = array(
            'next' => $postLoadMoreIDs,
            'previous' => '',
            'initial' => get_the_ID(),
            'current' => get_the_ID(),
            'current_slug' => $slug,
            'counter' => 1,
            'max_page' => $this->config('items') ?? 5,
            'previous_ids' => array(get_the_ID()),
            'cats' => $cats ?? null,
        );

        return $loadmore;
    }

    public function get_archive_infinite_scroll_settings()
    {
        global $wp_query;


        $loadmore = array(
            'query' => json_encode($wp_query->query_vars), // everything about your loop is here
            'current_page' => get_query_var('paged') ? get_query_var('paged') : 1,
            'max_page' => $wp_query->max_num_pages,
        );

        return $loadmore;
    }

    public function get_front_settings($settings)
    {
        if ($this->config('enableScroll')) {
            if (is_single()) {
                $disable_scroll = get_post_meta(get_the_ID(), 'wa_meta_disable_scroll', true) ?? '';

                if ($disable_scroll !== "on") {
                    return $this->get_single_infinite_scroll_settings();
                }
            }

            if (is_archive() || is_search() || is_home()) {
                return $this->get_archive_infinite_scroll_settings();
            }
        }

        return array();
    }

    public function register_api()
    {
        $this->api = new WA_InfiniteScroll_API();
    }
}
