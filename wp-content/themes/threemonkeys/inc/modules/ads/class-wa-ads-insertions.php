<?php

class WA_Ads_Insertions
{
    protected $prefix;
    protected $loader;
    protected $ads_module;
    protected $insertions_codes;
    public function __construct($ads_module)
    {
        $this->prefix = $ads_module->prefix;
        $this->ads_module = $ads_module;

        $this->ads_module->loader->add_filter('wa_theme_get_wa_theme_options_slots_page_fields', $this, 'add_insertions_fields', 10, 1);

        $this->insertions_codes = $this->get_insertion_codes();

        $this->process_insertions_codes();
    }

    public function add_insertions_fields($fields)
    {

        $_insertion_fields  = array(
            'id'          =>  $this->prefix . 'insertions',
            'type'        => 'group',
            'title' => 'Inserciones de anuncios en el sitio;',
            'desc'       => 'Añade inserciones de bloques de anuncio en determinados espacios definidos del sitio. Hay anuncios que tendrás que agregar directamente como widgets o directamente en el código del tema.',
            'repeatable'  => true,
            'options'     => array(
                'group_title'   => 'Inserción {#}',
                'add_button'    => 'Agregar Código',
                'remove_button' => 'Quitar Código',
                'closed'        => true,  // Repeater fields closed by default - neat & compact.
                'sortable'      => true,  // Allow changing the order of repeated groups.
            ),
            'tab_icon' => 'dashicons-align-wide',
            'tab_name' => 'Inserciones',
            'wa_group_fields' => apply_filters('wa_theme_get_' .  $this->prefix  . 'insertions_fields', array(
                'id' => array(
                    'name' => 'Identificador',
                    'desc' => 'Escribe un identificador para el código',
                    'id'   => 'id',
                    'type' => 'text',
                ),
                'descripcion' =>  array(
                    'name' => 'Descripción',
                    'desc' => '',
                    'id'   => 'descripcion',
                    'type' => 'textarea_small',
                ),
                'posicion' => array(
                    'name'             => 'Posición',
                    'desc'             => 'Selecciona el lugar donde se insertará el anuncio.',
                    'id'               => 'posicion',
                    'type'             => 'select',
                    'show_option_none' => false,
                    // 'default'          => 'wp_body_open',
                    'options'          => apply_filters('wa_get_ads_insertion_positions', array(
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
                'ad_slot' => array(
                    'name'             => 'Bloque de anuncio',
                    'id'               => 'ad_slot',
                    'type'             => 'select',
                    'desc' => "Selecciona el bloque de anuncio a insertar en la posición.",
                    'show_option_none' => false,
                    'default' => 'boxbanner',
                    'options_cb' => array($this->ads_module, 'get_ad_slots_options'),
                )
            )),

        );

        $fields[$this->prefix . 'insertions'] = $_insertion_fields;

        return $fields;
    }

    public function get_insertion_codes()
    {
        $_ads_insertions = array();
        $insertions_codes = array();

        if (class_exists('Wa_Theme_Manager')) {
            $_ads_insertions_opts = Wa_Theme_Manager::get_opciones('wa_theme_options_slots', 'wa_theme_options_insertions');
            $_ads_insertions = apply_filters('wa_ads_insertions', $_ads_insertions_opts); //$ads_options_cmb[0]
        }

        if (is_array($_ads_insertions)) {
            foreach ($_ads_insertions as $slot) {
                if (isset($slot['id'])) {
                    $slot_id = WA_Utils::convert_name_as_id($slot['id']);
                    $insertions_codes[$slot_id] = $slot;
                }
            }
        }

        return $insertions_codes;
    }

    public function process_insertions_codes()
    {

        if (!is_admin() && !is_feed()) {

            // $positions = apply_filters('wa_get_ads_insertion_positions', array(
            //     'wp_body_open' => __('Al inicio del body', 'cmb2'),
            //     'wp_footer'   => __('En el footer', 'cmb2'),
            // ));

            foreach ($this->insertions_codes as $insertion) {

                if ($insertion['posicion'] === "wa_the_content") {

                    add_filter('the_content', function ($content) use ($insertion) {

                        if (is_singular()) {

                            $ad_slot = $this->ads_module->ad_slot($insertion['ad_slot']);

                            if ($ad_slot) {

                                ob_start();

                                $slot = new WA_Ad_Slot($ad_slot);
                                $slot->render_slot();
                                $slot_code = ob_get_clean();

                                $parrafo = intval($insertion['parrafo']);

                                $content = WA_Theme_Manager::insertAdAfterParagraph($slot_code, $parrafo, $content);
                            }
                        }

                        return $content;
                    }, 10);
                } else {
                    add_action($insertion['posicion'], function () use ($insertion) {
                        $ad_slot = $this->ads_module->ad_slot($insertion['ad_slot']);

                        if ($ad_slot) {
                            $slot = new WA_Ad_Slot($ad_slot);
                            $slot->render_slot();
                        }
                    }, 10);
                }
            }
        }
    }
}
