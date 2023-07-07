<?php

/**
 * The Template for displaying Search Results pages.
 */

get_header();
?>
<main class="site-main search-results" role="main">
	<div class="container">
		<?php

		if (have_posts()) :
		?>

			<section class="section p-0">

				<header class="section__title-container page-header">
					<h1 class="section__title page-title">
						<?php printf(esc_html__('Search Results for: %s', 'wa-theme'), '<span class="search-keywords">' . get_search_query() . '</span>'); ?>
					</h1>
				</header>

			</section>


			<section class="section p-0">
				<div class="archive-articles-container" data-loadmore-layout="grid" data-loadmore-item-layout="archive-item">
					<div class="row"></div>
					<?php
					$_args = array(
						'items_layout_css' => 'article-item archive-item',
						'items_config' => array(
							'items_show_tags' => false,
							'items_show_main_cat' => false,
							'items_show_badge_cat' => true,
							'items_show_date' => false,
							'items_show_author' => true,
							'items_show_excerpt' => false,
							'items_show_arrow' => true,
							'items_show_more_btn' => false,
						),
					);
					get_template_part('template-parts/archive', 'loop', $_args);
					?>
				</div>
			</section>

		<?php
		//	get_template_part('archive', 'loop');
		else :
		?>
			<article id="post-0" class="post no-results not-found">
				<header class="entry-header">
					<h1 class="entry-title"><?php esc_html_e('Nothing Found', 'foodandpleasure-theme'); ?></h1>
				</header><!-- /.entry-header -->
				<p><?php esc_html_e('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'foodandpleasure-theme'); ?></p>
				<?php
				get_search_form();
				?>
			</article><!-- /#post-0 -->
		<?php
		endif;
		wp_reset_postdata();
		?>
	</div>
</main>

<?php
get_footer();
