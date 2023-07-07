<?php

/**
 * What to Read Next
 */

?>
<?php
/**
 * Sección sin sidebar
 * Beauty
 * Layout Sección 2
 */

$category = get_post_primary_category(get_the_ID());
$parent_cat = isset($category['primary_category']) ? smart_category_top_parent_id($category['primary_category']->term_id, true) : null;

//$cats = wp_get_post_categories(get_the_ID(), array('fields' => 'ids'));

$_layoutArgs = array(
    'section_id' => is_object($parent_cat) ? 'seccion-' . $parent_cat->slug : "",
    'section_class' => is_object($parent_cat) ? 'section-related main-category-' . $parent_cat->slug : 'section-related',
    'section_name' => 'Lo último',
    'grid_layout' => 'grid-container-items scroll-mobile three-cols',
    'section_show_link' => false,
    'section_show_more_link' => false,
    'exclude_container' => true,
    'items_layout_css' => 'article-item--related',
    'has_container' => false,
    'items_config' => array('items_show_tags' => false, 'items_show_main_cat' => false, 'items_show_badge_cat' => false, 'items_show_date' => false, 'items_show_author' => false, 'items_show_excerpt' => false),
    'show_empty' => false,
    'queryArgs' => array(
        'posts_per_page' => 3,
        // 'category_name' => is_object($parent_cat) ?  $parent_cat->slug : null,
        'post__not_in' => array(get_the_ID()),
    )
);

$layoutArgs = wp_parse_args($args, $_layoutArgs);

//get_template_part( 'template-parts/layouts/layout','without-sidebar',$layoutArgs);
get_template_part('template-parts/layouts/layout', '', $layoutArgs);
?>
