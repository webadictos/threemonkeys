<?php

use Automattic\Jetpack\Stats\WPCOM_Stats;

/**
 * Construye el layout dependiendo los parámetrs
 */


$_layoutArgs = array(
    'has_container' => true,
    'exclude_container' => false,
    'grid_layout' => 'grid-container-items',
    'items_layout' => 'article-item',
    'items_layout_css' => '',
    'items_swiper' => false,
    'exclude_ids' => true,
    'items_config' => array(
        'items_show_tags' => false,
        'items_show_main_cat' => false,
        'items_show_badge_cat' => false,
        'items_show_date' => false,
        'items_show_author' => false,
        'items_show_excerpt' => false,
        'items_show_arrow' => false,
        'items_show_more_btn' => false,
        'items_more_btn_txt' => __('Leer más', 'wa-theme'),
        'image_animation' => true,
    ),
    'section_id' => '',
    'section_class' => '',
    'section_name' => '',
    'section_description' => null,
    'section_link' => '',
    'section_show_link' => false,
    'show_section_title' => true,
    'section_title_container_class' => '',
    'section_title_class' => '',
    'show_more_btn' => false,
    'show_more_txt' => 'Ver todas',
    'show_more_link' => '',
    'show_empty' => true,
    'queryArgs' => array(),
);


$layoutArgs = wp_parse_args($args, $_layoutArgs);

$_args = array(
    'post_type' => array('post', 'fp_video'),
    'posts_per_page' => 5,
    'paged' => 1,
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
    'post_status' => 'publish',
);

if ($layoutArgs['exclude_ids']) {
    $_args['post__not_in'] =  $GLOBALS['exclude_ids'];
}

$_args = array_merge($_args, $layoutArgs['queryArgs']);

$seccion = isset($layoutArgs['queryArgs']['category_name']) ? get_category_by_slug($layoutArgs['queryArgs']['category_name']) : '';








?>
<section class="section <?php echo $layoutArgs['section_class']; ?> <?php echo (trim($layoutArgs['section_id']) !== "") ? "seccion-" . $layoutArgs['section_id'] : ''; ?>" data-section-id="<?php echo (trim($layoutArgs['section_id']) !== "") ? $layoutArgs['section_id'] : ''; ?>">

    <?php
    if (!$layoutArgs['exclude_container']) :
    ?>
        <div class="container<?php echo $layoutArgs['has_container'] ? '' : '-fluid'; ?>">
        <?php endif; ?>


        <?php if ($layoutArgs['show_section_title']) : ?>


            <header class="section__title-container <?php echo $layoutArgs['section_title_container_class']; ?>">



                <h2 class="section__title <?php echo $layoutArgs['section_title_class']; ?>">
                    <?php if ($layoutArgs['section_show_link']) : ?>

                        <?php
                        if ($layoutArgs['section_link'] !== "") {
                            $link = $layoutArgs['section_link'];
                        } else {
                            $link = get_category_link($seccion);
                        }
                        ?>
                        <a href="<?php echo $link; ?>" title="<?php echo $layoutArgs['section_name']; ?>">

                        <?php endif; ?>

                        <span>
                            <?php if ($layoutArgs['section_name']) : ?>

                                <?php echo $layoutArgs['section_name']; ?>

                            <?php else : ?>
                                <?php echo $seccion->cat_name; ?>

                            <?php endif; ?>

                        </span>


                        <?php if ($layoutArgs['section_show_link']) : ?>

                        </a>
                    <?php endif; ?>
                </h2>

                <?php if (!is_null($layoutArgs['section_description']) && trim($layoutArgs['section_description']) !== "") : ?>
                    <div class="section__description">
                        <?php echo wpautop($layoutArgs['section_description'], false); ?>
                    </div>
                <?php endif; ?>

            </header>

        <?php
        endif;
        ?>




        <?php




        // print_r($featuredPostsSlider);
        ?>


        <div class="<?php echo $layoutArgs['grid_layout']; ?>">

            <?php

            $i = 0;
            $numdestacado = 0;

            //foreach($carrusel as $post):
            // setup_postdata($post);'items_swiper' => false,

            $itemArgs = array(
                'items_swiper' => $layoutArgs['items_swiper'],
                'items_layout_css' => $layoutArgs['items_layout_css'],
                'items_config' => $layoutArgs['items_config'],
            );

            $wordpressAPI = "3bdf3a9b8c63";

            $jetpackBlogID = "";

            if (class_exists('Jetpack_Options')) {
                $jetpackBlogID = Jetpack_Options::get_option('id');
            }


            if (false === ($popularToday = get_transient("wa_trending_week"))) {


                $jetpackURL = 'https://stats.wordpress.com/csv.php?api_key=' . $wordpressAPI . '&blog_id=' . $jetpackBlogID . '&table=postviews&days=7&limit=20&summarize&format=json';

                $response = wp_remote_get($jetpackURL);


                if (is_wp_error($response)) {
                    // Ocurrió un error al hacer la llamada
                    //echo 'Error al obtener los datos de la API: ' . $response->get_error_message();
                } else {
                    // Obtener la respuesta de la API
                    $body = wp_remote_retrieve_body($response);

                    // Convertir la respuesta a un objeto JSON
                    $popularToday = json_decode($body);

                    set_transient('wa_trending_week', $popularToday, 1 * HOUR_IN_SECONDS); // 30 Minutos

                }
            }


            $i = 0;
            $frontpage_id = get_option('page_on_front');
            $blog_id = get_option('page_for_posts');

            $exclude_ids = array($frontpage_id, $blog_id);

            $exclude_ids = array_merge($exclude_ids, $GLOBALS['exclude_ids']);

            $popularIds = array();

            foreach ($popularToday[0]->postviews as $p) {

                $current_id = intval($p->post_id);

                if ($current_id > 0 && !in_array($current_id, $exclude_ids) && $current_id !== get_the_ID()) :

                    if (!has_post_thumbnail($current_id)) continue;
                    if (get_post_type($current_id) !== "post") continue;

                    if ($i > ($_args['posts_per_page'] - 1)) :
                        break;
                    endif;

                    $popularIds[] = $current_id;

                    $i++;

                endif;
            }

            if (count($popularIds) > 0) :

                $_args_popular = array(
                    'post__in' => $popularIds,
                    'no_found_rows' => true,
                    'update_post_meta_cache' => false,
                    'update_post_term_cache' => false,
                    'post_status' => 'publish',
                );

                $articlesQuery = new WP_Query();



                $articlesQuery->query($_args_popular);

                while ($articlesQuery->have_posts()) : $articlesQuery->the_post();
                    $GLOBALS['exclude_ids'][] = get_the_ID();



                    get_template_part('template-parts/items/' . $layoutArgs['items_layout'], null, $itemArgs);


                    $numdestacado++;
                    $i++;

                endwhile;
                wp_reset_postdata();
                wp_reset_query();
            endif;




            ?>
            <?php //endforeach;
            ?>
        </div>


        <?php if ($layoutArgs['show_more_btn']) : ?>
            <div class="show-more">
                <a class="btn btn-primary show-more__btn" href="<?php echo $layoutArgs['show_more_link']; ?>"><?php echo $layoutArgs['show_more_txt']; ?></a>
            </div>
        <?php endif; ?>


        <?php
        if (!$layoutArgs['exclude_container']) :
        ?>
        </div>

    <?php endif; ?>
</section>