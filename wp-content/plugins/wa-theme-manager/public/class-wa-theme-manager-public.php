<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://webadictos.com
 * @since      1.0.0
 *
 * @package    Wa_Theme_Manager
 * @subpackage Wa_Theme_Manager/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wa_Theme_Manager
 * @subpackage Wa_Theme_Manager/public
 * @author     Daniel Medina <admin@webadictos.com.mx>
 */
class Wa_Theme_Manager_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Prefix for options
	 */

	protected $prefix;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->prefix = "wa_theme_options_";
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wa_Theme_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wa_Theme_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wa-theme-manager-public.css', array(), $this->version, 'all');
		$this->init_globals();
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wa_Theme_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wa_Theme_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//	wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wa-theme-manager-public.js', array('jquery'), $this->version, false);
	}

	public function init_globals()
	{
		if (!is_admin()) {
			$GLOBALS['promoted'] = Wa_Theme_Manager::get_opciones($this->prefix . 'promote', $this->prefix . 'posts_promoted'); //cmb2_get_option('webcammx_portada','webcammx_opciones_portada',false);
		}
	}

	public function init_layout_settings()
	{
		if (!is_admin()) {

			$themeSetup = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_theme_setup');



			$cssVars = array();

			$bgType = get_post_meta(get_the_ID(), 'wa_post_bg_type', true);
			switch ($bgType) {
				case "custom":
					$cssVars['section-background-image'] = "url('" . get_post_meta(get_the_ID(), 'wa_post_page_bg', true) . "')";
					break;
				case "none":
					$cssVars['section-background-image'] = "none";
					break;
			}
			$cssVars['page-background'] = get_post_meta(get_the_ID(), 'wa_post_page_bg_color', true);
			$cssVars['page-entry-background'] = get_post_meta(get_the_ID(), 'wa_post_entry_bg_color', true);
			$cssVars['page-layout-padding'] = get_post_meta(get_the_ID(), 'wa_post_page-layout-padding', true);

			echo "<style>";
			echo ":root{";
			foreach ($cssVars as $key => $v) {
				if (trim($v) !== "") {
					echo "--" . $key . ":" . $v . ";";
				}
			}
			echo "}";
			echo "</style>";
		}
	}
}
