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

if (!class_exists('CMB2_Field_Social')) {

    class CMB2_Field_Social
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
            add_filter('cmb2_render_social', array($this, 'render_social'), 10, 5);
            add_filter('cmb2_sanitize_social', array($this, 'sanitize_social'), 10, 4);
            add_filter('cmb2_types_esc_social', array($this, 'escape'), 10, 4);
        }

        /**
         * Render field.
         */
        public function render_social($field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object)
        {

            // make sure we specify each part of the value we need.
            $value = wp_parse_args($field_escaped_value, array(
                'social' => '',
                'url' => '',
            ));


            echo $field_type_object->select(array(
                'name'  => $field_type_object->_name('[social]'),
                'id'    => $field_type_object->_id('_social'),
                'options' => $this->get_social_networks($value['social']),
                'desc'    => '',
            ));



            echo $field_type_object->input(array(
                'name'  => $field_type_object->_name('[url]'),
                'id'    => $field_type_object->_id('_url'),
                'value' => $value['url'],
                'placeholder' => "URL del perfil",
                'desc'  => '',
            ));


            echo $field_type_object->_desc(true);
        }

        /**
         * Gets a number of terms and displays them as options
         * @param  CMB2_Field $field 
         * @return array An array of options that matches the CMB2 options array
         */
        public function get_social_networks($value)
        {
            // $_args = array(
            //     'taxonomy'   => 'bushmills_retailers',
            //     'hide_empty' => false,
            // );

            // $taxonomy = 'bushmills_retailers';

            // $terms = get_terms($_args);

            $socialNetworks = apply_filters('wa_theme_manager_social_field_values', array(
                array(
                    'id' => 'facebook',
                    'name' => 'Facebook'
                ),
                array(
                    'id' => 'twitter',
                    'name' => 'Twitter'
                ),
                array(
                    'id' => 'instagram',
                    'name' => 'Instagram'
                ),
                array(
                    'id' => 'youtube',
                    'name' => 'YouTube'
                ),
                array(
                    'id' => 'tiktok',
                    'name' => 'Tiktok'
                ),
                array(
                    'id' => 'linkedin',
                    'name' => 'LinkedIn'
                ),
                array(
                    'id' => 'pinterest',
                    'name' => 'Pinterest'
                ),
                array(
                    'id' => 'flipboard',
                    'name' => 'Flipboard'
                ),
                array(
                    'id' => 'email',
                    'name' => 'Email'
                ),
            ));

            $options = "";
            // Initate an empty array
            $term_options = "";


            $term_options .= '<option value=""></option>';


            // if (!empty($terms)) {
            foreach ($socialNetworks as $socialNetwork) {
                $term_options .= '<option value="' . $socialNetwork['id'] . '" ' . selected($value, $socialNetwork['id'], false) . '>' . $socialNetwork['name'] . '</option>';

                //$term_options[$term->term_id] = $term->name;
            }
            // }

            return $term_options;
        }


        /**
         * Optionally save the latitude/longitude values into two custom fields.
         * sanitize_retail(NULL, Array, 58070, Array)
         */
        public function sanitize_social($check, $meta_value, $object_id, $field_args)
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
    $fp_social_field = new CMB2_Field_Social();
}
