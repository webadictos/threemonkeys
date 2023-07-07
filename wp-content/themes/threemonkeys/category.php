<?php

/**
 * The Template for displaying Category Archive pages.
 */

get_header();
?>
<main class="site-main category-archive " role="main">

	<?php
	if (have_posts()) :
	?>

		<header class="category-archive__header container-fluid">


			<h1 class="category-archive__title bordered-title">
				<?php
				echo single_cat_title('');
				?>
			</h1>
			<?php
			$category_description = category_description();
			if (!empty($category_description)) :
				echo apply_filters('category_archive_meta', '<div class="category-archive__meta">' . $category_description . '</div>');
			endif;
			?>
		</header>


		<section class="archive-articles-container category-archive__items container" data-loadmore-layout="grid" data-loadmore-item-layout="archive-item">
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
			get_template_part('template-parts/category/category', 'loop', $_args);
			?>
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
