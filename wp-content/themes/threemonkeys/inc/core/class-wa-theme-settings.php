<?php

class WA_Theme_Settings
{

    protected $settings = array();
    protected $loader;
    protected $modules;

    public function __construct($loader, $modules)
    {
        $this->loader = $loader;
        $this->modules = $modules;

        $this->loader->add_filter('wa_general_settings', $this, 'clean_general_settings', 10, 1);
        $this->loader->add_filter('wa_theme_setup_script', $this, 'theme_setup_script', 10, 1);
        $this->loader->add_filter('wa_theme_settings', $this, 'add_additional_settings', 11, 1);
        $this->loader->add_filter('wa_general_front_settings', $this, 'general_front_settings', 11, 1);

        // $this->init();
    }

    public function run()
    {
        $this->load_settings();
    }

    private function load_settings()
    {
        $general_options = array(
            'default_image' => get_template_directory_uri() . '/assets/images/default.png',
            'logo' => '',
            'logo_navbar' => '',
            'logo_footer' => '',
            'logo_dark' => '',
            'refreshPage' => true,
            'refreshTime' => '60',
            'enableDMP' => true,
        );

        $_general_options = array();

        if (class_exists('Wa_Theme_Manager')) {
            $theme_general_options = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_theme_setup');

            if ($theme_general_options)
                $_general_options = apply_filters('wa_general_settings', $theme_general_options[0]);
        }

        $this->settings['general'] = array_merge($general_options, $_general_options);

        $active_modules = $this->modules->all();

        foreach ($active_modules as $module_name => $module) {

            if ($module->get_config())
                $this->settings[$module_name] = $module->get_config();
        }
    }

    public function get($module = "")
    {
        if ($module !== "" && isset($this->settings[$module])) {
            $this->settings[$module] = apply_filters("wa_{$module}_settings", $this->settings[$module]);

            return $this->settings[$module];
        } else {
            $this->settings = apply_filters('wa_theme_settings', $this->settings);
        }

        return $this->settings;
    }

    public function setting($module, $setting)
    {
        return $this->settings[$module][$setting] ?? null;
    }

    public function add($group, $_settings)
    {

        if (trim($group) !== "" && is_array($_settings)) {
            $this->settings[$group] = $_settings;
        }
    }

    public function clean_general_settings($_settings)
    {

        if (class_exists('Jetpack_Options')) {
            $_settings['jetpackID'] = Jetpack_Options::get_option('id');
            $_settings['jetpackApiVersion'] = JETPACK__API_VERSION;
            $_settings['jetpackVersion'] = JETPACK__VERSION;
        }
        $_settings['refreshPage'] = filter_var($_settings['refreshPage'], FILTER_VALIDATE_BOOLEAN) ?? false;
        $_settings['enableDMP'] = filter_var($_settings['enableDMP'], FILTER_VALIDATE_BOOLEAN) ?? false;


        return $_settings;
    }

    public function add_additional_settings($_settings)
    {

        if (class_exists('Jetpack_Options')) {
            $_settings['generales']['jetpackID'] = Jetpack_Options::get_option('id');
            $_settings['generales']['jetpackApiVersion'] = JETPACK__API_VERSION;
            $_settings['generales']['jetpackVersion'] = JETPACK__VERSION;
        }

        return $_settings;
    }

    public function general_front_settings($_general_settings)
    {

        /*
        $general_options = array(
            'default_image' => get_template_directory_uri() . '/assets/images/default.png',
            'logo' => '',
            'logo_navbar' => '',
            'logo_footer' => '',
            'logo_dark' => '',
            'refreshPage' => true,
            'refreshTime' => '60',
            'enableDMP' => true,
        );
*/
        unset($_general_settings['default_image']);
        unset($_general_settings['logo']);
        unset($_general_settings['logo_navbar']);
        unset($_general_settings['logo_footer']);
        unset($_general_settings['logo_dark']);
        unset($_general_settings['logo_id']);
        unset($_general_settings['logo_navbar_id']);
        unset($_general_settings['logo_footer_id']);
        unset($_general_settings['logo_dark_id']);


        if (is_user_logged_in() || !$_general_settings['refreshPage']) {
            $_general_settings['refreshPage'] = false;
            unset($_general_settings['refreshTime']);
        }

        return $_general_settings;
    }

    public function theme_setup_script($setup)
    {

        $setup['general'] = apply_filters("wa_general_front_settings", $this->get('general'));

        foreach (array_keys($this->settings) as $module_name) {

            if ($this->modules->module($module_name) && $this->modules->module($module_name)->setting('show_in_front')) {
                $module_settings = $this->modules->module($module_name)->get_front_settings($this->get($module_name));
                $module_json_name = str_replace("-", "_", $module_name);
                $setup[$module_json_name] = $module_settings;
            }
        }



        return $setup;
    }
}
