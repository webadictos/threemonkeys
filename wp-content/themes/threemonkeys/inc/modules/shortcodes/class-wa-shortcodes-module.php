<?php

class WA_Shortcodes_Module extends WA_Module
{

    public function init()
    {

        // $this->load_config();

        add_shortcode('webadictos', array($this, 'shortcode_handler'), 10, 2);
        remove_filter('the_content', 'wpautop');
        add_filter('the_content', array($this, 'remove_wpautop_in_front'), 10, 1);
        add_filter('the_content', array($this, 'remove_unused_shortcodes'), 10, 1);
    }

    public function shortcode_handler($atts, $content = null)
    {

        $fixedAtts = array();

        foreach ($atts as $k => $v) {


            if ($v === "false" || $v === "true") {
                $v = filter_var($v, FILTER_VALIDATE_BOOLEAN);
            }

            $fixedAtts[$k] = $v;
        }

        $atts = $fixedAtts;

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

        get_template_part('template-parts/' . $atts['modulo'], null, $atts);


        return ob_get_clean();
    }

    public function remove_wpautop_in_front($content)
    {
        if (is_home() || is_front_page() || is_page())
            return $content;
        else
            return wpautop($content);
    }


    public function remove_unused_shortcodes($content)
    {
        $pattern = $this->unused_shortcode_regex();
        $content = preg_replace_callback('/' . $pattern . '/s', 'strip_shortcode_tag', $content);
        return $content;
    }

    public function unused_shortcode_regex()
    {
        global $shortcode_tags;
        $tagnames = array_keys($shortcode_tags);
        $tagregexp = join('|', array_map('preg_quote', $tagnames));
        $regex = '\\[(\\[?)';
        $regex .= "(?!$tagregexp)";
        $regex .= '\\b([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*+(?:\\[(?!\\/\\2\\])[^\\[]*+)*+)\\[\\/\\2\\])?)(\\]?)';
        return $regex;
    }
}
