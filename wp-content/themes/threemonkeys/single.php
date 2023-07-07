<?php

/**
 * The Template for displaying all single posts.
 */

get_header();



$page_id = get_option('page_for_posts');

?>

<main class="site-main single-main container" role="main">

	<section class="articles-container">


		<?php

		if (have_posts()) :
			while (have_posts()) :
				the_post();
				$do_not_duplicate[] = get_the_ID();
				get_template_part('template-parts/single/content', 'single');
			endwhile;
		endif;

		//   $blocks = parse_blocks($post->post_content);
		//     print_r($blocks);

		wp_reset_postdata(); //  loop.
		?>



	</section>

</main>

<?php

get_footer();
