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
 * @package    Wa_Theme_Manager_Portada
 * @subpackage Wa_Theme_Manager/includes
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

/**
 * PortadaInStyle.
 *
 * @package PortadaInStyle
 * @author  Daniel Medina <dmedina@forbes.com.mx>
 */
class Wa_Theme_Manager_Portada
{

    /**
     * Plugin version.
     *
     * @var string
     */
    const VERSION = '1.0';

    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static $instance = null;
    public $tipos = array('post');
    public $transientSectionNames = array(
        "wa_theme_options_breaking"=>'instyle_section_breaking',
        "wa_theme_options_lo_ultimo"=>"instyle_section_lo_ultimo",
        "wa_theme_options_must_read"=>"instyle_section_mustread",
    );
    public $optionsPortada = array(
        "wa_theme_options_posts_breaking" => "wa_theme_options_breaking"
    );




    public $purge = 0;

    /**
     * Initialize the plugin.
     */
    function __construct()
    {


        add_action('save_post', array($this, 'guardar_portada'), 11, 2);
        add_action('transition_post_status', array($this, 'guardar_portada'), 10, 2);
        add_action('publish_future_post', array($this, 'publishPost'), 11, 3);
        add_action('updated_option', array($this, 'checkUpdatedOptions'));
        add_action('added_option',   array($this, 'checkUpdatedOptions'));
        add_action('deleted_option', array($this, 'checkUpdatedOptions'));
        add_action('redis_object_cache_set', array($this, 'redisObjectCacheDeleteAllOptions'), 10, 4);
    }

    /**
     * Return an instance of this class.
     *
     * @return object A single instance of this class.
     */
    public static function get_instance()
    {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }



    public function checkUpdatedOptions($option)
    {
        $transients = array();


        if (wp_installing() === FALSE) {
            $alloptions = wp_load_alloptions(); // alloptions should be cached at this point

            // If option is part of the alloptions collection then clear it.
            if (array_key_exists($option, $alloptions)) {
                wp_cache_delete($option, 'options');
                //wp_cache_delete( 'alloptions', 'options' );
            }
        }

        if ($option == "wa_theme_options") {
            error_log("deleted from cache group 'options': $option");
            wp_cache_delete('alloptions', 'options');

            //$this->refreshTransients($transients);
            $this->purge = 1;

            $this->purgarHome();
        }
    }

    function redisObjectCacheDeleteAllOptions($key, $value, $group, $expiration)
    {
        if ('alloptions' == $key && 'options' == $group) {
            wp_cache_delete('alloptions', 'options');
        }
    }

    protected function purgarHome()
    {
        global $nginx_purger, $rt_wp_nginx_purger;

        if (is_object($nginx_purger)) {
            if ($this->purge) {
                $homepage_url = trailingslashit(home_url());
                $nginx_purger->purge_url($homepage_url);
            }
        } else if (is_object($rt_wp_nginx_purger)) {
            if ($this->purge) {
                $homepage_url = trailingslashit(home_url());
                $rt_wp_nginx_purger->purgeUrl($homepage_url);
            }
        }
    }

    protected function refreshTransients($transients)
    {

        if (is_array($transients) && count($transients) > 0) {
            foreach ($transients as $transientName) {

                delete_transient($transientName);
                error_log("Se refresco el transient:" . $transientName);
            }
        }
    }

    protected function verificarPost($post, $updated = false)
    {

        $showbreaking = get_post_meta( $post->ID, 'wa_post_show_breaking', true);
        $showLoUltimo = get_post_meta( $post->ID, 'wa_post_show_lo_ultimo', true);
        $showmustread = get_post_meta( $post->ID, 'wa_post_show_mustread', true);
        $showdestacadas = get_post_meta( $post->ID, 'wa_post_show_destacadas', true);

        if ($showbreaking == "on") {
            $breaking = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_breaking');

            if (!in_array($post->ID, $breaking)) {
                array_unshift($breaking, $post->ID);
                if (count($breaking) > 4) {
                    $breakingPop = array_pop($breaking);
                }
                cmb2_update_option("wa_theme_options", 'wa_theme_options_breaking', $breaking, true);
            }
        }

        if($showLoUltimo=="on"){
            $loUltimo = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_lo_ultimo');
            $loUltimoOrder = get_post_meta( $post->ID, 'wa_post_orden_lo_ultimo', true);


            if(!in_array($post->ID,$loUltimo)){

                $loUltimo[$loUltimoOrder-1] = $post->ID;

               cmb2_update_option( "wa_theme_options", 'wa_theme_options_lo_ultimo', $loUltimo, true );
            }              

        }

        if($showmustread=="on"){
            $mustread = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_must_read');
            $mustreadOrder = get_post_meta( $post->ID, 'wa_post_orden_mustread', true);


            if(!in_array($post->ID,$mustread)){

                $mustread[$mustreadOrder-1] = $post->ID;

               cmb2_update_option( "wa_theme_options", 'wa_theme_options_must_read', $mustread, true );
            }              

        }


        if($showdestacadas=="on"){
            $destacadas = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_destacadas');
            $destacadasdOrder = get_post_meta( $post->ID, 'wa_post_orden_destacadas', true);


            if(!in_array($post->ID,$destacadas)){

                $destacadas[$destacadasdOrder-1] = $post->ID;

               cmb2_update_option( "wa_theme_options", 'wa_theme_options_destacadas', $destacadas, true );
            }              

        }

 
    }



    protected function isInPortada($post)
    {
        $breaking = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_breaking');
        $loUltimo = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_lo_ultimo');
        $mustRead = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_must_read');
        $editorsPick = Wa_Theme_Manager::get_opciones('wa_theme_options', 'wa_theme_options_editors_pick');

        if (is_array($breaking) && in_array($post->ID, $breaking)) {
            return true;
        }
        if (is_array($loUltimo) && in_array($post->ID, $loUltimo)) {
            return true;
        }
        if (is_array($mustRead) && in_array($post->ID, $mustRead)) {
            return true;
        }
        if (is_array($editorsPick) && in_array($post->ID, $editorsPick)) {
            return true;
        }


        return false;
    }


    public function publishPost($post_ID)
    {
        //error_log("Publicando Post Programado");

        $post = get_post($post_ID);
        $updated = false;
        //error_log("Verificar Post Programado");
        $this->verificarPost($post, $updated);

        $this->purgarHome();
    }


    /**
     * Nofity users when publish a post.
     *
     * @param  string  $new_status New status of post
     * @param  string  $old_status Old status of post.
     * @param  WP_Post $post       Post data.
     *
     * @return void
     */
    public function guardar_portada($post_ID, $post)
    {

        if ('publish' != get_post_status($post_ID)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        // AJAX? Not used here
        //if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
        //        return;
        // Check user permissions
        // if ( ! current_user_can( 'edit_post', $post_ID ) )
        //         return;
        // Return if it's a post revision
        if (false !== wp_is_post_revision($post_ID))
            return;

        if (!$post) $post = get_post($post_ID);

        $updated = false;

        if ($post->post_modified_gmt != $post->post_date_gmt) {
            if (!$this->isInPortada($post)) {

                $this->purge = 1;

                $this->purgarHome();
                return;
            }

            $updated = true;
        }

        $transients = array();

        if ($post->post_type === "post") {
            $this->verificarPost($post, $updated);
        }


        $this->purgarHome();
    }
}
