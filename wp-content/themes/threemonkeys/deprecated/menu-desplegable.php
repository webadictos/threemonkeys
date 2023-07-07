<?php

/**
 * Funciones para el menu desplegable
 */

/**
 * Add custom fields to menu item
 *
 * This will allow us to play nicely with any other plugin that is adding the same hook
 *
 * @param  int $item_id 
 * @params obj $item - the menu item
 * @params array $args
 */

function wa_nav_custom_fields($item_id, $item)
{

	wp_nonce_field('show_submenu_nonce', '_show_submenu_nonce');
	$custom_menu_meta = get_post_meta($item_id, '_show_submenu', true);
	$submenuArgs = get_post_meta($item_id, '_submenu_args', true);

	//   if($item->object==="category"){
	// $categories=get_categories(
	//     array( 'parent' => $item->object_id,'hide_empty'=>true )
	// );
?>
	<div class="field-custom_menu_meta description-wide" style="margin: 5px 0;">


		<div class="menu-desplegable-sections">
			<input type="checkbox" name="show_submenu[<?php echo $item_id; ?>][0]" id="_show_submenu-for-<?php echo $item_id; ?>" <?php echo (esc_attr($custom_menu_meta) === 'on') ? 'checked' : ''; ?> />
			<label for="_show_submenu-for-<?php echo $item_id; ?>">Mostrar submenú</label>
		</div>

		<div class="menu-desplegable-sections">
			<label for="_submenu-args-for-<?php echo $item_id; ?>">Argumentos</label>

			<textarea name="submenu_args[<?php echo $item_id; ?>][0]" id="_submenu-args-for-<?php echo $item_id; ?>" placeholder="{}" style="width:100%;height:100px;"><?php echo trim($submenuArgs); ?></textarea>
		</div>



		<input type="hidden" class="nav-menu-id" value="<?php echo $item_id; ?>" />


	</div>

<?php
	//  }
}
add_action('wp_nav_menu_item_custom_fields', 'wa_nav_custom_fields', 10, 2);


/**
 * Save the menu item meta
 * 
 * @param int $menu_id
 * @param int $menu_item_db_id	
 */
function wa_nav_update($menu_id, $menu_item_db_id)
{

	// Verify this came from our screen and with proper authorization.
	if (!isset($_POST['_show_submenu_nonce']) || !wp_verify_nonce($_POST['_show_submenu_nonce'], 'show_submenu_nonce')) {
		return $menu_id;
	}

	if (isset($_POST['show_submenu'][$menu_item_db_id])) {

		$sanitized_data = sanitize_text_field($_POST['show_submenu'][$menu_item_db_id][0]);



		update_post_meta($menu_item_db_id, '_show_submenu', $sanitized_data);
	} else {
		delete_post_meta($menu_item_db_id, '_show_submenu');
	}

	if (isset($_POST['submenu_args'][$menu_item_db_id])) {

		$sanitized_data = sanitize_text_field($_POST['submenu_args'][$menu_item_db_id][0]);



		update_post_meta($menu_item_db_id, '_submenu_args', $sanitized_data);
	} else {
		delete_post_meta($menu_item_db_id, '_submenu_args');
	}
}
add_action('wp_update_nav_menu_item', 'wa_nav_update', 10, 2);
