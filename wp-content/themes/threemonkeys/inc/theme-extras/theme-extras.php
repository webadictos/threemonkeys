<?php


function wa_themeSetupScript()
{

    if (is_single() && get_post_type() === 'post') {


        $breadcrumb = '<script type="application/ld+json">';
        $breadcrumb .= '{';
        $breadcrumb .= '"@context": "https://schema.org","@type": "BreadcrumbList",';
        $breadcrumb .= '"itemListElement": [';
        $position = 1;
        $breadcrumb .= '{
            "@type": "ListItem",
            "position": ' . $position . ',
            "name": "' . get_bloginfo('name') . '",   
            "item": "' . get_home_url() . '"
        }';
        $primary = get_post_primary_category(get_the_ID());

        $parents = get_ancestors($primary['primary_category']->term_id, 'category');

        array_unshift($parents, $primary['primary_category']->term_id);
        $parents_reverse = array_reverse($parents);

        foreach ($parents_reverse as $cat) {
            $position++;

            $catname = get_cat_name($cat);
            $breadcrumb .= ",";
            $breadcrumb .= '{
                "@type": "ListItem",
                "position": ' . $position . ',
                "name": "' . $catname . '", 
                "item": "' . get_category_link($cat) . '"
            }';
        }


        $breadcrumb .= ']';
        $breadcrumb .= '}';
        $breadcrumb .= '</script>';

        echo $breadcrumb;
    }
}

add_filter('wa_get_ads_insertion_positions', function ($positions) {

    $new_positions = array(
        'wa_before_header'   => __('Antes del &lt;header&gt; principal', 'cmb2'),
        'wa_after_header'   => __('Después del &lt;/header&gt; principal', 'cmb2'),
        'wa_before_footer'   => __('Antes del <footer>', 'cmb2'),
        'wa_after_footer'   => __('Después del </footer>', 'cmb2'),

        // 'wa_before_single_header'   => __('Antes del <header> de la nota', 'cmb2'),
        // 'wa_after_single_header'   => __('Después del </header> de la nota', 'cmb2'),
        // 'wa_single_header'   => __('Dentro del <header></header> de la nota', 'cmb2'),
        // 'wa_single_footer'   => __('Dentro del <footer></footer> de la nota', 'cmb2'),
        // 'wa_before_single_footer'   => __('Antes del <footer> de la nota', 'cmb2'),
        // 'wa_after_single_footer'   => __('Después del </footer> de la nota', 'cmb2'),
        // 'wa_before_single_entry'   => __('Antesl del texto de la nota', 'cmb2'),
        // 'wa_after_single_entry'   => __('Después del texto de la nota', 'cmb2'),
        // 'wa_single_entry'   => __('Dentro del texto de la nota (Al principio)', 'cmb2'),
    );

    $positions = array_merge($positions, $new_positions);

    $positions = array_unique($positions, SORT_REGULAR);

    return $positions;
});

add_filter('wa_get_codes_positions', function ($positions) {

    $new_positions = array(
        'wa_before_header'   => __('Antes del  &lt;header&gt; principal', 'cmb2'),
        'wa_after_header'   => __('Después del  &lt;/header&gt; principal', 'cmb2'),
        'wa_before_footer'   => __('Antes del  &lt;footer&gt;', 'cmb2'),
        'wa_after_footer'   => __('Después del  &lt;/footer&gt;', 'cmb2'),

        // 'wa_before_single_header'   => __('Antes del <header> de la nota', 'cmb2'),
        // 'wa_after_single_header'   => __('Después del </header> de la nota', 'cmb2'),
        // 'wa_single_header'   => __('Dentro del <header></header> de la nota', 'cmb2'),
        // 'wa_single_footer'   => __('Dentro del <footer></footer> de la nota', 'cmb2'),
        // 'wa_before_single_footer'   => __('Antes del <footer> de la nota', 'cmb2'),
        // 'wa_after_single_footer'   => __('Después del </footer> de la nota', 'cmb2'),
        // 'wa_before_single_entry'   => __('Antesl del texto de la nota', 'cmb2'),
        // 'wa_after_single_entry'   => __('Después del texto de la nota', 'cmb2'),
        // 'wa_single_entry'   => __('Dentro del texto de la nota (Al principio)', 'cmb2'),
    );

    $positions = array_merge($positions, $new_positions);

    $positions = array_unique($positions, SORT_REGULAR);

    return $positions;
});

add_action('pre_get_posts', function ($query) {
    if ($query->is_category() && $query->is_main_query()) {
        $posts_per_page = 13; // Número de elementos para la primera página
        $query->set('posts_per_page', $posts_per_page);
    }
});
