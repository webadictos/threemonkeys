<?php

/**
 * Widget de filtros activos
 */
function wa_active_filters()
{

    $_chosen_attributes = wa_get_filters_in_url();
    $base_link          = home_url($_SERVER['REQUEST_URI']);
    if (is_single()) {
        $base_link = get_post_type_archive_link('gg_socio');
    }

    if (0 < count($_chosen_attributes)) {


        echo '<ul class="wa-filter list-unstyled d-flex flex-wrap gap-2">';

        // Attributes.
        if (!empty($_chosen_attributes)) {

            $grouped_atts = array();

            foreach ($_chosen_attributes as $taxonomy => $data) {

                $tax = get_taxonomy($taxonomy);


                foreach ($data['terms'] as $term_slug) {
                    $term = get_term_by('slug', $term_slug, $taxonomy);
                    if (!$term) {
                        continue;
                    }

                    $filter_name    = $taxonomy;
                    $current_filter = isset($_GET[$filter_name]) ? explode(',', (wp_unslash($_GET[$filter_name]))) : array(); // WPCS: input var ok, CSRF ok.
                    $current_filter = array_map('sanitize_title', $current_filter);
                    $new_filter     = array_diff($current_filter, array($term_slug));

                    $link = remove_query_arg(array($filter_name), $base_link);

                    if (count($new_filter) > 0) {
                        $link = add_query_arg($filter_name, implode(',', $new_filter), $link);
                    }
                    //<button type="button" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="propio en un radio limitado" >Delivery</button>
                    $grouped_atts[$tax->labels->singular_name][] = '<a class="btn btn-primary buscador-filters__check--btn active" rel="nofollow" role="button" aria-label="' . esc_attr__('Remove filter', 'guia-gastronomica') . '" href="' . esc_url($link) . '" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="' . sprintf('%1$s : %2$s', $tax->labels->singular_name, esc_html($term->name)) . '" ><small>&#10006;</small> ' . esc_html($term->name) . '</a>';
                }
            }
        }

        if (count($grouped_atts) > 0) {

            foreach ($grouped_atts as $key => $atts) {
                //<span class="wa-filter__label">' . $key . ':</span> 
                // echo '<li class="wa-filter__chosen chosen">';
                $before = "<li>";
                $after = "</li>";
                $separator = " ";

                $items = $before . implode("{$after}{$separator}{$before}", $atts) . $after;

                echo $items;
                //echo '<span class="wa-filter__term">' . implode(" ", $atts) . '</span>';

                // echo "</li>";
            }
        }

        echo '</ul>';
    }
}


function wa_get_filters_in_url()
{


    $chosen_attributes = array();

    if (!empty($_GET)) {
        foreach ($_GET as $key => $value) {
            if (0 === strpos($key, 'gg_')) {
                $taxonomy     = $key;
                $filter_terms = !empty($value) ? explode(',', (wp_unslash($value))) : array();

                if (empty($filter_terms) || !taxonomy_exists($taxonomy)) {
                    continue;
                }

                $chosen_attributes[$taxonomy]['terms'] = array_map('sanitize_title', $filter_terms); // Ensures correct encoding.
            }
        }
    }

    return $chosen_attributes;
}


add_action('pre_get_posts', 'wa_set_random_socios');
function wa_set_random_socios($query)
{


    //print_r($query);

    if (is_post_type_archive('gg_socio') || (is_single() && get_post_type() === "gg_socio")) :

        //If you wanted it for the archive of a custom post type use: is_post_type_archive( $post_type )
        //Set the order ASC or DESC
        //    $query->set( 'order', 'ASC' );
        //Set the orderby
        //$tax_query['relation'] = "AND";

        //$query->set('orderby', 'rand');
        // $query->set('tax_query', $tax_query);

        // $query->set('relation', 'AND');

        //print_r($query);


        // Some conditional to tell our query from other queries
        if ($query->get('suppress_filters')) {
            $query->set('suppress_filters', true);
        }

    endif;
};



function waShowRecommendForm($css = "color-gg-secondary")
{
    $currentLang = wa_get_lang();

    $url = waGetUserPage('recomendar');

?>

    <div class="recommend-link d-block text-end py-3">
        <a class="<?php echo $css; ?>" href="<?php echo get_the_permalink($url); ?>" data-bs-toggle="modal" data-bs-target="#formRecommend"><?php echo __('¿No encuentras lo que estás buscando? Recomiéndalo <span class="text-decoration-underline">aquí</span>', 'guia-gastronomica'); ?></a>
    </div>
<?php
}
