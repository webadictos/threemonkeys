<?php

/**
 * Template Name: Section Page
 * Description: Page layout for sections and other elements.
 *
 */

get_header();

// the_post();
?>
<main class="site-main page-section container" role="main">


	<article id="page-<?php the_ID(); ?>" <?php post_class('section-page'); ?>>

		<header class="section-page__header">
			<h1 class="section-page__title bordered-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
		</header><!-- /.entry-header -->

		<div class="page-content">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before'   => '<nav class="page-links" aria-label="' . esc_attr__('Page', 'foodandpleasure-theme') . '">',
					'after'    => '</nav>',
					'pagelink' => esc_html__('Page %', 'foodandpleasure-theme'),
				)
			);
			edit_post_link(
				esc_attr__('Edit', 'foodandpleasure-theme'),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</div>
	</article><!-- /#post-<?php the_ID(); ?> -->

</main>
<?php
get_footer();
