<?php

/**
 * Shortcodes
 */

function wa_shortcode_handler($atts, $content = null)
{




    // $a = shortcode_atts(array(
    //     'title' => 'Título de sección',
    //     'section_class' => '',
    //     'title_container_class' => 'section__title-underlined',
    //     'title_class' => '',
    // ), $atts);

    $fixedAtts = array();

    foreach ($atts as $k => $v) {


        if ($v === "false" || $v === "true") {
            $v = filter_var($v, FILTER_VALIDATE_BOOLEAN);
        }

        $fixedAtts[$k] = $v;
    }

    $atts = $fixedAtts;

    // var_dump(($fixedAtts));

    if (isset($atts['title']) && !is_null($atts['title'])) $atts['section_name'] = $atts['title'];

    if (isset($atts['query_args']) && !is_null($atts['query_args'])) {
        $_query = explode(",", $atts['query_args']);
        $queryArgs = array();
        foreach ($_query as $q) {
            $vals = explode(":", $q);

            if ($vals[1] === "false" || $vals[1] === "true") {
                $vals[1] = filter_var($vals[1], FILTER_VALIDATE_BOOLEAN);
            }

            $queryArgs[$vals[0]] = $vals[1];
        }

        $atts['queryArgs'] = $queryArgs;
    }

    if (isset($atts['items_config']) && !is_null($atts['items_config'])) {
        $_query = explode(",", $atts['items_config']);
        $queryArgs = array();
        foreach ($_query as $q) {
            $vals = explode(":", $q);

            if ($vals[1] === "false" || $vals[1] === "true") {
                $vals[1] = filter_var($vals[1], FILTER_VALIDATE_BOOLEAN);
            }

            $queryArgs[$vals[0]] = $vals[1];
        }

        $atts['items_config'] = $queryArgs;
    }




    if (!is_null($content)) {
        $atts['section_description'] = $content;
    }




    ob_start();

    //Si no especifican módulo se devuelve cadena vacia.
    if (is_null($atts['modulo'])) return ob_get_clean();

    // print_r($atts);

    // print_r($content);

    get_template_part('template-parts/' . $atts['modulo'], null, $atts);


    return ob_get_clean();
}

add_shortcode('webadictos', 'wa_shortcode_handler');



function wa_remove_wpautop_in_front($content)
{
    if (is_home() || is_front_page())
        return $content;
    else
        return wpautop($content);
}

// add_action('wp_head', 'wa_remove_wpautop_in_front');
remove_filter('the_content', 'wpautop');
add_filter('the_content', 'wa_remove_wpautop_in_front');
