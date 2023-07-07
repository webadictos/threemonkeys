<?php

/**
 * INFINITE SCROLL FUNCTIONS
 */

function getLoadMoreSetup()
{

    global $wp_query;

    $loadmore = array();

    // $parent_cat = smart_category_top_parent_id($category['primary_category']->term_id,true);

    //$cats=wp_get_post_categories(get_the_ID());
    $criterio = $GLOBALS['theme_setup']['scroll']['criterio'];

    $posts_scroll = $GLOBALS['current_post_config']['posts_scroll'];


    $notIn = $GLOBALS['promoted'];

    $notIn[] = get_the_ID();
    $args = array(
        'post_status' => 'publish',
        'post_type' => 'post',
        //  'category__in'   => $cats,
        'posts_per_page' => $GLOBALS['theme_setup']['scroll']['items'],
        'post__not_in'   => $notIn,
        'fields' => 'ids',
        'cache_results'  => false,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'has_password' => false,
    );

    switch ($criterio) {

        case "seccion_principal":
            $category = get_post_primary_category(get_the_ID());
            $cats = array($category['primary_category']->term_id);
            $args['category__in'] = $cats;
            break;

        case "seccion_todas":
            $cats_ids = array();
            $cats = get_the_category(get_the_ID());

            if ($cats) {
                foreach ($cats as $cat) {
                    $cats_ids[] = $cat->term_id;
                }
            }
            $args['category__in'] = $cats_ids;
            break;

        case "tags":
            $tags_ids = array();
            $tags = get_the_tags(get_the_ID());
            if ($tags) {
                foreach ($tags as $tag) {
                    $tags_ids[] = $tag->term_id;
                }
            }

            $args['tag__in'] = $tags_ids;
            break;
        default:

            break;
    }



    $nextPosts = new WP_Query($args);


    $posts = $nextPosts->get_posts();
    $slug = str_replace(get_home_url(), "", get_the_permalink());

    $postLoadMoreIDs = $posts;

    if (is_array($posts_scroll) && count($posts_scroll) > 0) {
        $postLoadMoreIDs = array();
        $postsScrollIDs = array_map(function ($n) {
            return intval($n);
        }, $posts_scroll);


        $postsMerged = array_unique(array_merge($postsScrollIDs, $posts), SORT_REGULAR);

        foreach ($postsMerged as $postMerged) {
            $postLoadMoreIDs[] = $postMerged;
        }
    }





    $loadmore = array(
        'next' => $postLoadMoreIDs,
        'previous' => '',
        'initial' => get_the_ID(),
        'current' => get_the_ID(),
        'current_slug' => $slug,
        'counter' => 1,
        'max_page' => $GLOBALS['theme_setup']['scroll']['items'],
        'previous_ids' => array(get_the_ID()),
        'cats' => isset($cats) ? $cats : null,
    );

    return $loadmore;
}


function getLoadMoreArchiveSetup()
{
    global $wp_query;


    $loadmore = array(
        'posts' => json_encode($wp_query->query_vars), // everything about your loop is here
        'current_page' => get_query_var('paged') ? get_query_var('paged') : 1,
        'max_page' => $wp_query->max_num_pages,
    );

    return $loadmore;
}


function wa_loadmore_ajax_handler()
{
    global $wpdb, $post;

    $postID = intval($_REQUEST['postid']);

    $post = get_post($postID);


    setup_postdata($post);

    if (is_a($post, 'WP_Post')) :

        $template = get_page_template_slug($post);

        if (class_exists('Jetpack_Lazy_Images')) {
            $instance = Jetpack_Lazy_Images::instance();
            $instance->setup_filters();
        }

        ob_start();


        switch ($template) {
            case "article-full.php":
                get_template_part('template-parts/single/content', 'full');


                break;

            case "article-galeria.php":
                get_template_part('template-parts/single/content', 'galeria');

                break;

            default:
                get_template_part('template-parts/single/content', 'single');
        }


        $article = ob_get_clean();

        // echo $article;

        wp_send_json_success($article);


    endif;
    wp_die(); // here we exit the script and even no wp_reset_query() required!
}

add_action('wp_ajax_loadmore', 'wa_loadmore_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_loadmore', 'wa_loadmore_ajax_handler'); // wp_ajax_nopriv_{action}



function wa_archive_loadmore_ajax_handler()
{

    // prepare our arguments for the query
    $args = json_decode(stripslashes($_REQUEST['query']), true);
    $args['paged'] = $_REQUEST['page'] + 1; // we need next page to be loaded
    $args['post_status'] = 'publish';
    $itemLayout = (isset($_REQUEST['item_layout']) &&  trim($_REQUEST['item_layout']) !== "") ? trim($_REQUEST['item_layout']) : 'article-item-nota col-12 col-md-6 col-md-4 mb-3 mb-md-0';
    $layout = (isset($_REQUEST['layout']) &&  trim($_REQUEST['layout']) !== "") ? trim($_REQUEST['layout']) : 'flex';
    // it is always better to use WP_Query but not here
    query_posts($args);

    $_layoutArgs = array(
        'items_layout_css' => $itemLayout,
        'items_swiper' => false,
        'items_config' => array(
            'items_show_tags' => false,
            'items_show_main_cat' => true,
            'items_show_badge_cat' => true,
            'items_show_date' => false,
            'items_show_author' => true,
            'items_show_excerpt' => false,
            'items_show_arrow' => true,
            'items_show_more_btn' => false,
            'image_animation' => true,
        ),
    );



    if (have_posts()) :


        ob_start();
        // run the loop
        $i = 0;

        echo '<div class="row g-0" data-page="' . $args['paged'] . '">';

        if ($layout === "grid") {
            echo "</div>";
        }


        while (have_posts()) : the_post();



            get_template_part('template-parts/items/article', 'item', $_layoutArgs); // Post format: content-index.php



        endwhile;

        if ($layout === "flex") {
            echo "</div>";
        }

        $articles = ob_get_clean();

        // echo $article;

        wp_send_json_success($articles);
    endif;
    wp_die(); // here we exit the script and even no wp_reset_query() required!
}



add_action('wp_ajax_loadmore_archive', 'wa_archive_loadmore_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_loadmore_archive', 'wa_archive_loadmore_ajax_handler'); // wp_ajax_nopriv_{action}