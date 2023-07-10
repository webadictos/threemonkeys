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
 * Class CMB2_Field_Parameters.
 */

if (!class_exists('CMB2_Field_Parameters')) {

    class CMB2_Field_Parameters
    {

        /**
         * Current version number.
         */
        const VERSION = '1.0.0';

        /**
         * Initialize the plugin by hooking into CMB2.
         */
        public function __construct()
        {
            add_filter('cmb2_render_parameters', array($this, 'render_parameters'), 10, 5);
            add_filter('cmb2_sanitize_parameters', array($this, 'sanitize_parameters'), 10, 4);
            add_filter('cmb2_types_esc_parameters', array($this, 'escape'), 10, 4);
        }

        /**
         * Render field.
         */
        public function render_parameters($field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object)
        {

            // make sure we specify each part of the value we need.
            $value = wp_parse_args($field_escaped_value, array(
                'key' => '',
                'value' => '',
            ));


            echo $field_type_object->input(array(
                'name'  => $field_type_object->_name('[key]'),
                'id'    => $field_type_object->_id('_key'),
                'value' => $value['key'],
                'placeholder' => "Key",
                'style' => "width:150px;",
                'desc'    => '',
            ));



            echo $field_type_object->input(array(
                'name'  => $field_type_object->_name('[value]'),
                'id'    => $field_type_object->_id('_value'),
                'value' => $value['value'],
                'placeholder' => "Value",
                'style' => "width:250px;",
                'desc'  => '',
            ));


            echo $field_type_object->_desc(true);
        }

        /**
         * Optionally save the latitude/longitude values into two custom fields.
         * sanitize_retail(NULL, Array, 58070, Array)
         */
        public function sanitize_parameters($check, $meta_value, $object_id, $field_args)
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
    $fp_parameters_field = new CMB2_Field_Parameters();
}
