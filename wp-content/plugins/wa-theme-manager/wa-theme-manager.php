<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://webadictos.com
 * @since             1.0.0
 * @package           Wa_Theme_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       WA Theme Manager
 * Plugin URI:        https://webadictos.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Daniel Medina
 * Author URI:        https://webadictos.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wa-theme-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WA_THEME_MANAGER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wa-theme-manager-activator.php
 */
function activate_wa_theme_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wa-theme-manager-activator.php';
	Wa_Theme_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wa-theme-manager-deactivator.php
 */
function deactivate_wa_theme_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wa-theme-manager-deactivator.php';
	Wa_Theme_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wa_theme_manager' );
register_deactivation_hook( __FILE__, 'deactivate_wa_theme_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wa-theme-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wa_theme_manager() {

	$plugin = new Wa_Theme_Manager();
	$plugin->run();

}
run_wa_theme_manager();
