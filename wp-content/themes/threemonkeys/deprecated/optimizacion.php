<?php

/**
 * Funciones de optimización
 */



//add_filter('the_content', 'add_lazyframe_class', 99);

function add_lazyframe_class($content)
{

    if (!is_feed() && is_single() && !is_preview() || wp_doing_ajax()) {

        // Convert character encoding to 'HTML-ENTITIES' - characters become HTML entities
        // ---------------------------------------------------------------------
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");

        // Set up the_content as a DOM
        // ---------------------------------------------------------------------
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(utf8_decode($content), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Get an array of images in the content - iterate & add the class
        // ---------------------------------------------------------------------
        $iframes = $document->getElementsByTagName('iframe');

        foreach ($iframes as $iframe) {
            $lazyclass = "lazy-wa";
            $iframeSrc = "";
            if ($iframe->hasAttribute('class')) {
                $class = $iframe->getAttribute('class');
                $lazyclass = $class . " " . $lazyclass;
            }

            $iframe->setAttribute('class', $lazyclass);

            if ($iframe->hasAttribute('src')) {
                $iframeSrc = $iframe->getAttribute('src');
                $iframe->removeAttribute('src');
                $iframe->setAttribute('data-src', $iframeSrc);
            }
        }

        $html = $document->saveHTML();

        return $html;
    } else {
        return $content;
    }
}




/**
 * Remove Instagram embed.js script on each embed
 */
add_filter('embed_oembed_html', function ($html, $url, $attr, $post_id) {
    $regex =    '/<script.*instagram\.com\/embed.js.*\s?script>/U';
    $regex_2 =  '/<script.*platform\.instagram\.com\/.*\/embeds\.js.*script>/U';

    if (preg_match($regex, $html) || preg_match($regex_2, $html)) {
        add_filter('kh_has_instagram_embed', '__return_true');

        $html = preg_replace($regex, '', $html);
        $html = preg_replace($regex_2, '', $html);

        return $html;
    }

    return $html;
}, 100, 4);




function omit_instagram_script($provider, $url, $args)
{
    $host = parse_url($provider, PHP_URL_HOST);
    if (strpos($host, 'instagram.com') !== false || $provider == "https://graph.facebook.com/v5.0/instagram_oembed/") {
        $provider = add_query_arg('omitscript', 'true', $provider);
    }
    return $provider;
}
add_filter('oembed_fetch_url', 'omit_instagram_script', 10, 3);

function omit_twitter_script($provider, $url, $args)
{
    $host = parse_url($provider, PHP_URL_HOST);
    if (strpos($host, 'twitter.com') !== false) {
        $provider = add_query_arg('omit_script', 'true', $provider);
    }
    return $provider;
}
add_filter('oembed_fetch_url', 'omit_twitter_script', 10, 3);

function omit_facebook_script($provider, $url, $args)
{
    $host = parse_url($provider, PHP_URL_HOST);
    if (strpos($host, 'facebook.com') !== false) {
        $provider = add_query_arg('omitscript', 'true', $provider);
    }
    return $provider;
}
add_filter('oembed_fetch_url', 'omit_facebook_script', 10, 3);


function waGetFirstCarruselPost()
{
    /*
            'posts_per_page' => 4,
            'paged' => 1,
            //  'meta_key'     => 'rrm_post_show_carrusel',
            //  'meta_value'   => 'on',			
            //'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'category_name' => 'featured',
            'post_status' => 'publish',
*/

    $args = array(
        'post_type'     => 'post',
        'post_status'   => 'publish',
        'posts_per_page' => 1,
        'category_name' => 'featured',
        'fields'        => 'ids',
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    );

    // The Query
    $result_query = new WP_Query($args);

    $ID_array = $result_query->posts;

    // Restore original Post Data
    wp_reset_postdata();

    return $ID_array;
}

function preloadFeaturedImage()
{
    global $post;
    $id = 0;

    if (is_single()) {
        $id = get_the_ID();
    }

    if (is_home() || is_front_page()) {
        $carrusel = waGetFirstCarruselPost();
        if (count($carrusel) > 0)
            $id = $carrusel[0];
    }

    if ($id) {
        if (has_post_thumbnail($id)) :
            $thumb = get_the_post_thumbnail($id, 'large', array());

            $featuredImg = wp_kses_hair($thumb, array('https', 'http'));
?>
            <link rel='preload' as='image' imagesrcset="<?php echo $featuredImg['srcset']['value']; ?>" imagesizes="<?php echo $featuredImg['sizes']['value']; ?>">
<?php
        endif;
    }
}

add_action('wp_head', 'preloadFeaturedImage', -1000);


add_filter('autoptimize_filter_css_defer_inline', 'my_ao_css_defer_inline', 10, 1);
function my_ao_css_defer_inline($inlined)
{


    if (is_single()) {

        $filename = get_stylesheet_directory() . "/assets/css/critical-single.css";

        if (file_exists($filename)) {
            $single_css = file_get_contents($filename);
            return $single_css . "";
        } else {
            return $inlined;
        }
    } else if (is_category()) {

        $filename = get_stylesheet_directory() . "/assets/css/critical-category.css";

        if (file_exists($filename)) {
            $single_css = file_get_contents($filename);
            return $single_css . "";
        } else {
            return $inlined;
        }
    } else {
        return $inlined; // use default a-t-f CSS for all other types 
    }
}


/*
* Imagen Facebook
*     
*/
function filter_wpseo_opengraph_image($img)
{

    global $post;

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
};

// add the filter 
//add_filter('wpseo_opengraph_image', 'filter_wpseo_opengraph_image', 10, 1);


add_filter('perfmatters_used_css', function ($inlined) {
    //custom code here


    $slug = wa_Utils::getSlug();

    $pathToCss = get_stylesheet_directory() . "/assets/critical-css/";

    $filename = "";
    $type = "";

    $fileTmp = $pathToCss . $slug . ".css";

    if (is_front_page() && !is_home()) {

        $filename = $pathToCss  . "home.css";
        $type = "home";
    } else if (!is_front_page() && is_home()) {

        $filename = $pathToCss  . "blog.css";
        $type = "blog";
    } else if (file_exists($fileTmp)) {
        $filename = $fileTmp;
        $type = $slug;
    } else if (is_single()) {
        $filename = $pathToCss  . get_post_type() . ".css";
        $type = get_post_type();

        if (!file_exists($filename)) {
            $filename = $pathToCss . "single.css";
        }
    } else if (is_page()) {
        $filename = $pathToCss  . "page.css";
        $type = "page";
    } else if (is_archive()) {

        $filename = $pathToCss  . "archive.css";
        $type = "archive";

        if (is_post_type_archive()) {
            $post_type = get_query_var('post_type');
            $filenamePostType = $pathToCss . "archive-" .    $post_type . ".css";
            if (file_exists($filenamePostType)) {
                $filename = $filenamePostType;
                $type = "archive" . $post_type;
            }
        }
    } else if (is_tag()) {
        $filename = $pathToCss  . "tag.css";
        $type = "tag";
    } else if (is_search()) {
        $filename = $pathToCss  . "search.css";
        $type = "tag";
    }

    // echo "FILENAME";
    // print_r($filename);
    // error_log("Filename" . $filename);
    // die();

    if (file_exists($filename)) {
        $inlined = file_get_contents($filename);
    }

    // $inlined = ".test-" . $type . "{--test:'{$filename}';}" . $inlined;
    $inlined = str_replace(array("\r", "\n"), '', $inlined);

    return $inlined;
});
