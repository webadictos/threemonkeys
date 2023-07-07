<?php

/**
 * Funciones útiles para obtener la categoría principal de una entrada
 */


class WA_Main_Term extends WA_Module
{

    public function init()
    {
        $this->loader->add_filter('get_primary_category', $this, 'get_primary_category_filter', 10, 2);
        $this->loader->add_filter('body_class', $this, 'add_primary_term_to_bodyclass', 10, 1);
        $this->loader->add_filter('post_class', $this, 'add_primary_term_to_postclass', 10, 3);
        $this->loader->add_filter('nav_menu_css_class', $this, 'add_parent_term_to_nav', 10, 2);
        $this->loader->add_action('nav_menu_css_class', $this, 'add_current_nav_class', 10, 2);
    }

    /**
     * Returns object or ID of top-level taxonomy
     *
     * @param    string      $termID      Category ID to be checked
     * @return   string      $parent  ID of top-level parent category
     */
    public function get_parent_term($termID, $object = false, $tax = 'category')
    {
        $parent = null;
        while ($termID) {
            $cat = get_term($termID, $tax);

            $termID = $cat->parent; // assign parent ID (if exists) to $catid
            // the while loop will continue whilst there is a $catid
            // when there is no longer a parent $catid will be NULL so we can assign our $catParent
            if (!$object)
                $parent = $cat->term_id;
            else $parent = $cat;
        }
        return $parent;
    }


    public function get_primary_term($post_id, $term = 'category', $return_all_categories = false)
    {
        // $arg_list = func_get_args();

        // print_r($arg_list);

        $return = array();

        if (class_exists('WPSEO_Primary_Term')) {
            // Show Primary category by Yoast if it is enabled & set
            $wpseo_primary_term = new WPSEO_Primary_Term($term, $post_id);
            $primary_term = get_term($wpseo_primary_term->get_primary_term());

            if (!is_wp_error($primary_term)) {
                $return['primary_category'] = $primary_term;
            }
        }

        // print_r($post_id);
        // print_r($term);
        // print_r($return);

        if (empty($return['primary_category']) || $return_all_categories) {

            $categories_list = get_the_terms($post_id, $term);


            if (empty($return['primary_category']) && !empty($categories_list)) {
                $return['primary_category'] = $categories_list[0];  //get the first category
            }
            if ($return_all_categories) {
                $return['all_categories'] = array();

                if (!empty($categories_list)) {
                    foreach ($categories_list as &$category) {
                        $return['all_categories'][] = $category->term_id;
                    }
                }
            }
        }

        return $return;
    }

    public function add_primary_term_to_postclass($classes, $class, $postId)
    {

        //  $primary_category = apply_filters('get_primary_category', null, $postId);

        $primary_category = $this->get_primary_category($postId);
        // $parent_cat = isset($primary['primary_category']) ? $this->get_parent_term($primary['primary_category']->term_id, true) : null;

        if (!is_null($primary_category)) {
            if (is_object($primary_category['parent_category']))    $classes[] = "parent-category-" . $primary_category['parent_category']->slug;
            if (is_object($primary_category['primary_category']))    $classes[] = "primary-category-" . $primary_category['primary_category']->slug;
        }

        return $classes;
    }

    public function get_primary_category($post_id = 0, $tax = 'category')
    {
        global $post;

        if (!$post_id) $post_id = get_the_ID();

        $category = $this->get_primary_term($post_id, $tax);
        $parent_cat = isset($category['primary_category']) ? $this->get_parent_term($category['primary_category']->term_id, true, $tax) : null;

        $primary_category = array(
            'primary_category' => $category['primary_category'] ?? null,
            'parent_category' => $parent_cat,
        );

        return $primary_category;
    }

    public function get_primary_category_filter($primary_category, $post_id = 0, $tax = 'category')
    {
        global $post;


        if (!$post_id) $post_id = get_the_ID();

        if ($post_id) {
            $primary_category = $this->get_primary_category($post_id, $tax);
        }

        return $primary_category;
    }


    public function add_primary_term_to_bodyclass($classes)
    {

        global $post;

        $term = get_queried_object();


        if (is_single()) {


            $post_id = get_the_ID();

            $primary_category = $this->get_primary_category($post_id);

            if (!is_null($primary_category)) {
                if (is_object($primary_category['parent_category']))    $classes[] = "parent-category-" . $primary_category['parent_category']->slug;
                if (is_object($primary_category['primary_category']))    $classes[] = "primary-category-" . $primary_category['primary_category']->slug;
            }
        } else if (is_category()) {

            $parent_term = $this->get_parent_term($term->term_id, true, 'category');

            if (is_object($parent_term))    $classes[] = "parent-category-" . $parent_term->slug;

            $classes[] = "primary-category-" . $term->slug;
        } else  if (is_tax()) {

            $parent_term = $this->get_parent_term($term->term_id, true, $term->taxonomy);

            if (is_object($parent_term))    $classes[] = "parent-tax-" . $parent_term->slug;

            $classes[] = "primary-tax-" . $term->slug;
        }


        return $classes;
    }

    public function add_parent_term_to_nav($classes, $item)
    {
        if ('category' == $item->object) {
            $category = get_category($item->object_id);
            $parent_term = $this->get_parent_term($category->term_id, true, 'category');

            if (is_object($parent_term))    $classes[] = "parent-category-" . $parent_term->slug;
        }
        return $classes;
    }


    public function add_current_nav_class($classes, $item)
    {

        // Getting the current post details
        global $post;

        // Get post ID, if nothing found set to NULL
        $id = (isset($post->ID) ? get_the_ID() : NULL);

        // Checking if post ID exist...
        if (isset($id)) {

            $primary_category = $this->get_primary_category($id);

            // Getting the URL of the menu item
            $menu_slug = strtolower(trim($item->url));

            // If the menu item URL contains the current post types slug add the current-menu-item class
            if (is_object($primary_category['parent_category'])) {

                $cat_link = get_category_link($primary_category['parent_category']);

                if (strpos($menu_slug, $cat_link) !== false) {

                    if (!in_array('main-category', $classes)) {
                        $classes[] = 'main-category';
                    }
                }
            }
        }

        // Return the corrected set of classes to be added to the menu item
        return $classes;
    }
}
