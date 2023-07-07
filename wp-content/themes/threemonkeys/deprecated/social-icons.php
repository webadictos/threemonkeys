<?php

/**
 * Social Icons Menú
 */

function my_customizer_social_media_array()
{

    /* store social site names in array */
    $social_sites = array('facebook', 'instagram', 'twitter',  'pinterest', 'youtube', 'linkedin', 'tiktok', 'spotify', 'email');

    return $social_sites;
}

/* add settings to create various social media text areas. */
add_action('customize_register', 'my_add_social_sites_customizer');

function my_add_social_sites_customizer($wp_customize)
{

    $wp_customize->add_section('my_social_settings', array(
        'title'    => __('Social Media Icons', 'bushmills-theme'),
        'priority' => 1000,
    ));

    $social_sites = my_customizer_social_media_array();
    $priority = 5;

    foreach ($social_sites as $social_site) {

        $wp_customize->add_setting("$social_site", array(
            'type'              => 'theme_mod',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_url_raw'
        ));

        $wp_customize->add_control($social_site, array(
            'label'    => __("$social_site url:", 'bushmills-theme'),
            'section'  => 'my_social_settings',
            'type'     => 'text',
            'priority' => $priority,
        ));

        $priority = $priority + 5;
    }
}

/* takes user input from the customizer and outputs linked social media icons */
function my_social_media_icons($inverse = true, $stack = true, $search = false)
{

    $social_sites = my_customizer_social_media_array();

    /* any inputs that aren't empty are stored in $active_sites array */
    foreach ($social_sites as $social_site) {
        if (strlen(get_theme_mod($social_site)) > 0) {
            $active_sites[] = $social_site;
        }
    }

    /* for each active social site, add it as a list item */
    if (!empty($active_sites)) {

        echo "<ul class='social-media-icons'>";

        foreach ($active_sites as $active_site) {

            /* setup the class */

            $class = 'fab fa-' . $active_site;
            if ($active_site == "facebook") $class .= "-f";
            if ($active_site == 'email') {
?>
                <li>
                    <a class="email" target="_blank" href="mailto:<?php echo is_email(get_theme_mod($active_site)); ?>" rel="noopener noreferrer">

                        <?php if ($stack) : ?>
                            <span class="fa-stack fa-2x">
                                <i class="fa fa-circle fa-stack-2x"></i>

                            <?php endif; ?>
                            <i class="fa fa-envelope <?php if ($stack) : ?>fa-stack-1x <?php endif; ?> fa-social-icon <?php echo ($inverse) ? 'fa-inverse' : ''; ?>" title="<?php _e('email icon', 'guia-gastronomica'); ?>"></i>

                            <?php if ($stack) : ?>

                            </span>
                        <?php endif; ?>

                    </a>
                </li>
            <?php } else { ?>
                <li>

                    <a class="social-icon-<?php echo $active_site; ?>" target="_blank" href="<?php echo esc_url(get_theme_mod($active_site)); ?>" rel="noopener noreferrer">
                        <?php if ($stack) : ?>
                            <span class="fa-stack fa-2x">
                                <i class="fa fa-circle fa-stack-2x"></i>

                            <?php endif; ?>

                            <i class="<?php echo esc_attr($class); ?> fa-social-icon <?php echo ($inverse) ? 'fa-inverse' : ''; ?> <?php if ($stack) : ?>fa-stack-1x <?php endif; ?>" title="<?php printf(__('Ir a %s', 'guia-gastronomica'), $active_site); ?>"></i>
                            <?php if ($stack) : ?>

                            </span>
                        <?php endif; ?>

                    </a>
                </li>
            <?php
            }
        }

        if ($search) {
            ?>
            <li>

                <a class="social-icon-search" target="_blank" href="#search" title="Buscar">
                    <?php if ($stack) : ?>
                        <span class="fa-stack fa-2x">
                            <i class="fa fa-circle fa-stack-2x"></i>

                        <?php endif; ?> <i class="fas fa-search <?php echo ($inverse) ? 'fa-inverse' : ''; ?> <?php if ($stack) : ?>fa-stack-1x <?php endif; ?>"></i>
                        <?php if ($stack) : ?>

                        </span>
                    <?php endif; ?>
                </a>
            </li>
    <?php
        }

        echo "</ul>";
    }
}


function waShowSharebar($postID, $class = "")
{

    ?>
    <ul class="social-sharebar <?php echo $class; ?>">
        <li><a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(get_permalink($postID)); ?>" target="_blank" class="fb share-link" title="<?php echo __('¡Compartir en Facebook!', 'guia-gastronomica'); ?>" rel="noopener noreferrer nofollow"><i class="fab fa-facebook-f"></i></a></li>
        <li><a href="https://twitter.com/share?url=<?php echo urlencode(get_permalink($postID)); ?>&text= <?php echo get_the_title($postID); ?> " target="_blank" class="tw share-link" rel="noopener noreferrer nofollow" title="<?php echo __('¡Compartir en Twitter!', 'guia-gastronomica'); ?>"><i class="fab fa-twitter"></i></a></li>
        <li><a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink($postID)); ?>" target="_blank" class="linkedin share-link" rel="noopener noreferrer nofollow" title="<?php echo __('¡Compartir en LinkedIn!', 'guia-gastronomica'); ?>"><i class="fab fa-linkedin-in"></i></a></li>
        <li><a href="https://api.whatsapp.com/send?text=<?php echo urlencode(get_permalink($postID)); ?>" data-action="share/whatsapp/share" target="_blank" class="whatsapp share-link" rel="noopener noreferrer nofollow" title="<?php echo __('¡Compartir en WhatsApp!', 'guia-gastronomica'); ?>"><i class="fab fa-whatsapp"></i></a></li>
    </ul>

<?php
}
