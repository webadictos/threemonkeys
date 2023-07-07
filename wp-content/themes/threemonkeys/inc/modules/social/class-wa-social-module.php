<?php

/**
 * 
 */

class WA_Social_Module extends WA_Module
{

    public function init()
    {
        // $this->load_config();

        $this->loader->add_filter('wa_theme_get_wa_theme_options_page_fields', $this, 'add_settings_page', 10, 2);
    }




    public function add_settings_page($optionsFields, $prefix = "wa_theme_options_")
    {

        $fieldID = $prefix . "social";

        $optionsFields["{$fieldID}"] =
            array(
                'id'          => "{$fieldID}",
                'type'        => 'group',
                'description' => 'Configuración de redes sociales',
                'repeatable'  => false, // use false if you want non-repeatable group
                'options'     => array(
                    'group_title'       => __('Configuración de redes sociales', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
                    // 'add_button'        => __( 'Add Another Entry', 'cmb2' ),
                    // 'remove_button'     => __( 'Remove Entry', 'cmb2' ),
                    'sortable'          => false,
                    'closed'         => false, // true to have the groups closed by default
                    // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
                ),
                'tab_name' => 'Redes Sociales',
                'tab_icon' => 'dashicons-share',
                'wa_group_fields' => apply_filters("wa_theme_get_{$fieldID}_fields", array(
                    'social_networks' => array(
                        'name'    => "Perfiles sociales",
                        'desc' => 'Agrega cada uno de los links de perfiles sociales',
                        'id'      => 'social_networks',
                        'type'    => 'social',
                        'repeatable' => true,
                        'options' => array(
                            'add_row_text' => __('Agregar red social', 'wa-theme'),
                        ),
                    ),
                    'igtoken' => array(
                        'name' => 'Token de Instagram',
                        'desc' => 'Token de Instagram para poder tener acceso a los últimos posts',
                        'id'   => 'igtoken',
                        'type' => 'text',
                    ),
                    'GoogleAPIKey' => array(
                        'name' => 'YouTube API Key',
                        'desc' => '',
                        'id'   => 'GoogleAPIKey',
                        'type' => 'text',
                    ),
                    'youtube_channel' => array(
                        'name' => 'YouTube Channel ID',
                        'desc' => '',
                        'id'   => 'youtube_channel',
                        'type' => 'text',
                    ),
                    'instagram_category' =>  array(
                        'name'           => 'Instagram Picks',
                        'id'             => 'instagram_category',
                        'taxonomy'       => 'category', // Enter Taxonomy Slug
                        'type'           => 'taxonomy_radio_hierarchical',
                        // Optional :
                        'text'           => array(
                            'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
                        ),
                        'remove_default' => 'false', // Removes the default metabox provided by WP core.
                        // Optionally override the args sent to the WordPress get_terms function.
                        'query_args' => array(
                            // 'orderby' => 'slug',
                            // 'hide_empty' => true,
                        ),
                    )
                )),

            );

        return $optionsFields;
    }

    public function load_config()
    {
        $social_options = array(
            'igtoken' => '',
            'GoogleAPIKey' => '',
            'youtube_channel' => '',
            'social_networks' => array(),
        );
        if (class_exists('Wa_Theme_Manager')) {
            $social_options_cmb = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_social');

            if ($social_options_cmb)
                $social_options = $social_options_cmb[0];
        }

        $this->module_config = $social_options;
    }

    public function show_social_icons($opts = array())
    {
        $default = array(
            'exclude' => array(),
            'css' => '',
        );

        $params = wp_parse_args($opts, $default);


        $social_networks_icons = array(
            'facebook' => '<i class="fab fa-facebook-f"></i>',
            'twitter' => '<i class="fab fa-twitter"></i>',
            'instagram' => '<i class="fab fa-instagram"></i>',
            'youtube' => '<i class="fab fa-youtube"></i>',
            'tiktok' => '<i class="fab fa-tiktok"></i>',
            'linkedin' => '<i class="fab fa-linkedin"></i>',
            'pinterest' => '<i class="fab fa-pinterest"></i>',
            'flipboard' => '<i class="fab fa-flipboard"></i>',
            'email' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
            <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>
          </svg>',
        );

        $social_networks = $this->module_config['social_networks'];


        if (is_array($social_networks) && count($social_networks) > 0) {

            $custom_class = "";

            if (trim($params['css']) !== "") $custom_class = $params['css'];


            echo "<ul class='wa-social-profiles {$custom_class}'>";

            foreach ($social_networks as $social_network) {

                if (in_array($social_network['social'], $params['exclude'])) continue;

                echo "<li>";

                echo '<a class="wa-social-profiles__link" target="_blank" rel="noopener noreferrer" title="' . sprintf(__('Síguenos en %s', 'wa-theme'), ucfirst($social_network['social'])) . '" href="' . $social_network['url'] . '">' . apply_filters("wa_social_{$social_network['social']}_icon", $social_networks_icons[$social_network['social']]) . '</a>';

                echo "</li>";
            }

            echo "</ul>";
        }
    }

    public function sharebar($postID, $opts)
    {


        $default = array(
            'networks' => array('facebook', 'twitter', 'linkedin', 'whatsapp'),
            'css' => '',
        );

        $params = wp_parse_args($opts, $default);

        $networks = $params['networks'];

        $social_networks = array(
            'facebook' => array(
                'link' => 'https://www.facebook.com/sharer.php?u={URL}',
                'icon' => '<i class="fab fa-facebook-f"></i>',
                'share_title' => __('¡Compartir en Facebook!', 'wa-theme'),
            ),
            'twitter' => array(
                'link' => 'https://twitter.com/share?url={URL}&text={TITLE}',
                'icon' => '<i class="fab fa-twitter"></i>',
                'share_title' => __('¡Compartir en Twitter!', 'wa-theme'),
            ),
            'linkedin' => array(
                'link' => 'https://www.linkedin.com/sharing/share-offsite/?url={URL}',
                'icon' => '<i class="fab fa-linkedin-in"></i></a>',
                'share_title' => __('¡Compartir en LinkedIn!', 'wa-theme'),
            ),
            'whatsapp' => array(
                'link' => 'https://api.whatsapp.com/send?text={URL}',
                'icon' => '<i class="fab fa-whatsapp"></i>',
                'share_title' => __('¡Compartir en LinkedIn!', 'wa-theme'),
            )
        );

        $custom_class = "";

        if (trim($params['css']) !== "") $custom_class = $params['css'];

        echo '<ul class="wa-social-share ' . $custom_class . '">';

        foreach ($networks as $network) {

            $current_network = "";

            if (isset($network['link'])) {
                $current_network = $network;
            } else if (isset($social_networks[$network])) {
                $current_network = $social_networks[$network];
            }

            if ($current_network !== "") {
                echo "<li>";
                $link = str_replace("{URL}", urlencode(get_permalink($postID)), $current_network['link']);
                $link = str_replace("{TITLE}", apply_filters("wa_share_{$network}_title", get_the_title($postID)), $link);

                echo '<a href="' . $link . '" target="_blank" class="' . $network . '-social-share__link wa-social-share__link" title="' . $current_network['share_title'] . '">' . apply_filters("wa_share_{$network}_icon", $current_network['icon']) . '</a>';
                echo "</li>";
            }
        }

        echo "</ul>";
    }

    /**
     * Refresca el token de Instagram
     */
    public function get_instagram_token()
    {

        if (!isset($this->module_config['igtoken'])) return "";


        $ig_token = "";
        $first_igtoken = "";

        if ($this->module_config['igtoken'] !== "") {

            if (false === ($first_igtoken = get_transient("wa_ig_last_token"))) {

                $first_igtoken = trim($this->module_config['igtoken']);

                if ($this->module_config['igtoken'] !== "") {
                    set_transient('wa_ig_last_token', $first_igtoken, 10 * YEAR_IN_SECONDS); // 30 Minutos
                }
            }

            if (trim($first_igtoken) !== trim($this->module_config['igtoken'])) {
                set_transient('wa_ig_last_token', $this->module_config['igtoken'], 10 * YEAR_IN_SECONDS); // 30 Minutos
                delete_transient('wa_ig_tokenv3');
            }

            if (false === ($ig_token = get_transient("wa_ig_tokenv3"))) {

                $ig_token = trim($this->module_config['igtoken']);

                wp_safe_remote_get('https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . $ig_token);


                if ($ig_token !== "") {
                    set_transient('wa_ig_tokenv3', $ig_token, MONTH_IN_SECONDS); // 30 Minutos
                }
            }
        }



        return $ig_token;
    }

    public function get_front_settings($settings)
    {

        unset($settings['social_networks']);
        unset($settings['instagram_category']);

        $settings['igtoken'] = $this->get_instagram_token();

        return $settings;
    }
}

/**
 * Funcion para imprimir los links de los perfiles de redes sociales configurados en el THEME MANAGER
 */

function wa_show_social_profiles($opts = array())
{
    $wa_theme = $GLOBALS['WA_Theme'];

    if (is_a($wa_theme, 'WA_Theme')) {
        $modulos = $wa_theme->modules();

        if ($social_module = $modulos->module("social")) {

            $social_module->show_social_icons($opts);
        }
    }
}

/**
 * Imprime la barra de compartir
 */

function wa_show_sharebar($postID, $opts = array())
{
    $wa_theme = $GLOBALS['WA_Theme'];

    if (is_a($wa_theme, 'WA_Theme')) {
        $modulos = $wa_theme->modules();

        if ($social_module = $modulos->module("social")) {

            $social_module->sharebar($postID, $opts);
        }
    }
}
