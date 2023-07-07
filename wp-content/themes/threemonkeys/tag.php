<?php

/**
 * The Template used to display Tag Archive pages.
 */

get_header();
?>
<main class="site-main tag-archive container" role="main">
	<?php

	if (have_posts()) :
	?>

		<section class="section p-0">

			<header class="section__title-container page-header">
				<h1 class="section__title page-title">
					<?php printf(esc_html__('Tag: %s', 'foodandpleasure-theme'), single_tag_title('', false)); ?>
				</h1>
				<?php
				$tag_description = tag_description();
				if (!empty($tag_description)) :
					echo apply_filters('tag_archive_meta', '<div class="archive-meta">' . $tag_description . '</div>');
				endif;
				?>
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
	else :
		// 404.
		get_template_part('content', 'none');
	endif;

	wp_reset_postdata(); // End of the loop.
	?>
</main>
<?php
get_footer();
