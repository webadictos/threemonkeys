<?php

/**
 * Template Name: Page (Default)
 * Description: Page template with Sidebar on the left side.
 *
 */

get_header();

// the_post();
?>
<main class="site-main container" role="main">

	<?php
	$thumb = "";
	if (has_post_thumbnail()) :

		$thumb = get_the_post_thumbnail(get_the_ID(), 'full', array('title' => get_the_title(), 'alt' => get_the_title(), 'class' => "w-100"));

	endif;
	?>
	<div class="row justify-content-center">
		<div class="col-12 col-md-11 col-lg-10">
			<article id="page-<?php the_ID(); ?>" <?php post_class('page-layout'); ?>>
				<header class="page-header">



					<?php
					if ($thumb !== "") :
					?>
						<figure class="page-thumbnail m-0"><?php echo $thumb; ?></figure>
					<?php
					endif;
					?>


					<div class="page-info">
						<h1 class="page-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
					</div>

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
		</div>
	</div>

</main>
<?php
get_footer();
