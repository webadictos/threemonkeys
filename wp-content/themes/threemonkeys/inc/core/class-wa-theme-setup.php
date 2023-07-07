<?php

/**
 * WA_Theme_Setup
 */
if (!defined('ABSPATH')) exit;


class WA_Theme_Setup
{

    protected $loader;

    function __construct($loader)
    {
        $this->loader = $loader;

        /**
         * Se corrige el excerpt para darle prioridad a la descripción SEO
         */
        remove_filter('get_the_excerpt', 'wp_trim_excerpt'); //Remove the filter we don't want
        $this->loader->add_filter('get_the_excerpt', $this, 'get_excerpt', 10, 2);
        add_filter('the_excerpt', 'do_shortcode'); //Make sure shortcodes get processed

        /**
         * Se habilita la carga de archivos SVG
         */
        $this->loader->add_filter('wp_check_filetype_and_ext', $this, 'check_filetype', 10, 4);
        $this->loader->add_filter('upload_mimes', $this, 'cc_mime_types');
        $this->loader->add_action('admin_head', $this, 'fix_svg');


        /**
         * Se elimina la restricción de tener que incluir un email al registrar un usuario
         */
        // This will suppress empty email errors when submitting the user form
        $this->loader->add_action('user_profile_update_errors', $this, 'remove_empty_email_error', 10, 3);
        $this->loader->add_action('show_user_profile', $this, 'remove_required_email_validation', 10, 1);
        $this->loader->add_action('edit_user_profile', $this, 'remove_required_email_validation', 10, 1);
        $this->loader->add_action('user_new_form', $this, 'remove_required_email_validation', 10, 1);


        /**
         * Elimina por completo los comentarios
         */
        $this->disable_comments_behavior();

        $this->loader->add_filter('wa_theme_setup_script_current_canal', $this, 'get_current_canal');
        $this->loader->add_filter('wa_theme_setup_script_current_tags', $this, 'get_current_tags');

        $this->loader->add_action('wp_head', $this, 'add_setup_script_to_head');
    }


    public function disable_comments_behavior()
    {
        add_action('admin_init', function () {
            // Redirect any user trying to access comments page
            global $pagenow;

            if ($pagenow === 'edit-comments.php') {
                wp_safe_redirect(admin_url());
                exit;
            }

            // Remove comments metabox from dashboard
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

            // Disable support for comments and trackbacks in post types
            foreach (get_post_types() as $post_type) {
                if (post_type_supports($post_type, 'comments')) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
        });

        // Close comments on the front-end
        add_filter('comments_open', '__return_false', 20, 2);
        add_filter('pings_open', '__return_false', 20, 2);

        // Hide existing comments
        add_filter('comments_array', '__return_empty_array', 10, 2);

        // Remove comments page in menu
        add_action('admin_menu', function () {
            remove_menu_page('edit-comments.php');
        });

        // Remove comments links from admin bar
        add_action('init', function () {
            if (is_admin_bar_showing()) {
                remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
            }
        });
    }


    public function get_excerpt($text = '', $post)
    {


        $seodesc = trim(get_post_meta($post->ID, '_yoast_wpseo_metadesc', true));
        $excerpt = trim($post->post_excerpt);
        $theexcerpt = "";

        if ($seodesc != "") {
            $theexcerpt = $seodesc;
            return $seodesc;
        } else if ($excerpt) {
            $theexcerpt = $excerpt;
            return $excerpt;
        }


        if ('' == $theexcerpt) {
            $text = get_the_content('');
            $text = apply_filters('the_content', $text);
            $text = str_replace(']]>', ']]&gt;', $text);
            $excerpt_length = apply_filters('excerpt_length', 40);
            $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
            $text = wp_trim_words($text, $excerpt_length, $excerpt_more);
        }
        return apply_filters('wp_trim_excerpt', $text, $post->post_content);
    }

    function check_filetype($data, $file, $filename, $mimes)
    {

        global $wp_version;
        if ($wp_version !== '4.7.1') {
            return $data;
        }

        $filetype = wp_check_filetype($filename, $mimes);

        return [
            'ext'             => $filetype['ext'],
            'type'            => $filetype['type'],
            'proper_filename' => $data['proper_filename']
        ];
    }

    public function cc_mime_types($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    public function fix_svg()
    {
        echo '<style type="text/css">
          .attachment-266x266, .thumbnail img {
               width: 100% !important;
               height: auto !important;
          }
          </style>';
    }

    public function remove_empty_email_error($errors, $update, $user)
    {
        // echo ("HOLA");
        // print_r($errors);
        $errors->remove('empty_email');
    }
    function remove_required_email_validation($form_type)
    {
        // print_r($form_type);

?>
        <script type="text/javascript">
            jQuery('#email').closest('tr').removeClass('form-required').find('.description').remove();
            // Uncheck send new user email option by default
            <?php if (isset($form_type) && $form_type === 'add-new-user') : ?>
                jQuery('#send_user_notification').removeAttr('checked');
            <?php endif; ?>
        </script>
<?php
    }

    public function get_current_page_setup()
    {
        $current_object = get_queried_object();

        $current_page = array();



        if (is_single()) {
            $current_page['is_single'] = 'true';
            $current_page['post_type'] = get_post_type(get_the_ID());
        }

        if (is_page() || is_single() && !is_front_page()) {
            $current_page['is_singular'] = 'true';
            $current_page['postID'] = (string) get_the_ID();
        }

        if (is_archive() || is_category()) {
            $current_page['is_archive'] = 'true';
            $current_page['canal'] = $current_object->slug ?? '';
            $current_page['taxonomy'] = $current_object->taxonomy ?? '';
        }

        $current_page['tags'] = apply_filters('wa_theme_setup_script_current_tags', null);
        $current_page['canal'] = apply_filters('wa_theme_setup_script_current_canal', null);

        return apply_filters('wa_theme_setup_script_current', $current_page);
    }

    public function get_current_canal($canal)
    {

        $postID = get_the_ID();
        $canalTmp = array();

        if (is_single()) {
            $canales = get_the_category($postID);
            foreach ($canales as $c) {
                $canalTmp[] = $c->slug;
            }
        }

        if (is_category()) {
            $canales = get_term_parents_list(get_query_var('cat'), 'category', array('format' => 'slug', 'link' => false, 'separator' => ''));
            if (!is_string($canales)) $canales = "";
            $canalesTmp = explode("/", $canales);

            if (is_array($canalesTmp)) {
                foreach ($canalesTmp as $c) {
                    if (trim($c) != "") {
                        // $c = preg_replace('/[^A-Za-zÀ-ú0-9 ]/', '', $c);
                        $canalTmp[] = $c;
                    }
                }
            }
        }

        if (count($canalTmp) > 0) $canal = $canalTmp;


        return $canal;
    }

    public function get_current_tags($tags)
    {

        $postID = get_the_ID();
        $tagsTmp = array();

        if (is_single()) {
            $post_tags = get_the_tags($postID);

            $tagsTmp = array();

            if ($post_tags) {
                foreach ($post_tags as $t) {
                    $tagsTmp[] = strtolower($t->slug);
                }
            }
        }

        if (is_tag()) {
            $current_object = get_queried_object();
            $tagsTmp[] = $current_object->slug ?? '';
        }

        if (count($tagsTmp) > 0) $tags = $tagsTmp;


        return $tags;
    }

    public function add_setup_script_to_head()
    {


        $postSetup = apply_filters('wa_theme_setup_script', array(
            'current' => $this->get_current_page_setup(),
            'activeID' => is_singular() ? get_the_ID() : "0",
            'currentID' => is_singular() ? get_the_ID() : "0",
            'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
            'themeUri' => get_template_directory_uri(),
        ));




        echo "<script type='text/javascript'>\n";
        echo "/* <![CDATA[ */\n";
        echo "const WA_ThemeSetup =" . (json_encode($postSetup)) . ";\n";
        echo "window.WA_ThemeSetup = WA_ThemeSetup;\n";
        echo "window.ThemeSetup = WA_ThemeSetup;\n";
        echo "/* ]]> */\n";
        echo "</script>\n";
    }


    public function add_setup_script_to_footer()
    {


        $postSetup = apply_filters('wa_theme_setup_script_footer', array());




        echo "<script type='text/javascript'>\n";
        echo "/* <![CDATA[ */\n";
        echo "const ThemeSetupTres =" . (json_encode($postSetup)) . ";\n";
        echo "window.ThemeSetupTres = ThemeSetupTres;\n";
        echo "/* ]]> */\n";
        echo "</script>\n";
    }
}
