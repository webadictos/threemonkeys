<?php
/*
Plugin Name: CMB2 Field Type: Google Maps
Plugin URI: https://github.com/mustardBees/cmb_field_map
GitHub Plugin URI: https://github.com/mustardBees/cmb_field_map
Description: Google Maps field type for CMB2.
Version: 2.2.0
Author: Phil Wylie
Author URI: https://www.philwylie.co.uk/
License: GPLv2+
*/

/**
 * Class PW_CMB2_Field_Google_Maps.
 */

if (!class_exists('CMB2_Ad_Slot')) {

    class CMB2_Ad_Slot
    {

        /**
         * Current version number.
         */
        const VERSION = '2.2.1';

        /**
         * Initialize the plugin by hooking into CMB2.
         */
        public function __construct()
        {
            add_filter('cmb2_render_ad_slot', array($this, 'render_ad_slot'), 10, 5);
            add_filter('cmb2_sanitize_ad_slot', array($this, 'sanitize_ad_slot'), 10, 4);
            add_filter('cmb2_types_esc_ad_slot', array($this, 'escape'), 10, 4);
        }

        /**
         * Render field.
         */
        public function render_ad_slot($field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object)
        {

            // make sure we specify each part of the value we need.
            $value = wp_parse_args($field_escaped_value, array(
                'platform' => '',
                'width' => '',
                'height' => '',
            ));


            echo $field_type_object->select(array(
                'name'  => $field_type_object->_name('[platform]'),
                'id'    => $field_type_object->_id('_platform'),
                'options' => $this->get_platforms($value['platform']),
                'style' => "margin-top:0;margin-right:1rem;",
            ));

            echo $field_type_object->input(array(
                'name'  => $field_type_object->_name('[width]'),
                'id'    => $field_type_object->_id('_width'),
                'value' => $value['width'],
                'placeholder' => "Ancho",
                'style' => "width:100px;",
            ));

            echo $field_type_object->input(array(
                'name'  => $field_type_object->_name('[height]'),
                'id'    => $field_type_object->_id('_height'),
                'value' => $value['height'],
                'placeholder' => "Alto",
                'style' => "width:100px;",
            ));

            echo $field_type_object->_desc(true);
        }

        /**
         * Gets a number of terms and displays them as options
         * @param  CMB2_Field $field 
         * @return array An array of options that matches the CMB2 options array
         */
        public function get_platforms($value)
        {
            // $_args = array(
            //     'taxonomy'   => 'bushmills_retailers',
            //     'hide_empty' => false,
            // );

            // $taxonomy = 'bushmills_retailers';

            // $terms = get_terms($_args);

            $platforms = array(
                array(
                    'id' => 'mobile',
                    'name' => 'Mobile'
                ),
                array(
                    'id' => 'desktop',
                    'name' => 'Desktop'
                ),
                array(
                    'id' => 'all',
                    'name' => 'Todas'
                ),
            );

            $options = "";
            // Initate an empty array
            $term_options = "";


            $term_options .= '<option value=""></option>';


            // if (!empty($terms)) {
            foreach ($platforms as $platform) {
                $term_options .= '<option value="' . $platform['id'] . '" ' . selected($value, $platform['id'], false) . '>' . $platform['name'] . '</option>';

                //$term_options[$term->term_id] = $term->name;
            }
            // }

            return $term_options;
        }


        /**
         * Optionally save the latitude/longitude values into two custom fields.
         * sanitize_retail(NULL, Array, 58070, Array)
         */
        public function sanitize_ad_slot($check, $meta_value, $object_id, $field_args)
        {
            // if not repeatable, bail out.
            if (!is_array($meta_value) || !$field_args['repeatable']) {
                return $check;
            }

            foreach ($meta_value as $key => $val) {
                $meta_value[$key] = array_filter(array_map('sanitize_text_field', $val));
            }

            return array_filter($meta_value);
        }

        public static function escape($check, $meta_value, $field_args, $field_object)
        {
            // if not repeatable, bail out.
            if (!is_array($meta_value) || !$field_args['repeatable']) {
                return $check;
            }

            foreach ($meta_value as $key => $val) {
                $meta_value[$key] = array_filter(array_map('esc_attr', $val));
            }

            return array_filter($meta_value);
        }
    }
    $ad_slot_field = new CMB2_Ad_Slot();
}
