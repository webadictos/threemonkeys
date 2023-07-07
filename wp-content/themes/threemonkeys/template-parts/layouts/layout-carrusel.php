<?php

/**
 * Construye el layout dependiendo los parámetrs
 */


$_layoutArgs = array(
    'has_container' => true,
    'grid_layout' => '',
    'exclude_container' => false,
    'items_layout' => 'article-item',
    'items_layout_css' => '',
    'items_swiper' => true,
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
        'image_animation' => false,
    ),
    'items_visible' => 3,
    'items_visible_movil' => 1,
    'items_visible_tablet' => 2,
    'direction' => 'horizontal',
    'loop' => false,
    'mousewheel' => false,
    'pagination' => true,
    'navigation' => true,
    'items_gap' => 10,
    'autoheight' => true,
    'autoplay' => true,
    'section_description' => null,
    'section_id' => '',
    'section_class' => '',
    'section_name' => '',
    'section_link' => '',
    'section_show_link' => false,
    'show_section_title' => true,
    'section_title_container_class' => '',
    'has_title_container' => true,
    'show_more_btn' => false,
    'show_more_txt' => 'Ver más',
    'show_more_link' => '',
    'section_title_class' => '',
    'queryArgs' => array(),
);

$layoutArgs = wp_parse_args($args, $_layoutArgs);



$_args = array(
    'posts_per_page' => 6,
    'paged' => 1,
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
    'post_status' => 'publish',
);

$_args = array_merge($_args, $layoutArgs['queryArgs']);

$_argsSwiper = array(
    'direction' => $layoutArgs['direction'],
    'loop' => filter_var($layoutArgs['loop'], FILTER_VALIDATE_BOOLEAN),
    'items_gap' => intval($layoutArgs['items_gap']),
    'autoheight' => filter_var($layoutArgs['autoheight'], FILTER_VALIDATE_BOOLEAN),
    'items_visible' => intval($layoutArgs['items_visible']),
    'items_visible_movil' => intval($layoutArgs['items_visible_movil']),
    'items_visible_tablet' => intval($layoutArgs['items_visible_tablet']),
    'mousewheel' => filter_var($layoutArgs['mousewheel'], FILTER_VALIDATE_BOOLEAN),
    'pagination' => filter_var($layoutArgs['pagination'], FILTER_VALIDATE_BOOLEAN),
    'navigation' => filter_var($layoutArgs['navigation'], FILTER_VALIDATE_BOOLEAN),
    'autoplay' => filter_var($layoutArgs['autoplay'], FILTER_VALIDATE_BOOLEAN),
);

$carruselConfig = htmlentities(json_encode($_argsSwiper));

//Args to Boolean
$layoutArgs['has_title_container'] = filter_var($layoutArgs['has_title_container'], FILTER_VALIDATE_BOOLEAN);


$seccion = isset($layoutArgs['queryArgs']['category_name']) ? get_category_by_slug($layoutArgs['queryArgs']['category_name']) : '';

?>
<section class="section <?php echo $layoutArgs['section_class']; ?> <?php echo (trim($layoutArgs['section_id']) !== "") ? "seccion-" . $layoutArgs['section_id'] : ''; ?>" <?php echo (trim($layoutArgs['section_id']) !== "") ? "data-seccion-id='" . $layoutArgs['section_id'] . "'" : ''; ?>>

    <?php if ($layoutArgs['show_section_title']) : ?>


        <header class="section__title-container <?php echo $layoutArgs['section_title_container_class']; ?> container<?php echo $layoutArgs['has_title_container'] ? '' : '-fluid'; ?>">



            <h2 class="section__title <?php echo $layoutArgs['section_title_class']; ?>">
                <?php if ($layoutArgs['section_show_link']) : ?>

                    <?php
                    if ($layoutArgs['section_link'] !== "") {
                        $link = $layoutArgs['section_link'];
                    } else {
                        $link = get_category_link($seccion);
                    }
                    ?>
                    <a href="<?php echo $link; ?>" title="<?php echo $seccion->cat_name; ?>">

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
    if (!$layoutArgs['exclude_container']) :
    ?>
        <div class="container<?php echo $layoutArgs['has_container'] ? '' : '-fluid'; ?>">
        <?php endif; ?>
        <?php


        $articlesQuery = new WP_Query();

        $articlesQuery->query($_args);

        $carouselID = uniqid();
        ?>

        <div class="carousel-container-layout" id="swiper-carousel-<?php echo $carouselID; ?>">
            <div class="swiper grid-swipe-container <?php echo $layoutArgs['grid_layout']; ?>" data-carrusel-config="<?php echo $carruselConfig; ?>" data-carousel-id="<?php echo $carouselID; ?>">


                <div class="swiper-wrapper">

                    <?php

                    $i = 0;
                    $numdestacado = 0;

                    //foreach($carrusel as $post):
                    // setup_postdata($post);

                    $itemArgs = array(
                        'items_swiper' => $layoutArgs['items_swiper'],
                        'items_layout_css' => $layoutArgs['items_layout_css'],
                        'items_config' => $layoutArgs['items_config'],
                    );


                    while ($articlesQuery->have_posts()) : $articlesQuery->the_post();

                        $GLOBALS['exclude_ids'][] = get_the_ID();

                        get_template_part('template-parts/items/' . $layoutArgs['items_layout'], null, $itemArgs);


                        $numdestacado++;
                        $i++;

                    endwhile;
                    wp_reset_postdata();

                    ?>
                    <?php //endforeach;
                    ?>



                </div>

                <?php if ($layoutArgs['navigation']) :
                ?>
                    <!-- If we need navigation buttons -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                <?php endif;
                ?>

                <?php if ($layoutArgs['navigation']) : ?>
                    <!-- If we need pagination -->
                    <div class="swiper-pagination"></div>
                <?php endif; ?>


            </div>


        </div>


        <?php if ($layoutArgs['show_more_btn']) : ?>
            <div class="text-center my-5">
                <a class="btn btn-primary btn-show-more" href="<?php echo $layoutArgs['show_more_link']; ?>"><?php echo $layoutArgs['show_more_txt']; ?></a>
            </div>
        <?php endif; ?>
        <?php
        if (!$layoutArgs['exclude_container']) :
        ?>
        </div>
    <?php endif; ?>
</section>