<?php

/**
 * The Template for displaying Archive pages.
 */

get_header();
?>
<main class="site-main section-archive" role="main">
	<div class="container">
		<?php
		if (have_posts()) :
		?>

			<section class="section p-0">

				<header class="section__title-container page-header">


					<h1 class="section__title page-title">
						<?php
						if (is_day()) :
							printf(esc_html__('Daily Archives: %s', 'foodandpleasure-theme'), get_the_date());
						elseif (is_month()) :
							printf(esc_html__('Monthly Archives: %s', 'foodandpleasure-theme'), get_the_date(_x('F Y', 'monthly archives date format', 'foodandpleasure-theme')));
						elseif (is_year()) :
							printf(esc_html__('Yearly Archives: %s', 'foodandpleasure-theme'), get_the_date(_x('Y', 'yearly archives date format', 'foodandpleasure-theme')));
						else :
							esc_html_e('Blog Archives', 'foodandpleasure-theme');
						endif;
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
							'items_show_badge_cat' => false,
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
