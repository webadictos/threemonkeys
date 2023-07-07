<div class="offcanvas offcanvas-start hamburger-menu" tabindex="-1" id="menuoffcanvas" aria-labelledby="menuoffcanvasLabel">
    <div class="offcanvas-header">
        <?php
        $logo_dark = wa_theme()->setting('general', 'logo_dark') ?? '';

        if ($logo_dark !== "") :
        ?>
            <img class="hamburguer-menu__logo" src="<?php echo $logo_dark; ?>" width="403" height="68" alt="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" loading="lazy" fetchpriority="low">



        <?php endif; ?>
        <button type="button" class="btn-close close-hamburger" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body offcanvas-menu">





        <nav class="navbar h-100">

            <?php
            if (has_nav_menu('hamburger-menu')) :

                // Loading WordPress Custom Menu (theme_location).
                wp_nav_menu(
                    array(
                        'menu_class'     => 'navbar-nav',
                        'container'      => 'div',
                        'container_class' => 'menu-hamburger__main-items collapse show navbar-collapse justify-content-start',
                        'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
                        'walker'          => new WP_Bootstrap_Navwalker(),
                        'theme_location' => 'hamburger-menu',
                    )
                );
            // wp_nav_menu(array(
            //     'theme_location' => 'hamburguer-menu',
            //     'container'       => 'div',
            //     'container_id'    => 'main-menu-left-nav',
            //     'container_class' => 'menu-hamburguer__main-items collapse show navbar-collapse justify-content-start mb-auto',
            //     'menu_class'      => 'navbar-nav',
            //     'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
            //     'walker'          => new WP_Bootstrap4_Navwalker_Footer()
            // ));
            endif;
            ?>


            <div class="navbar-text w-100 py-5 hamburger-menu__social">
                <p class="hamburger-menu__heading">Síguenos</p>
                <?php
                if (function_exists('wa_show_social_profiles')) {
                    wa_show_social_profiles(array('exclude' => array('email')));
                }
                ?>
            </div>


        </nav>


    </div>
</div>

<div class="overlay-background"></div>