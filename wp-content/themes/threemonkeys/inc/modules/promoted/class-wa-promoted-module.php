<?php

class WA_Promoted_Module extends WA_Module
{
    public function init()
    {

        if (!wa_theme()->modules()->is_active('infinite-scroll')) return;
        if (!wa_theme()->module('infinite-scroll')->config('enableScroll')) return;
        if (!wa_theme()->module('infinite-scroll')->config('enablePromoted')) return;

        $this->loader->add_filter('wa_theme_set_options_page', $this, 'add_settings', 10, 2);

        // print_r(wa_theme()->modules()->is_active('infinite-scroll'));
        //    $this->loader->add_filter('wa_theme_get_wa_meta_article_metabox_fields', $this, 'add_metabox_fields', 10, 2);
    }

    public function load_config()
    {
        $promoted_options = array();
        if (class_exists('Wa_Theme_Manager')) {
            $promoted_options_cmb = Wa_Theme_Manager::get_opciones('wa_theme_options_promote', 'wa_theme_options_posts_promoted');

            $promoted_options = apply_filters('wa_promote_settings', $promoted_options_cmb);
        }

        $this->module_config = $promoted_options;
    }

    public function add_settings($optionsPage, $prefix = "wa_theme_options_")
    {
        $codeOptionsPage = array(
            'id'           => $prefix . 'promote_page',
            'title'        => esc_html__('Notas patrocinadas', 'cmb2'),
            'object_types' => array('options-page'),
            'option_key'   => $prefix . 'promote',
            'parent_slug'  => 'wa_theme_options',
            'capability'      => 'manage_options',
            'has_tabs' => false,
            'wa_fields' => apply_filters('wa_theme_get_wa_theme_options_promote_page_fields', array(
                $prefix . 'posts_promoted' => array(
                    'name'          => __('Notas patrocinadas', 'cmb2'),
                    'id'            => $prefix . 'posts_promoted',
                    'type'          => 'post_search_ajax',
                    'desc'            => 'Comienza escribiendo el título de la nota.<br>Las notas patrocinadas aparecerán enseguida en el scroll infinito, ayudando a conseguir más pageviews.',
                    // Optional :
                    'limit'          => 5,         // Limit selection to X items only (default 1)
                    'maxitems'      => 5,
                    'sortable'          => true,     // Allow selected items to be sortable (default false)
                    'query_args'    => array(
                        'post_type'            => array('post'),
                        'post_status'        => array('publish'),
                        'posts_per_page'    => 5,
                        'date_query' => array(
                            'after' => date('Y-m-d', strtotime('-2 years'))
                        )
                    )
                )
            )),
        );

        $optionsPage[$prefix . 'promote_page'] = $codeOptionsPage;


        return $optionsPage;
    }

    public function get_front_settings($settings)
    {

        $currentPost = get_the_ID();

        if ($currentPost) {
            $settings = array_diff($settings, [$currentPost]);
            $settings = array_values($settings);
        }

        return $settings;
    }
}
