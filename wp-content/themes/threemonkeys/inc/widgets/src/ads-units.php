<?php

/**
 * Widget API: WP_Widget_Archives class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement the Archives widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class WA_Widget_Adunit extends WP_Widget
{

	/**
	 * Sets up a new Archives widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct()
	{

		$widget_ops = array(
			'classname' 					=> 'wa-ad-unit',
			'description' 					=> 'Bloques de anuncios',
			'customize_selective_refresh' 	=> true,
		);
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('wa_adunit_widget', 'WA - Bloques de anuncios', $widget_ops, $control_ops);

		// Enqueue style if widget is active (appears in a sidebar) or if in Customizer preview.
		if (is_active_widget(false, false, $this->id_base) || is_customize_preview()) {

			//add_action( 'spiga_banners_slots', array( $this, 'widget_mamador' ) );
		}
	}

	/**
	 * Outputs the content for the current Archives widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Archives widget instance.
	 */
	public function widget($args, $instance)
	{

		$slot		= $instance['slot'];


		if ($slot) {

			echo $args['before_widget'];

			$_slot = wa_theme()->module('ads')->ad_slot($slot);

			if (is_array($_slot)) {
				wa_create_ad_slot($_slot);
			}

			echo $args['after_widget'];
		}
	}


	/**
	 * Handles updating settings for the current Archives widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget_Archives::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array(
			'slot'	=> null,
		));
		$instance['slot'] 	= $new_instance['slot'];

		return $instance;
	}

	/**
	 * Outputs the settings form for the Archives widget.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form($instance)
	{
		$instance = wp_parse_args((array) $instance, array(
			'slot' => null,
		));

		$options = wa_theme()->module('ads')->get_ad_slots_options();

?>

		<p>
			<label for="<?php echo $this->get_field_id('slot'); ?>">Bloque de anuncio:</label>

			<select name="<?php echo $this->get_field_name('slot'); ?>">

				<?php
				foreach ($options as $slot_id => $slot) {

				?>
					<option value="<?php echo $slot_id; ?>" <?php selected(esc_attr($instance['slot']), $slot_id); ?>><?php echo $slot; ?></option>
				<?php

				}
				?>

			</select>
		</p>

<?php
	}
}

function wa_adunit_register_widget()
{
	if (function_exists('wa_theme')) {
		if (!wa_theme()->modules()->is_active('ads')) return;
	}
	register_widget('WA_Widget_Adunit');
}
add_action('widgets_init', 'wa_adunit_register_widget');
