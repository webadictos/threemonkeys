<?php

class WA_Ads_Module extends WA_Module
{

    protected $slots_key;
    protected $slots_group;
    protected $ad_types_key;
    protected $settings_key;
    protected $theme_options_key;
    protected $size_mappings;
    public $prefix = 'wa_theme_options_';
    protected $ads_insertions;
    protected $ads_slots;


    public function init()
    {

        require_once get_template_directory() . '/inc/modules/ads/class-wa-ads-insertions.php';
        require_once get_template_directory() . '/inc/modules/ads/class-wa-ad-slot.php';


        $this->theme_options_key = "wa_theme_options";
        $this->slots_group = $this->prefix . 'slots_group';
        $this->ad_types_key = $this->prefix . 'ad_types';
        $this->slots_key = $this->prefix . 'slots';
        $this->settings_key = $this->prefix . "ads";

        $this->loader->add_filter('wa_theme_get_wa_theme_options_page_fields', $this, 'add_settings', 10, 1);
    }

    public function load_config()
    {
        $ads_options = array(
            'enabled' => true,
            'network' => '',
            'prefix' => '',
            'loadOnScroll' => '',
            'refreshAllAdUnits' => '',
            'timeToRefreshAllAdUnits' => '',
            'refreshAllAdUnitsLimit' => '',
            'refreshAds' => '',
            'refresh_time' => '',
            'enableInRead' => '',
            'inReadParagraph' => '',
            'enableMultipleInRead' => '',
            'inReadLimit' => '',
        );
        $_ads_options = array();
        if (class_exists('Wa_Theme_Manager')) {
            $ads_options_cmb = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_ads');

            if ($ads_options_cmb)
                $_ads_options = apply_filters('wa_ads_settings', $ads_options_cmb[0]) ?? array();  //$ads_options_cmb[0];
        }

        $this->module_config = array_merge($ads_options, $_ads_options);

        $this->size_mappings = $this->size_mappings();
        $this->ads_slots = $this->ad_slots();
    }

    public function after_load_config()
    {
        if ($this->config('enabled')) {
            $this->loader->add_filter('wa_theme_set_options_page', $this, 'add_slots_page', 11, 1);
            $this->loader->add_filter('wa_theme_set_metaboxes', $this, 'add_metaboxes', 10, 2);
            $this->loader->add_filter("wa_ads_front_settings", $this, 'add_size_mappings_to_front', 10, 1);
            $this->loader->add_filter('wa_article_config', $this, 'article_config_filter', 10, 2);

            $this->ads_insertions = new WA_Ads_Insertions($this);
        }
    }


    public function add_settings($optionsFields)
    {

        $optionsFields[$this->settings_key] =
            array(
                'id'          => $this->settings_key,
                'type'        => 'group',
                'description' => '',
                'repeatable'  => false, // use false if you want non-repeatable group
                'options'     => array(
                    'group_title'       => __('Configuración de anuncios en el sitio', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
                    // 'add_button'        => __( 'Add Another Entry', 'cmb2' ),
                    // 'remove_button'     => __( 'Remove Entry', 'cmb2' ),
                    'sortable'          => false,
                    'closed'         => false, // true to have the groups closed by default
                    // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
                ),
                'tab_name' => 'Ads',
                'tab_icon' => 'dashicons-editor-table',
                'wa_group_fields' => apply_filters("wa_theme_get_{$this->settings_key}_fields", array(
                    'enabled' => array(
                        'name'             => 'Habilitar anuncios',
                        'desc'             => 'Habilitar la funcionalidad de anuncios en el sitio.',
                        'id'               => 'enabled',
                        'type'             => 'select',
                        'show_option_none' => false,
                        'default'          => 1,
                        'options'          => array(
                            0 => __('No', 'cmb2'),
                            1   => __('Si', 'cmb2'),
                        ),
                    ),
                    'network' => array(
                        'name' => 'Google Ad Manager Network',
                        'desc' => 'Introduce el ID del network de Google Ad Manager.',
                        'id'   => 'network',
                        'type' => 'text',
                        'attributes' => array(
                            'type' => 'number',
                            'pattern' => '\d*',
                        ),
                    ),
                    'prefix' => array(
                        'name' => 'Prefijo del bloque superior',
                        'desc' => 'Si los bloques de anuncio del sitio tienen un bloque de nivel superior, escríbe el código',
                        'id'   => 'prefix',
                        'type' => 'text',
                    ),
                    'loadOnScroll' => array(
                        'name'             => '¿Desplegar banners al interactuar?',
                        'desc'             => 'Al habilitarlo, los banners se cargarán cuando el usuario interactue con el sitio.',
                        'id'               => 'loadOnScroll',
                        'type'             => 'select',
                        'show_option_none' => false,
                        'default'          => 0,
                        'options'          => array(
                            0 => __('No', 'cmb2'),
                            1   => __('Si', 'cmb2'),
                        ),
                    ),
                    'refreshAllAdUnits' => array(
                        'name'             => '¿Refrescar bloques cada determinado tiempo?',
                        'desc'             => 'Indica si deseas que todos los bloques de anuncio se refresquen cada determinados segundos. Al habilitar esto todos los bloques del sitio harán un refresh.',
                        'id'               => 'refreshAllAdUnits',
                        'type'             => 'select',
                        'show_option_none' => false,
                        'default'          => 0,
                        'options'          => array(
                            0 => __('No', 'cmb2'),
                            1   => __('Si', 'cmb2'),
                        ),
                    ),
                    'timeToRefreshAllAdUnits' => array(
                        'name' => 'Tiempo en segundos para refrescar los adunits',
                        'desc' => 'Indica cuanto tiempo en segundos se refrescarán todos los bloques.',
                        'id'   => 'timeToRefreshAllAdUnits',
                        'type' => 'text',
                        'default' => 60,
                        'attributes' => array(
                            'type' => 'number',
                            'pattern' => '\d*',
                        ),
                    ),
                    'refreshAllAdUnitsLimit' => array(
                        'name' => 'Límite de recargas',
                        'desc' => 'Indica hasta cuantas veces se refrescarán todos los bloques. 0 significa que no tiene límite.',
                        'id'   => 'refreshAllAdUnitsLimit',
                        'type' => 'text',
                        'default' => 0,
                        'attributes' => array(
                            'type' => 'number',
                            'pattern' => '\d*',
                        ),
                    ),
                    'refreshAds' => array(
                        'name'             => '¿Refrescar bloques según viewability?',
                        'desc'             => 'Indica si deseas que los bloques visibles se refresquen cada determinado tiempo. Al habilitar esto todos los bloques visibles harán un refresh.',
                        'id'               => 'refreshAds',
                        'type'             => 'select',
                        'show_option_none' => false,
                        'default'          => 1,
                        'options'          => array(
                            0 => __('No', 'cmb2'),
                            1   => __('Si', 'cmb2'),
                        ),
                    ),
                    'refresh_time' => array(
                        'name' => 'Tiempo en segundos para refrescar los adunits',
                        'desc' => 'Indica cuanto tiempo en segundos se refrescarán todos los bloques visibles.',
                        'id'   => 'refresh_time',
                        'type' => 'text',
                        'default' => 30,
                        'attributes' => array(
                            'type' => 'number',
                            'pattern' => '\d*',
                        ),
                    ),
                    'enableInRead' => array(
                        'name'             => '¿Habilitar banner inRead?',
                        'desc'             => 'Al habilitarlo, se insertará un banner después del párrafo que se especifique de cada nota.',
                        'id'               => 'enableInRead',
                        'type'             => 'select',
                        'show_option_none' => false,
                        'default'          => 0,
                        'options'          => array(
                            0 => __('No', 'cmb2'),
                            1   => __('Si', 'cmb2'),
                        ),
                    ),

                    'inread_slot' => array(
                        'name'    => 'Bloque que mostrará el banner inread',
                        'desc'    => 'Selecciona el bloque que se mostrará en el espacio inread',
                        'id'      => 'inread_slot',
                        'type'    => 'select',
                        'options_cb' => array($this, 'get_ad_slots_options'),
                        // 'options' => array(
                        //     'ros-t-a' => 'Super Banner A',
                        //     'ros-t-b' => 'Super Banner B',
                        //     'ros-footer' => 'Footer',
                        //     'ros-inread' => 'Primer inRead',
                        //     'ros-b-notas' => 'inRead secundarios (cada 5 párrafos)',
                        //     'ros-i' => 'Interstitial',
                        //     'ros-b-a' => 'Box Banner A',
                        //     'ros-b-b' => 'Box Banner B',
                        // ),
                        // 'attributes'    => array(
                        //     'data-conditional-id'     => 'enableInRead',
                        //     'data-conditional-value'  => '1',
                        // ),
                    ),

                    'inReadParagraph' => array(
                        'name' => 'Insertar inRead después del párrafo #',
                        'desc' => 'Indica después de que párrafo del texto se insertará el banner inread',
                        'id'   => 'inReadParagraph',
                        'type' => 'text',
                        'default' => 3,
                        'attributes' => array(
                            'type' => 'number',
                            'pattern' => '\d*',
                        ),
                    ),
                    'enableMultipleInRead' => array(
                        'name'             => '¿Habilitar múltiples banners inRead?',
                        'desc'             => 'Al habilitarlo, se insertarán banners dentro del texto 5 párrafos después del primer inRead, esto dependiendo de la longitud del artículo.',
                        'id'               => 'enableMultipleInRead',
                        'type'             => 'select',
                        'show_option_none' => false,
                        'default'          => 0,
                        'options'          => array(
                            0 => __('No', 'cmb2'),
                            1   => __('Si', 'cmb2'),
                        ),
                    ),
                    'inReadLimit' => array(
                        'name' => 'Máximo de banners inRead',
                        'desc' => 'Indica el número máximo de banners inRead se podrán insertar en un texto',
                        'id'   => 'inReadLimit',
                        'type' => 'text',
                        'default' => 3,
                        'attributes' => array(
                            'type' => 'number',
                            'pattern' => '\d*',
                        ),
                    ),
                    'multiple_inread_slot' => array(
                        'name'    => 'Bloque que mostrará los siguientes bloques inread',
                        'desc'    => 'Selecciona el bloque que se mostrará en el espacio inread',
                        'id'      => 'multiple_inread_slot',
                        'type'    => 'select',
                        'options_cb' => array($this, 'get_ad_slots_options'),
                        // 'options' => array(
                        //     'ros-t-a' => 'Super Banner A',
                        //     'ros-t-b' => 'Super Banner B',
                        //     'ros-footer' => 'Footer',
                        //     'ros-inread' => 'Primer inRead',
                        //     'ros-b-notas' => 'inRead secundarios (cada 5 párrafos)',
                        //     'ros-i' => 'Interstitial',
                        //     'ros-b-a' => 'Box Banner A',
                        //     'ros-b-b' => 'Box Banner B',
                        // ),
                        // 'attributes'    => array(
                        //     'data-conditional-id'     => 'enableMultipleInRead',
                        //     'data-conditional-value'  => '1',
                        // ),
                    ),
                )),

            );

        return $optionsFields;
    }

    public function add_slots_page($optionsPage)
    {

        $codeOptionsPage = array();

        $slots_key = $this->slots_key;
        $slots_group = $this->slots_group;
        $ad_types_key = $this->ad_types_key;

        $codeFields = array(
            $slots_group => array(
                'id'          => $slots_group,
                'type'        => 'group',
                'title' => 'Bloques de anuncio',
                'desc'       => 'Define los bloques de anuncio que se utilizarán en el sitio',
                'repeatable'  => true,
                'options'     => array(
                    'group_title'   => 'Bloque {#}',
                    'add_button'    => 'Agregar bloque',
                    'remove_button' => 'Quitar bloque',
                    'closed'        => true,
                    'sortable'      => true,
                ),
                'tab_icon' => 'dashicons-embed-generic',
                'tab_name' => 'Bloques de anuncio',
                'wa_group_fields' => apply_filters('wa_theme_get_' . $slots_group . '_fields', array(
                    'name' => array(
                        'name' => 'Nombre legible',
                        'desc' => 'Escribe un nombre legible para el bloque',
                        'id'   => 'name',
                        'type' => 'text',
                        'required' => true,
                        'data-validation' => 'required',
                        'attributes'  => array(
                            'required'    => 'required',
                        ),

                    ),
                    'id' => array(
                        'name' => 'Identificador',
                        'desc' => 'Escribe un identificador para el bloque',
                        'id'   => 'id',
                        'type' => 'text',
                        'sanitization_cb' => array('WA_Theme_Manager', 'convert_name_as_id'),
                        'data-validation' => 'required',
                        'attributes'  => array(
                            'required'    => 'required',
                        ),

                    ),
                    'code' => array(
                        'name' => 'Código',
                        'desc' => 'Código del bloque en admanager (Sin el bloque superior, en caso de pertenecer a un bloque de nivel superior)',
                        'id'   => 'code',
                        'type' => 'text',
                        'data-validation' => 'required',
                        'attributes' => array('required' => 'required', 'placeholder' => "/" . $this->module_config['network'] . "/" . $this->module_config['prefix'] . "/"),
                    ),
                    'refresh' => array(
                        'name'             => '¿Puede refrescarse?',
                        'id'               => 'refresh',
                        'type'             => 'select',
                        'desc' => "Indica si el bloque permite refrescarse cada determinado tiempo (En caso de que la configuración global tenga activado el refresh).",
                        'show_option_none' => false,
                        'default' => '1',
                        'options'          => array(
                            '1'   => 'Sí',
                            '0'   => 'No',
                        )
                    ),
                    'ad_type' => array(
                        'name'             => 'Size Mapping',
                        'id'               => 'size_mapping',
                        'type'             => 'select',
                        'desc' => "Selecciona el size mapping que aplicará al slot. Asegúrate que los tamaños del size mapping estén asignados en Ad Manager.",
                        'show_option_none' => false,
                        'default' => 'boxbanner',
                        'options_cb' => array($this, 'get_size_mappings_options'),
                        // 'options'          => array(
                        //     'boxbanner'   => 'Box Banner',
                        //     'superbanner'   => 'Super Banner',
                        //     'billboard'   => 'Billboard',
                        //     'interstitial'   => 'Interstitial',
                        //     'custom'   => 'Custom',

                        // )
                    ),
                    'custom_params' => array(
                        'name' => 'Parámetros personalizados',
                        'desc' => 'Solo llenar si estas seguro de lo que estás haciendo',
                        'id'   => 'custom_params',
                        'type' => 'parameters',
                        'repeatable' => true,
                        'options' => array(
                            'add_row_text' => __('Agregar parámetro', 'wa-theme'),
                        ),
                    ),
                )),

            ),


            $ad_types_key  => array(
                'id'          =>  $ad_types_key,
                'type'        => 'group',
                'title' => 'Tipos de anuncio personalizados;',
                'desc'       => 'Indica las medidas para los nuevos size mappings',
                'repeatable'  => true,
                'options'     => array(
                    'group_title'   => 'Size Mapping {#}',
                    'add_button'    => 'Agregar Size Mapping',
                    'remove_button' => 'Quitar Size Mapping',
                    'closed'        => true,  // Repeater fields closed by default - neat & compact.
                    'sortable'      => true,  // Allow changing the order of repeated groups.
                ),
                'tab_icon' => 'dashicons-align-full-width',
                'tab_name' => 'Size Mappings',
                'wa_group_fields' => apply_filters('wa_theme_get_' .  $ad_types_key  . '_fields', array(
                    'identificador' => array(
                        'name' => 'Identificador',
                        'desc' => 'Escribe un identificador para el código',
                        'id'   => 'identificador',
                        'type' => 'text',
                        'attributes'  => array(
                            'required'    => 'required',
                        ),
                    ),
                    'descripcion' =>  array(
                        'name' => 'Descripción',
                        'desc' => '',
                        'id'   => 'descripcion',
                        'type' => 'textarea_small',
                        //'sanitization_cb' => array('Wa_Theme_Manager', 'accept_html_values_sanitize'),
                    ),
                    'sizes' => array(
                        'name'    => "Size mappings",
                        'desc' => 'Agrega el size mapping para cada plataforma',
                        'id'      => 'sizes',
                        'type'    => 'ad_slot',
                        'repeatable' => true,
                        'options' => array(
                            'add_row_text' => __('Agregar size mapping', 'wa-theme'),
                        ),
                    ),

                )),

            ),
        );


        $codeOptionsPage = array(
            'id'           => $this->prefix . 'slots_page',
            'title'        => esc_html__('Ad Slots', 'cmb2'),
            'object_types' => array('options-page'),
            'option_key'   => $this->slots_key,
            'parent_slug'  => $this->theme_options_key,
            'capability'      => 'manage_options',
            'vertical_tabs' => true,
            'has_tabs' => true,
            'wa_fields' => apply_filters('wa_theme_get_wa_theme_options_slots_page_fields', $codeFields),
        );

        $optionsPage[$this->prefix . 'slots_page'] = $codeOptionsPage;

        return $optionsPage;
    }

    public function size_mappings()
    {

        $size_mappings = array();
        $_size_mappings = array();


        if (class_exists('Wa_Theme_Manager')) {
            $size_mappings_opts = Wa_Theme_Manager::get_opciones($this->slots_key, $this->ad_types_key);
            $size_mappings = apply_filters('wa_ads_size_mappings', $size_mappings_opts); //$ads_options_cmb[0]
        }


        if (is_array($size_mappings)) {
            foreach ($size_mappings as $mapping) {
                $size_id = WA_Utils::convert_name_as_id($mapping['identificador']);
                $_size_mappings[$size_id] = $mapping;
            }
        }

        return $_size_mappings;
    }

    public function process_size_mappings()
    {
        $_sizes = array();


        // Generate an array of width and height
        $width_height_array = [];
        foreach ($this->size_mappings as $k => $item) {
            foreach ($item['sizes'] as $size) {
                $width_height_array[$k][] = [$size['width'], $size['height']];
            }

            $width_height_array[$k] = array_unique($width_height_array[$k], SORT_REGULAR);
        }


        // Generate an array of sizes by platform
        $sizes_by_platform_array = [];
        // Loop through the original array
        foreach ($this->size_mappings as $key => $value) {
            // Check if the 'sizes' key exists
            if (isset($value['sizes'])) {

                $value['sizes'] = array_unique($value['sizes'], SORT_REGULAR);
                // Loop through the 'sizes' array
                foreach ($value['sizes'] as $size) {
                    // Check if the 'platform' key exists
                    if (isset($size['platform'])) {
                        // Group the size by 'identificador' and 'platform'
                        $sizes_by_platform_array[$key][$size['platform']][] = [$size['width'], $size['height']];
                    }
                }
            }
        }

        $_sizes['sizes'] = $width_height_array;
        $_sizes['mappings'] = $sizes_by_platform_array;

        return $_sizes;
    }

    public function add_size_mappings_to_front($settings)
    {

        $size_mappings = $this->process_size_mappings();

        $settings['mappings'] = $size_mappings;

        return $settings;
    }

    public function get_size_mappings_options($field)
    {

        $sizes = array(
            'boxbanner' => 'Box Banner',
            'halfpage' => 'Half Page',
            'superbanner' => 'Super Banner',
            'Billboard' => 'Billboard',
            'inread' => 'In Read',
            'inread_multiple' => 'In Read secundarios',
            'interstitial' => 'Interstitial'

        );

        $size_mappings = $this->size_mappings;

        $_size_mappings = array();

        if (is_array($size_mappings)) {
            foreach ($size_mappings as $mapping_id => $mapping) {
                // $size_id = wa_theme()->helper('utils')->convert_name_as_id($mapping['identificador']);
                $_size_mappings[$mapping_id] = $mapping['identificador'];
            }
        }

        $sizes = array_merge($sizes, $_size_mappings);

        return $sizes;
    }

    public function ad_slots()
    {
        $_ads_slots = array();
        $ads_slots = array();

        if (class_exists('Wa_Theme_Manager')) {
            $ads_slots_opts = Wa_Theme_Manager::get_opciones($this->slots_key, $this->slots_group);
            $ads_slots = apply_filters('wa_ads_slots', $ads_slots_opts); //$ads_options_cmb[0]
        }

        if (is_array($ads_slots)) {
            foreach ($ads_slots as $slot) {
                if (isset($slot['id'])) {
                    //$slot_id = WA_Utils::convert_name_as_id($slot['id']);
                    $_ads_slots[$slot['id']] = $slot;
                }
            }
        }


        return $_ads_slots;
    }

    public function ad_slot($slot_name)
    {
        return $this->ads_slots[$slot_name] ?? false;
    }

    public function get_ad_slots_options($field = "")
    {

        $ads_slots = $this->ads_slots;

        $_ads_slots = array();

        if (is_array($ads_slots)) {
            foreach ($ads_slots as $slot_id => $slot) {
                // $size_id = wa_theme()->helper('utils')->convert_name_as_id($mapping['identificador']);
                $_ads_slots[$slot_id] = $slot['name'];
            }
        }

        return $_ads_slots;
    }

    public function add_metaboxes($metaboxes, $prefix)
    {

        $id = $prefix . 'ads_metabox';
        $tax_id = $prefix . 'ads_tax_metabox';



        $ads_fields = array(
            $prefix . 'disable_ads' => array(
                'name' => 'Deshabilitar publicidad',
                'id'   => $prefix . 'disable_ads',
                'type' => 'checkbox',
                'desc' => "Marque si desea desactivar toda la publicidad en la vista",
            ),
            $prefix . 'inread_paragraph' => array(
                'name' => 'Posición del banner inRead',
                'desc' => 'Párrafo donde aparecerá el banner inRead',
                'id'   => $prefix . 'inread_paragraph',
                'type' => 'text',
                'default' => '3',
                'attributes' => array(
                    'type' => 'number',
                    'pattern' => '\d*',
                ),
            ),
            $prefix . 'hide_adunits' => array(
                'name' => 'Desactivar anuncios',
                'id'   => $prefix . 'hide_adunits',
                'type' => 'checkbox',
                'desc' => "Marque si desea desactivar bloques de anuncios en la vista",

            ),
            $prefix . 'adunits' => array(
                'name'    => 'Desactivar Bloques de anuncios',
                'desc'    => 'Marque los bloques a desactivar',
                'id'      => $prefix . 'adunits',
                'type'    => 'multicheck',
                'options_cb' => array($this, 'get_ad_slots_options'),
                // 'options' => array(
                //     'ros-t-a' => 'Super Banner A',
                //     'ros-t-b' => 'Super Banner B',
                //     'ros-footer' => 'Footer',
                //     'ros-inread' => 'Primer inRead',
                //     'ros-b-notas' => 'inRead secundarios (cada 5 párrafos)',
                //     'ros-i' => 'Interstitial',
                //     'ros-b-a' => 'Box Banner A',
                //     'ros-b-b' => 'Box Banner B',
                // ),
                'attributes'    => array(
                    'data-conditional-id'     => $prefix . 'hide_adunits',
                    'data-conditional-value'  => 'on',
                ),
            )
        );



        $ads_tax_fields = array(
            $prefix . 'opciones_ads' => array(
                'name' => 'Opciones de Publicidad',
                'type' => 'title',
                'id'   => $prefix . 'opciones_ads'
            ),
            $prefix . 'disable_ads' => array(
                'name' => 'Deshabilitar publicidad',
                'id'   => $prefix . 'disable_ads',
                'type' => 'checkbox',
                'desc' => "Marque si desea desactivar toda la publicidad en la vista",
            ),
            $prefix . 'hide_adunits' => array(
                'name' => 'Desactivar anuncios',
                'id'   => $prefix . 'hide_adunits',
                'type' => 'checkbox',
                'desc' => "Marque si desea desactivar bloques de anuncios en la vista",
            ),
            $prefix . 'adunits' => array(
                'name'    => 'Desactivar Bloques de anuncios',
                'desc'    => 'Marque los bloques a desactivar',
                'id'      => $prefix . 'adunits',
                'type'    => 'multicheck',
                'options_cb' => array($this, 'get_ad_slots_options'),

                // 'options' => array(
                //     'ros-t-a' => 'Super Banner A',
                //     'ros-t-b' => 'Super Banner B',
                //     'ros-footer' => 'Footer',
                //     'ros-inread' => 'Primer inRead',
                //     'ros-b-notas' => 'inRead secundarios (cada 5 párrafos)',
                //     'ros-i' => 'Interstitial',
                //     'ros-b-a' => 'Box Banner A',
                //     'ros-b-b' => 'Box Banner B',
                // ),
                'attributes'    => array(
                    'data-conditional-id'     => $prefix . 'hide_adunits',
                    'data-conditional-value'  => 'on',
                ),
            )
        );


        $_metabox = array(
            'id'            => $id,
            'title'         => esc_html__('Opciones de publicidad', 'cmb2'),
            'object_types'  => get_post_types(array('public' => true), 'names'), //array('post', 'page'), // Post type
            'context'    => 'side',
            'priority'   => 'high',
            'wa_metabox_fields' => apply_filters("wa_theme_get_{$id}_fields", $ads_fields),


        );

        $metaboxes[$id] = $_metabox;


        $_taxonomy_metabox = array(
            'id'            => $tax_id,
            'title'         => esc_html__('Opciones de publicidad', 'cmb2'),
            'object_types'     => array('term'), // Tells CMB2 to use term_meta vs post_meta
            'taxonomies'       => get_taxonomies(array('public' => true)), //array('category', 'post_tag'), // Tells CMB2 which taxonomies should have these fields
            'wa_metabox_fields' => apply_filters("wa_theme_get_{$tax_id}_fields", $ads_tax_fields),

        );

        $metaboxes[$tax_id] = $_taxonomy_metabox;



        return $metaboxes;
    }




    public function article_config_filter($current_config, $post_id)
    {
        $disable_ads = get_post_meta($post_id, 'wa_meta_disable_ads', true) ?? '';
        $hide_ads = get_post_meta($post_id, 'wa_meta_hide_adunits', true) ?? '';
        $inread_paragraph = get_post_meta($post_id, 'wa_meta_inread_paragraph', true) ?? 3;

        $current_config['disable_ads'] = ($disable_ads === "on") ? true : false;

        if (!$current_config['disable_ads']) {
            $current_config['inread_paragraph'] = intval($inread_paragraph);
            if ($hide_ads === "on") {
                $exclude_adunits = get_post_meta($post_id, 'wa_meta_adunits', true) ?? array();


                if (is_array($exclude_adunits) && count($exclude_adunits) > 0) {
                    $current_config['exclude_adunits'] = $exclude_adunits;
                }
            }
        }

        return $current_config;
    }


    public function get_front_settings($settings)
    {

        $settings['enabled'] = filter_var($settings['enabled'], FILTER_VALIDATE_BOOLEAN) ?? false;

        if ($settings['enabled']) {
            $settings['refreshAds'] = filter_var($settings['refreshAds'], FILTER_VALIDATE_BOOLEAN) ?? false;
            $settings['refreshAllAdUnits'] = filter_var($settings['refreshAllAdUnits'], FILTER_VALIDATE_BOOLEAN) ?? false;
            $settings['enableInRead'] = filter_var($settings['enableInRead'], FILTER_VALIDATE_BOOLEAN) ?? false;
            $settings['enableMultipleInRead'] = filter_var($settings['enableMultipleInRead'], FILTER_VALIDATE_BOOLEAN) ?? false;
            $settings['loadOnScroll'] = filter_var($settings['loadOnScroll'], FILTER_VALIDATE_BOOLEAN) ?? false;
            $settings['refresh_time'] = filter_var($settings['refresh_time'], FILTER_VALIDATE_INT) ?? 30;
            $settings['timeToRefreshAllAdUnits'] = filter_var($settings['timeToRefreshAllAdUnits'], FILTER_VALIDATE_INT) ?? 60;
            $settings['refreshAllAdUnitsLimit'] = filter_var($settings['refreshAllAdUnitsLimit'], FILTER_VALIDATE_INT) ?? 0;

            $settings['inReadParagraph'] = filter_var($settings['inReadParagraph'], FILTER_VALIDATE_INT) ?? 3;
            $settings['inReadLimit'] = filter_var($settings['inReadLimit'], FILTER_VALIDATE_INT) ?? 5;

            if (!empty($settings['inread_slot'])) {
                $inread_slot = array();
                $inread_slot['code'] = $this->ad_slot($settings['inread_slot'])['code'] ?? 'inread';
                $inread_slot['size_mapping'] = $this->ad_slot($settings['inread_slot'])['size_mapping'] ?? 'inread';
                $inread_slot['refresh'] = filter_var($this->ad_slot($settings['inread_slot'])['refresh'], FILTER_VALIDATE_BOOLEAN) ?? false;

                $settings['inread_slot'] = $inread_slot;
            }

            if (!empty($settings['multiple_inread_slot'])) {
                $multiple_inread_slot = array();
                $multiple_inread_slot['code'] = $this->ad_slot($settings['multiple_inread_slot'])['code'] ?? 'inread';
                $multiple_inread_slot['size_mapping'] = $this->ad_slot($settings['multiple_inread_slot'])['size_mapping'] ?? 'inread-multiple';
                $multiple_inread_slot['refresh'] = filter_var($this->ad_slot($settings['multiple_inread_slot'])['refresh'], FILTER_VALIDATE_BOOLEAN) ?? false;
                $settings['multiple_inread_slot'] = $multiple_inread_slot;
            }
        } else {
            $settings = array(
                'enabled' => false,
            );
        }

        return apply_filters("wa_ads_front_settings", $settings);
    }
}
