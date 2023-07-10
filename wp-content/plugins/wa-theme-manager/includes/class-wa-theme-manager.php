<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://webadictos.com
 * @since      1.0.0
 *
 * @package    Wa_Theme_Manager
 * @subpackage Wa_Theme_Manager/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wa_Theme_Manager
 * @subpackage Wa_Theme_Manager/includes
 * @author     Daniel Medina <admin@webadictos.com.mx>
 */
class Wa_Theme_Manager
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wa_Theme_Manager_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;




	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('WA_THEME_MANAGER_VERSION')) {
			$this->version = WA_THEME_MANAGER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wa-theme-manager';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wa_Theme_Manager_Loader. Orchestrates the hooks of the plugin.
	 * - Wa_Theme_Manager_i18n. Defines internationalization functionality.
	 * - Wa_Theme_Manager_Admin. Defines all hooks for the admin area.
	 * - Wa_Theme_Manager_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wa-theme-manager-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wa-theme-manager-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wa-theme-manager-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wa-theme-manager-public.php';


		/**
		 * Require CMB2 
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/CMB2/init.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cmb2-field-post-search-ajax/cmb-field-post-search-ajax.php';
		//require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cmb2-field-ajax-search/cmb2-field-ajax-search.php';

		//require_once plugin_dir_path(dirname(__FILE__)) . 'includes/CMB2/cmb-field-spread-post-search-ajax.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cmb2-tabs/cmb2-tabs.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/CMB2/cmb2-radio-image.php';
		//	require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cmb2-conditionals/cmb2-conditionals.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cmb2-conditional-logic/cmb2-conditional-logic.php';


		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cmb2-field-map/cmb-field-map.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cmb2-attached-posts/cmb2-attached-posts-field.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cmb2-social-field/cmb2-social-field.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cmb2-ad-slot-field/cmb2-ad-slot-field.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cmb2-parameters-field/cmb2-parameters-field.php';


		$this->loader = new Wa_Theme_Manager_Loader();

		/**
		 * Códigos
		 */
		//require_once plugin_dir_path(dirname(__FILE__)) . 'includes/modules/class-wa-theme-manager-codigos.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/modules/class-wa-theme-manager-codes.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wa_Theme_Manager_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Wa_Theme_Manager_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Wa_Theme_Manager_Admin($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('cmb2_admin_init', $plugin_admin, 'wa_theme_manager_settings_page');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('cmb2_admin_init', $plugin_admin, 'wa_register_metaboxes');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Wa_Theme_Manager_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('wp_head', $plugin_public, 'init_layout_settings');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wa_Theme_Manager_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}


	static function get_opciones($prefix = '', $key = '', $default = false)
	{
		if (function_exists('cmb2_get_option')) {
			// Use cmb2_get_option as it passes through some key filters.
			return cmb2_get_option($prefix, $key, $default);
		}

		// Fallback to get_option if CMB2 is not loaded yet.
		$opts = get_option($prefix, $default);

		$val = $default;

		if ('all' == $key) {
			$val = $opts;
		} elseif (is_array($opts) && array_key_exists($key, $opts) && false !== $opts[$key]) {
			$val = $opts[$key];
		}

		return $val;
	}

	static function accept_html_values_sanitize($original_value, $args, $cmb2_field)
	{
		return $original_value; // Unsanitized value.
	}

	static function convert_name_as_id($original_value, $args, $cmb2_field)
	{
		$delimiter = "_";

		$name_id = strtolower(str_replace(" ", $delimiter, $original_value));
		$name_id = preg_replace('/[^A-Za-z0-9_' . $delimiter . ']/', '', $name_id);

		return $name_id;
	}
	static function insertAdAfterParagraph($insertion, $paragraph_id, $content)
	{
		$closing_p = '</p>';
		$paragraphs = explode($closing_p, $content);
		foreach ($paragraphs as $index => $paragraph) {
			// Only add closing tag to non-empty paragraphs
			if (trim($paragraph)) {
				// Adding closing markup now, rather than at implode, means insertion
				// is outside of the paragraph markup, and not just inside of it.
				$paragraphs[$index] .= $closing_p;
			}

			// + 1 allows for considering the first paragraph as #1, not #0.
			if ($paragraph_id == $index + 1) {
				$paragraphs[$index] .=  $insertion;
			}
		}
		return implode('', $paragraphs);
	}
}
