<?php
do_action('wa_before_footer');

$logo_footer = wa_theme()->setting('general', 'logo_footer') ?? '';

?>
<footer id="footer" class="footer-container">
	<div class="container-fluid">

		<div class="row">

			<div class="col-12 footer__credits pt-3">
				<p class="text-center">© <?php echo date("Y"); ?> <?php echo get_bloginfo('name'); ?></p>
			</div>
		</div>

	</div><!-- /.container -->
</footer><!-- /#footer -->
<?php
do_action('wa_after_footer');
?>
</div><!-- /#wrapper -->

<?php
wp_footer();
?>
</body>

</html>