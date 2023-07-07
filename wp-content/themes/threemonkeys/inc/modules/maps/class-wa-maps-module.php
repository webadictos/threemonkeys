<?php

class WA_Maps_Module extends WA_Module
{
    public function init()
    {

        // $this->load_config();

        $this->loader->add_filter('wa_theme_get_wa_theme_options_page_fields', $this, 'add_settings', 10, 2);
        $this->loader->add_action('wa_show_badges', $this, 'add_map_badge');

        $this->loader->add_filter('searchwp_live_search_query_args', $this, 'modify_map_search', 10, 1);
    }

    public function load_config()
    {
        $maps_options = array(
            'api' => true,
            'zoom' => '',
            'marker' => '',
            'markerActive' => '',
            'mylocation' => '',
        );
        $_maps_options = array();

        if (class_exists('Wa_Theme_Manager')) {
            $maps_options_cmb = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_map');


            if ($maps_options_cmb)
                $_maps_options = apply_filters('wa_map_settings', $maps_options_cmb[0]) ?? array();
        }

        $this->module_config = array_merge($maps_options, $_maps_options);
    }


    public function show_field_if_enable()
    {
        return $this->config('enableScroll');
    }

    public function add_settings($optionsFields, $prefix = "wa_theme_options_")
    {

        $scrollID = $prefix . "map";


        $optionsFields["{$scrollID}"] =
            array(
                'id'          => "{$scrollID}",
                'type'        => 'group',
                'description' => '',
                'repeatable'  => false, // use false if you want non-repeatable group
                'options'     => array(
                    'group_title'       => __('Configuración del mapa', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
                    'sortable'          => false,
                    'closed'         => false, // true to have the groups closed by default
                ),
                'tab_name' => 'Mapas',
                'tab_icon' => 'dashicons-location-alt',
                'wa_group_fields' => apply_filters(
                    "wa_theme_get_{$scrollID}_fields",
                    array(
                        'api' => array(
                            'name' => 'Api Key Google',
                            'desc' => 'Introduce el API de Google.',
                            'id'   => 'api',
                            'type' => 'text',
                        ),
                        'zoom' =>  array(
                            'name' => 'Zoom Inicial',
                            'desc' => 'Introduce el zoom inicial del mapa.',
                            'id'   => 'zoom',
                            'type' => 'text',
                            'attributes' => array(
                                'type' => 'number',
                                'pattern' => '\d*',
                            ),
                        ),
                        'map_center' => array(
                            'name' => 'Centro del mapa',
                            'desc' => 'Arrastra el marcador a la ubicación',
                            'id' => 'map_center',
                            'type' => 'pw_map',
                            'split_values' => true, // Save latitude and longitude as two separate fields
                        ),

                        'marker' => array(
                            'name'    => 'Imagen del marcador',
                            'desc'    => 'Seleccione la imagen de los marcadores.',
                            'id'          => 'marker',
                            'type'    => 'file',
                            // Optional:
                            'options' => array(
                                'url' => true, // Hide the text input for the url
                            ),
                            'text'    => array(
                                'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
                            ),
                            // query_args are passed to wp.media's library query.
                            'query_args' => array(
                                //'type' => 'application/pdf', // Make library only display PDFs.
                                // Or only allow gif, jpg, or png images
                                'type' => array(
                                    'image/gif',
                                    'image/jpeg',
                                    'image/png',
                                    'image/svg+xml',
                                ),
                            ),
                            'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
                        ),
                        'markerActive' => array(
                            'name'    => 'Imagen del marcador (Activo)',
                            'desc'    => 'Seleccione la imagen de los marcadores.',
                            'id'          => 'markerActive',
                            'type'    => 'file',
                            // Optional:
                            'options' => array(
                                'url' => true, // Hide the text input for the url
                            ),
                            'text'    => array(
                                'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
                            ),
                            // query_args are passed to wp.media's library query.
                            'query_args' => array(
                                //'type' => 'application/pdf', // Make library only display PDFs.
                                // Or only allow gif, jpg, or png images
                                'type' => array(
                                    'image/gif',
                                    'image/jpeg',
                                    'image/png',
                                    'image/svg+xml',
                                ),
                            ),
                            'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
                        ),
                        'mylocation' => array(
                            'name'    => 'Imagen para Mi ubicación',
                            'desc'    => 'Seleccione la imagen para identificar la ubicación del usuario.',
                            'id'          => 'mylocation',
                            'type'    => 'file',
                            // Optional:
                            'options' => array(
                                'url' => true, // Hide the text input for the url
                            ),
                            'text'    => array(
                                'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
                            ),
                            // query_args are passed to wp.media's library query.
                            'query_args' => array(
                                //'type' => 'application/pdf', // Make library only display PDFs.
                                // Or only allow gif, jpg, or png images
                                'type' => array(
                                    'image/gif',
                                    'image/jpeg',
                                    'image/png',
                                    'image/svg+xml',
                                ),
                            ),
                            'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
                        ),
                        'map_category' =>  array(
                            'name'           => 'Categoría de mapas',
                            'id'             => 'map_category',
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

                    )
                ),

            );

        return $optionsFields;
    }

    public function add_map_badge()
    {

        $map_category = $this->config('map_category') ?? 0;

        if ($map_category && has_category($map_category)) {
            $cat = get_category_by_slug($map_category);
            $link = get_category_link($cat);
            // $marker = get_template_directory_uri() . '/assets/images/marker-active.png';
            echo "<a href=\"{$link}\" class=\"map-badge\"></a>";
        }
    }

    public function modify_map_search($args)
    {
        $cat = sanitize_text_field($_REQUEST['category_name']) ?? '';

        if (!empty($cat)) {
            $args['category_name'] = $cat;
        }

        return $args;
    }
}
