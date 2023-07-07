<?php

/**
 * The Template for displaying Author pages.
 */

get_header();
?>
<main class="site-main author-archive" role="main">
	<div class="container">
		<?php
		if (have_posts()) :
			/**
			 * Queue the first post, that way we know
			 * what author we're dealing with (if that is the case).
			 *
			 * We reset this later so we can run the loop
			 * properly with a call to rewind_posts().
			 */
			//the_post();
		?>


			<section class="section p-0">

				<header class="section__title-container page-header">


					<h1 class="section__title page-title">
						<?php
						printf(esc_html__('Autor: %s', 'foodandpleasure-theme'), get_the_author());
						?>
					</h1>

				</header>

			</section>
			<section class="section p-0">
				<div class="archive-articles-container" data-loadmore-layout="grid" data-loadmore-item-layout="archive-item">
					<div class="row"></div>
					<?php
					$_args = array(
						'items_layout_css' => 'archive-item',
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
			//	get_template_part('author', 'bio');

			/**
			 * Since we called the_post() above, we need to
			 * rewind the loop back to the beginning that way
			 * we can run the loop properly, in full.
			 */
			rewind_posts();

		else :
			// 404.
			get_template_part('content', 'none');
		endif;

		wp_reset_postdata(); // End of the loop.
		?>
	</div>
</main>
<?php
get_footer();
