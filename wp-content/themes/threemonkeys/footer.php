<?php
do_action('wa_before_footer');

$logo_footer = wa_theme()->setting('general', 'logo_footer') ?? '';

?>
<footer id="footer" class="footer-container">
	<div class="container-fluid">
		<div class="row justify-content-center">

			<div class="col-12 footer__logo text-center mt-4 mb-2 my-lg-5">
				<a href="<?php echo esc_url(home_url()); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">

					<?php
					if (!empty($logo_footer)) :
					?>
						<img class="footer__logo-img" src="<?php echo esc_url($logo_footer); ?>" alt="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" width="271" height="46" loading="eager" fetchpriority="high" />
					<?php
					else :
						echo esc_attr(get_bloginfo('name', 'display'));
					endif;
					?>
				</a>


			</div>

			<?php
			if (has_nav_menu('footer-menu')) : // See function register_nav_menus() in functions.php
				/*
								Loading WordPress Custom Menu (theme_location) ... remove <div> <ul> containers and show only <li> items!!!
								Menu name taken from functions.php!!! ... register_nav_menu( 'footer-menu', 'Footer Menu' );
								!!! IMPORTANT: After adding all pages to the menu, don't forget to assign this menu to the Footer menu of "Theme locations" /wp-admin/nav-menus.php (on left side) ... Otherwise the themes will not know, which menu to use!!!
							*/
				wp_nav_menu(
					array(
						'container'       => 'nav',
						'container_class' => 'col-12 footer__nav',
						//'fallback_cb'     => 'WP_Bootstrap4_Navwalker_Footer::fallback',
						'walker'          => new WP_Bootstrap4_Navwalker_Footer(),
						'theme_location'  => 'footer-menu',
						'items_wrap'      => '<ul class="menu nav justify-content-center">%3$s</ul>',
					)
				);
			endif;

			if (is_active_sidebar('third_widget_area')) :
			?>
				<div class="col-12">


					<div class="footer__social">
						<?php
						if (function_exists('wa_show_social_profiles')) {
							wa_show_social_profiles();
						}
						?>
					</div>



					<?php
					dynamic_sidebar('third_widget_area');

					if (current_user_can('manage_options')) :
					?>
						<span class="edit-link"><a href="<?php echo esc_url(admin_url('widgets.php')); ?>" class="badge bg-secondary"><?php esc_html_e('Edit', 'foodandpleasure-theme'); ?></a></span><!-- Show Edit Widget link -->
					<?php
					endif;
					?>
				</div>
			<?php
			endif;
			?>


		</div><!-- /.row -->

		<div class="row pb-4">
			<?php
			if (has_nav_menu('privacy-menu')) : // See function register_nav_menus() in functions.php
				/*
								Loading WordPress Custom Menu (theme_location) ... remove <div> <ul> containers and show only <li> items!!!
								Menu name taken from functions.php!!! ... register_nav_menu( 'footer-menu', 'Footer Menu' );
								!!! IMPORTANT: After adding all pages to the menu, don't forget to assign this menu to the Footer menu of "Theme locations" /wp-admin/nav-menus.php (on left side) ... Otherwise the themes will not know, which menu to use!!!
							*/
				wp_nav_menu(
					array(
						'container'       => 'nav',
						'container_class' => 'col-12 col-md-6 footer__privacy pt-2',
						//'fallback_cb'     => 'WP_Bootstrap4_Navwalker_Footer::fallback',
						'walker'          => new WP_Bootstrap4_Navwalker_Footer(),
						'theme_location'  => 'privacy-menu',
						'items_wrap'      => '<ul class="menu nav justify-content-start">%3$s</ul>',
					)
				);
			endif;
			?>

			<div class="col-12 col-md-6 footer__credits pt-2">
				<p class="text-center text-md-end">© 2018-2023 Food and Pleasure. Todos los derechos reservados. The Cool Spot Group SL.</p>
			</div>
		</div>

	</div><!-- /.container -->
</footer><!-- /#footer -->
<?php
do_action('wa_after_footer');
?>
</div><!-- /#wrapper -->
<?php get_template_part('template-parts/overlays', ''); ?>
<?php
wp_footer();
?>
</body>

</html>