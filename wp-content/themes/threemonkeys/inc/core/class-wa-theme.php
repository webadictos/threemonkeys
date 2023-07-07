<?php

/**
 * WA Theme
 * 
 * Clase principal del tema para inicializar aspectos y fucnionalidades necesarias
 */
if (!defined('ABSPATH')) exit;


class WA_Theme
{
    protected $version = "1.0";


    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      WA_Theme_Loader    $loader    Maintains and registers all hooks for the plugin.
     */

    protected $loader;

    public $modulos;

    protected $theme_config;


    function __construct()
    {
        $this->load_core();
    }

    public function load_core()
    {

        require_once get_template_directory() . '/inc/core/class-wa-theme-loader.php';
        require_once get_template_directory() . '/inc/core/class-wa-theme-setup.php';
        require_once get_template_directory() . '/inc/core/class-wa-module.php';
        require_once get_template_directory() . '/inc/core/class-wa-modules-loader.php';
        require_once get_template_directory() . '/inc/core/class-wa-theme-settings.php';


        $this->loader = new WA_Theme_Loader();

        new WA_Theme_Setup($this->loader);

        $this->modulos = new WA_Modules_Loader($this->loader);

        $this->theme_config = new WA_Theme_Settings($this->loader, $this->modulos);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->modulos->run();

        $this->loader->run();

        $this->theme_config->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    WA_Theme_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    public function modules()
    {
        return $this->modulos;
    }

    public function module($module)
    {
        return $this->modulos->module($module);
    }
    public function helper($helper)
    {
        return $this->modulos->helper($helper);
    }

    public function settings()
    {
        return $this->theme_config;
    }

    public function setting($module, $setting)
    {
        return $this->theme_config->setting($module, $setting);
    }
}

$wa_theme = new WA_Theme();

$GLOBALS['WA_Theme'] = $wa_theme;

$wa_theme->run();


function wa_theme()
{
    return $GLOBALS['WA_Theme'];
}
