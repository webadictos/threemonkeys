<?php

/**
 * Template Name: Blog Index
 * Description: The template for displaying the Blog index /blog.
 *
 */

get_header();

$page_id = get_option('page_for_posts');
?>
<main class="site-main page-blog" role="main">

	<header class="page-blog__header container-fluid">


		<h1 class="page-blog__title bordered-title"><?php echo get_the_title($page_id); ?></h1>
		<?php
		$category_description = apply_filters('the_content', get_post_field('post_content', $page_id));
		if (!empty($category_description)) :
			echo  '<div class="page-blog__meta">' . $category_description . '</div>';
		endif;
		?>
	</header>

	<section class="archive-articles-container container" data-loadmore-layout="grid" data-loadmore-item-layout="archive-item">
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
	</section>
</main>
<?php
get_footer();
