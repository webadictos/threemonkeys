<nav id="header" class="navbar navbar-expand-md <?php if (is_home() || is_front_page()) : echo ' home';
                                                endif; ?>">
    <div class="container">
        <div class="row w-100 g-0 justify-content-center">
            <div class="col-12 col-lg-6 d-flex justify-content-md-center">
                <a class="navbar-brand logo-navbar me-0" href="<?php echo esc_url(home_url()); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">


                    <?php
                    $header_logo = waGetLogo(); // Get custom meta-value.
                    ?>


                    <?php

                    $mainLogo = $header_logo;

                    ?>
                    <?php
                    if (!empty($mainLogo)) :
                    ?>
                        <img class="main-logo " src="<?php echo esc_url($mainLogo); ?>" alt="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" width="300" height="83" loading="eager" fetchpriority="high" />


                    <?php
                    else :
                        echo esc_attr(get_bloginfo('name', 'display'));

                    endif;
                    ?>


                </a>

                <button class="navbar-toggler ms-auto p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'foodandpleasure-theme'); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="col-12 col-lg-6">


                <div id="navbar" class="collapse navbar-collapse row justify-content-end g-0">
                    <?php
                    // Loading WordPress Custom Menu (theme_location).
                    wp_nav_menu(
                        array(
                            'menu_class'     => 'col-12 navbar-nav order-md-2 justify-content-md-center justify-content-lg-end',
                            'container'      => '',
                            'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
                            'walker'         => new WP_Bootstrap_Navwalker(),
                            'theme_location' => 'main-menu',
                        )
                    );
                    ?>

                    <div class="navbar-text navbar-widgets col-12 d-flex align-items-center justify-content-md-center justify-content-lg-end mb-3 gap-3 flex-column flex-md-row">
                        <div class="navbar-social-icons">
                            <?php
                            if (function_exists('wa_show_social_profiles')) {
                                wa_show_social_profiles();
                            }
                            ?>
                            <?php //my_social_media_icons(false, false, false); 
                            ?>
                        </div>

                        <?php
                        if ('1' === $search_enabled) :
                        ?>
                            <div class="navbar-searchform">
                                <form class="search-form my-2 my-lg-0" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
                                            </svg>
                                        </span>
                                        <input type="text" name="s" class="form-control" placeholder="<?php esc_attr_e('Search', 'foodandpleasure-theme'); ?>" title="<?php esc_attr_e('Search', 'foodandpleasure-theme'); ?>" />
                                    </div>


                                </form>
                            </div>
                        <?php
                        endif;
                        ?>
                    </div>




                </div><!-- /.navbar-collapse -->



            </div>
        </div>







    </div><!-- /.container -->
</nav><!-- /#header -->