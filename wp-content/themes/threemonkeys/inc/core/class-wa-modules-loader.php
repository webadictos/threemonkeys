<?php

/**
 * 
 */

class WA_Modules_Loader
{

    public $modules;
    public $helpers;
    protected $loader;
    protected $modules_path;
    protected $helpers_path;

    public function __construct($loader)
    {
        $this->modules = array();
        $this->loader = $loader;
        $this->modules_path = get_template_directory() . "/inc/modules";
        $this->helpers_path = get_template_directory() . "/inc/core/helpers";

        $this->load_helpers();
        $this->load();
    }

    public function load_helpers()
    {
        $helpers = is_array($GLOBALS['theme_helpers']) ? $GLOBALS['theme_helpers'] : array();
        // Crea una instancia de cada módulo activo
        foreach ($helpers as $helper) {
            $clase_modulo = "WA_" .  $this->get_class_name($helper);
            $file_modulo = $this->helpers_path . "/" . "class-wa-" . strtolower($helper) . ".php";
            // Incluye el archivo que contiene la definición de la clase del módulo


            if (is_readable($file_modulo)) {
                require_once $file_modulo;

                // Crea una instancia del módulo y la guarda en el arreglo de módulos
                $this->helpers[$helper] = new $clase_modulo(true, array(), $this->loader);
            }
        }
    }

    public function load()
    {

        $theme_modules = is_array($GLOBALS['theme_modules']) ? $GLOBALS['theme_modules'] : array();

        // Carga los módulos activos desde el archivo de configuración
        // $modulos_activos = get_option('modulos_activos');


        $active_modules = array();
        foreach ($theme_modules as $module => $options) {
            if ($options['active']) {
                $active_modules[] = $module;
            }
        }



        // Crea una instancia de cada módulo activo
        foreach ($active_modules as $nombre_modulo) {
            $clase_modulo = "WA_" .  $this->get_class_name($nombre_modulo) . '_Module';
            $file_modulo = $this->modules_path . "/" . $nombre_modulo . "/" . "class-wa-" . strtolower($nombre_modulo) . "-module.php";
            // Incluye el archivo que contiene la definición de la clase del módulo


            if (is_readable($file_modulo)) {
                require_once $file_modulo;

                // Crea una instancia del módulo y la guarda en el arreglo de módulos
                $this->modules[$nombre_modulo] = new $clase_modulo($nombre_modulo, $theme_modules[$nombre_modulo]['config'], $this->loader);
                $this->loader->add_module($nombre_modulo);
            }
        }
    }

    public function run()
    {
        foreach ($this->helpers as $helper) {
            $helper->run();
        }

        foreach ($this->modules as $module) {
            $module->run();
        }
    }

    public function get_class_name($module)
    {

        $module_words = explode("-", $module);
        $module_name = array();

        foreach ($module_words as $word) {
            $module_name[] = ucfirst($word);
        }

        return implode("_", $module_name);
    }

    public function is_active($module)
    {

        if (isset($this->modules[$module])) {
            return true;
        }

        return false;
    }

    public function all()
    {
        return $this->modules;
    }

    public function module($module)
    {
        if ($this->is_active($module)) {
            return $this->modules[$module];
        }
        return false;
    }

    public function helper($helper)
    {

        if (isset($this->helpers[$helper])) {
            return $this->helpers[$helper];
        }

        return false;
    }
}
