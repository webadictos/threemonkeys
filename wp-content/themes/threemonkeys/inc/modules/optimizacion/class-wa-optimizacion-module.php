<?php

class WA_Optimizacion_Module extends WA_Module
{

    protected $path_to_css;

    public function init()
    {
        // $this->load_config();

        $this->loader->add_filter('oembed_fetch_url', $this, 'ommit_social_scripts', 10, 3);
        $this->loader->add_filter('embed_oembed_html', $this, 'remove_instagram_script', 100, 4);

        $this->loader->add_filter('perfmatters_used_css', $this, 'critical_css', 10, 1);
        $this->loader->add_filter('autoptimize_filter_css_defer_inline', $this, 'critical_css', 10, 1);

        $this->path_to_css = get_stylesheet_directory() . "/assets/critical-css/";

        if (isset($this->settings['disable_photon_opengraph']) && $this->settings['disable_photon_opengraph'])
            $this->loader->add_filter('wpseo_opengraph_image', $this, 'disable_photon_opengraph_image', 10, 1);
    }

    public function ommit_social_scripts($provider, $url, $args)
    {
        $social_networks = array(
            'twitter' => array(
                'host' => 'twitter.com',
                'arg' => 'omit_script',
            ),
            'instagram' => array(
                'host' => 'instagram.com',
                'arg' => 'omitscript',
            ),
            'facebook' => array(
                'host' => 'facebook.com',
                'arg' => 'omitscript',
            )
        );
        $host = parse_url($provider, PHP_URL_HOST);

        foreach ($social_networks as $social_network) {
            if (strpos($host, $social_network['host']) !== false) {
                $provider = add_query_arg($social_network['arg'], 'true', $provider);
                break;
            }
        }

        return $provider;
    }

    public function remove_instagram_script($html, $url, $attr, $post_id)
    {

        $regex =    '/<script.*instagram\.com\/embed.js.*\s?script>/U';
        $regex_2 =  '/<script.*platform\.instagram\.com\/.*\/embeds\.js.*script>/U';

        if (preg_match($regex, $html) || preg_match($regex_2, $html)) {
            add_filter('kh_has_instagram_embed', '__return_true');

            $html = preg_replace($regex, '', $html);
            $html = preg_replace($regex_2, '', $html);

            return $html;
        }

        return $html;
    }

    private static function get_url_type()
    {
        global $wp_query;

        $type = '';

        if ($wp_query->is_page) {
            $type = is_front_page() ? 'front' : 'page-' . $wp_query->post->ID;
        } elseif ($wp_query->is_home && !is_front_page()) {
            $type = 'home';
        } elseif ($wp_query->is_single) {
            $type = get_post_type() !== false ? get_post_type() : 'single';
        } elseif ($wp_query->is_category) {
            $type = 'category';
        } elseif ($wp_query->is_tag) {
            $type = 'tag';
        } elseif ($wp_query->is_tax) {
            $type = 'tax';
        } elseif ($wp_query->is_archive) {
            $type = $wp_query->is_day ? 'day' : ($wp_query->is_month ? 'month' : ($wp_query->is_year ? 'year' : ($wp_query->is_author ? 'author' : 'archive')));
        } elseif ($wp_query->is_search) {
            $type = 'search';
        } elseif ($wp_query->is_404) {
            $type = '404';
        }

        return $type;
    }


    public function critical_css($inlined)
    {
        global $wp_query;

        // $slug = $GLOBALS['WA_Theme']->helper('utils')->formatted_slug();

        $filename = "";
        $type = self::get_url_type();


        $filename = $this->path_to_css  . $type . ".css";

        // $fileTmp = $this->path_to_css . $slug . ".css";

        // if (is_front_page() && !is_home()) {

        //     $filename = $this->path_to_css  . "home.css";
        //     $type = "home";
        // } else if (!is_front_page() && is_home()) {

        //     $filename = $this->path_to_css  . "blog.css";
        //     $type = "blog";
        // } else if (file_exists($fileTmp)) {
        //     $filename = $fileTmp;
        //     $type = $slug;
        // } else if (is_single()) {
        //     $filename = $this->path_to_css  . get_post_type() . ".css";
        //     $type = get_post_type();

        //     if (!file_exists($filename)) {
        //         $filename = $this->path_to_css . "single.css";
        //     }
        // } else if (is_page()) {
        //     $filename = $this->path_to_css  . "page.css";
        //     $type = "page";
        // } else if (is_archive()) {

        //     $filename = $this->path_to_css  . "archive.css";
        //     $type = "archive-";


        //     if (is_post_type_archive()) {
        //         $post_type = get_query_var('post_type');
        //         $filenamePostType = $this->path_to_css . "archive-" .    $post_type . ".css";
        //         $type = "post-type-archive-" . $post_type;

        //         if (file_exists($filenamePostType)) {
        //             $filename = $filenamePostType;
        //         }
        //     }
        //     if (is_tax()) {
        //         $term = get_queried_object();
        //         $post_type = $term->taxonomy;
        //         $filenamePostType = $this->path_to_css . "tax-archive-" .    $post_type . ".css";
        //         $type = "tax-archive-" . $post_type;

        //         if (file_exists($filenamePostType)) {
        //             $filename = $filenamePostType;
        //         }
        //     }
        // } else if (is_category()) {
        //     $filename = $this->path_to_css  . "category.css";
        //     $type = "category";
        // } else if (is_tag()) {
        //     $filename = $this->path_to_css  . "tag.css";
        //     $type = "tag";
        // }
        // if (is_search()) {
        //     $filename = $this->path_to_css  . "search.css";
        //     $type = "search";
        // }

        if (file_exists($filename)) {
            $inlined = file_get_contents($filename);
        }

        $inlined = ".test-" . $type . "{--test:'{$type}';}" . $inlined;
        $inlined = str_replace(array("\r", "\n"), '', $inlined);

        return $inlined;
    }

    public function disable_photon_opengraph_image($img)
    {

        global $post;


        if (!is_single()) return $img;

        if (!class_exists('Jetpack_Photon')) return $img;
        $photon_removed = remove_filter('image_downsize', array(Jetpack_Photon::instance(), 'filter_image_downsize'));
        // Call wp_get_attachment_image(), wp_get_attachment_image_src(), or anything else that ultimately calls image_downsize()



        $the_thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "facebook-featured-image");
        $thumbnail_src = $the_thumbnail_src[0];
        //change this to exact WxH of your custom image size
        $check_thumb = strpos($thumbnail_src, '1200x630');
        if ($check_thumb) {
            $img = $thumbnail_src;
        }

        if ($photon_removed) {
            add_filter('image_downsize', array(Jetpack_Photon::instance(), 'filter_image_downsize'), 10, 3);
        }

        return $img;
    }
}
