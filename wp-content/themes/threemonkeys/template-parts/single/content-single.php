<?php

/**
 * The template for displaying content in the single.php template
 */
?>
<?php
$esInfinito = (isset($_REQUEST['action']) &&  $_REQUEST['action'] == "loadmore") ? true : false;

$primary_category = null;
$primary_category = apply_filters('get_primary_category', $primary_category, get_the_ID());


?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post single-entry'); ?> <?php function_exists('wa_article_attributes') ? wa_article_attributes() : ''; ?>>
    <header class="entry-header single-entry__header">
        <?php

        if (has_post_thumbnail()) :
            $thumb = get_the_post_thumbnail(get_the_ID(), 'full', array('title' => get_the_title(), 'alt' => get_the_title(), 'class' => "w-100"));
        else :
            $thumb = '<img src="' . $GLOBALS['default_image'] . '" alt="' . get_the_title() . '" title="' . get_the_title() . '" class="w-100">';
        endif;
        ?>

        <figure class="post-thumbnail single-entry__header-thumbnail"><?php echo $thumb; ?></figure>


        <div class="entry-info single-entry__header-info">

            <a class="single-entry__header-category" href="<?php echo get_category_link($primary_category['primary_category']->term_id); ?>"><?php echo get_cat_name($primary_category['primary_category']->term_id); ?></a>


            <h1 class="entry-title single-entry__header-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>

            <div class="entry-meta single-entry__header-meta">

                <div class="article-autor single-entry__header-meta--author" itemprop="author" itemscope itemtype="http://schema.org/Person"><?php echo __('Por', 'guia-gastronomica'); ?> <span itemprop="name">
                        <?php the_author_posts_link(); ?></span>
                </div>

                <time class="post-meta-date single-entry__header-meta--date" itemprop="datePublished" content="<?php the_date('Y-m-d'); ?>"><?php the_time(get_option('date_format')); ?></time>

            </div><!-- /.entry-meta -->
        </div>

    </header><!-- /.entry-header -->


    <div class="entry-content entry-grid">

        <div class="entry-grid__main-text entry-main-text">

            <?php
            if (function_exists('wa_show_sharebar')) {
                wa_show_sharebar(get_the_ID(), array('networks' => array('facebook', 'whatsapp', 'twitter')));
            }
            ?>

            <?php
            $excerpt = get_the_excerpt();
            ?>
            <div class="entry-excerpt">
                <?php echo wpautop($excerpt); ?>
            </div>

            <?php

            the_content();

            wp_link_pages(array('before' => '<div class="page-link"><span>' . __('Pages:', 'hotbook-theme-v2') . '</span>', 'after' => '</div>'));
            ?>
        </div>


        <aside class="entry-grid__aside d-none d-lg-block">

            <?php
            if (is_active_sidebar('articles_widget_area')) :

            ?>
                <div class="sticky-top ps-5 sticky-header single-widget-area">

                    <?php

                    dynamic_sidebar('articles_widget_area');
                    ?>


                </div>
            <?php endif; ?>

        </aside>

    </div><!-- /.entry-content -->
    <footer class="single-entry__footer">

    </footer>
</article><!-- /#post-<?php the_ID(); ?> -->