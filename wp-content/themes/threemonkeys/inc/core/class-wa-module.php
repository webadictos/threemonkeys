<?php

/**
 * 
 */

abstract class WA_Module
{

    protected $active;
    protected $settings;
    public $loader;
    protected $module_config;
    protected $name;
    protected $filter_name;
    public $modules_loader;

    public function __construct($name, $settings = array(), $loader = null)
    {
        $this->name = $name;
        $this->settings = $settings;
        $this->loader = $loader;
        // $this->init();
        // $this->load_config();
        $this->filter_name = $this->clean_name();
        $this->settings['show_in_front'] = $this->settings['show_in_front'] ?? true;
    }

    abstract public function init();

    public function run()
    {
        $this->init();
        $this->load_config();
        $this->after_load_config();
    }

    public function after_load_config()
    {
    }
    public function load_config()
    {
    }

    public function is_active_dependency($module)
    {
        return $this->modules_loader->is_active($module);
    }

    public function get_config()
    {
        return apply_filters("wa_{$this->filter_name}_settings", $this->module_config);
    }

    public function config($config)
    {
        return $this->module_config[$config] ?? null;
    }

    public function clean_name()
    {
        return str_replace("-", "_", $this->name);
    }

    public function setting($setting)
    {
        return $this->settings[$setting] ?? null;
    }

    public function get_front_settings($settings)
    {
        return apply_filters("wa_{$this->filter_name}_front_settings", $settings);
    }
}
