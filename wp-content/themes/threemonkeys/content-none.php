<?php

/**
 * The template for displaying "not found" content in the Blog Archives.
 */

$search_enabled = get_theme_mod('search_enabled', '1'); // Get custom meta-value.
?>
<div id="post-0" class="content error404 not-found container my-5 ">
	<h1 class="entry-title text-center"><?php esc_html_e('Not found', 'wa-theme'); ?></h1>
	<div class="entry-content">
		<div class="text-center my-5">
			<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" fill="currentColor" class="bi bi-emoji-dizzy" viewBox="0 0 16 16">
				<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
				<path d="M9.146 5.146a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708.708l-.647.646.647.646a.5.5 0 0 1-.708.708l-.646-.647-.646.647a.5.5 0 1 1-.708-.708l.647-.646-.647-.646a.5.5 0 0 1 0-.708zm-5 0a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 1 1 .708.708l-.647.646.647.646a.5.5 0 1 1-.708.708L5.5 7.207l-.646.647a.5.5 0 1 1-.708-.708l.647-.646-.647-.646a.5.5 0 0 1 0-.708zM10 11a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
			</svg>
		</div>
		<p class="text-center"><?php esc_html_e('It looks like nothing was found at this location.', 'wa-theme'); ?></p>

		<div class="row justify-content-center mt-2">
			<div class="col-12 col-md-8">
				<?php
				if ('1' === $search_enabled) :
					get_search_form();
				endif;
				?>
			</div>
		</div>
		<div>

		</div>
	</div><!-- /.entry-content -->
</div><!-- /#post-0 -->