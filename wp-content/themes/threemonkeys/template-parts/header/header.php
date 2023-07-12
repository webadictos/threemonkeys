<?php
$navbar_scheme   = get_theme_mod('navbar_scheme', 'navbar-light bg-light'); // Get custom meta-value.
$navbar_position = get_theme_mod('navbar_position', 'static'); // Get custom meta-value.
$search_enabled  = get_theme_mod('search_enabled', '1'); // Get custom meta-value.
?>

<?php
$logo = wa_theme()->setting('general', 'logo') ?? '';
$logo_dark = wa_theme()->setting('general', 'logo_dark') ?? '';
?>
<header id="masthead" class="masthead sticky-top <?php echo (is_home() || is_front_page()) ? 'header-home' : ''; ?>">

    <div id="header" class="header-container2">



        <nav id="header" class="navbar navbar-expand-md <?php if (is_home() || is_front_page()) : echo ' home';
                                                        endif; ?>">
            <div class="container">
                <div class="row w-100 g-0 justify-content-center">
                    <div class="col-12 col-lg-12 d-flex justify-content-md-center navbar-container">
                        <a class="navbar-brand logo-navbar me-0" href="<?php echo esc_url(home_url()); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">


                            <?php
                            if (!empty($logo)) :
                            ?>
                                <img class="main-logo" src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" width="460" height="50" loading="eager" fetchpriority="high" />
                            <?php
                            else :
                                echo esc_attr(get_bloginfo('name', 'display'));
                            endif;
                            ?>


                        </a>

                        <button class="navbar-toggler ms-auto p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'venture-theme'); ?>">

                            <svg viewBox="0 0 100 50" width="35" height="25" class="toggler-container">
                                <rect width="100" height="5" fill="#FFF" class="toggler-rect"></rect>
                                <rect y="25" width="100" height="5" fill="#FFF" class="toggler-rect"></rect>
                                <rect y="50" width="100" height="5" fill="#FFF" class="toggler-rect"></rect>
                            </svg>


                        </button>
                    </div>
                    <div class="col-12 col-lg-12">


                        <div id="navbar" class="collapse navbar-collapse row justify-content-center g-0">
                            <?php
                            // Loading WordPress Custom Menu (theme_location).
                            wp_nav_menu(
                                array(
                                    'menu_class'     => 'col-12 col-md-11 navbar-nav justify-content-md-center justify-content-lg-center',
                                    'container'      => '',
                                    'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
                                    'walker'         => new WP_Bootstrap_Navwalker(),
                                    'theme_location' => 'main-menu',
                                )
                            );
                            ?>

                            <div class="header__icon col-12 col-md-auto">

                                <button type="button" data-bs-toggle="collapse" data-bs-target="#collapse-search" aria-expanded="false" aria-controls="collapse-search" title="<?php echo __('Search', 'wa-theme'); ?>" class="btn search-toggler">

                                    <svg class="search-icon" fill="#FFF" width="17" height="17" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M790.588 1468.235c-373.722 0-677.647-303.924-677.647-677.647 0-373.722 303.925-677.647 677.647-677.647 373.723 0 677.647 303.925 677.647 677.647 0 373.723-303.924 677.647-677.647 677.647Zm596.781-160.715c120.396-138.692 193.807-319.285 193.807-516.932C1581.176 354.748 1226.428 0 790.588 0S0 354.748 0 790.588s354.748 790.588 790.588 790.588c197.647 0 378.24-73.411 516.932-193.807l516.028 516.142 79.963-79.963-516.142-516.028Z" fill-rule="evenodd" />
                                    </svg>

                                </button>

                            </div>

                        </div><!-- /.navbar-collapse -->



                    </div>
                </div>







            </div><!-- /.container -->
        </nav><!-- /#header -->







    </div>

    <div class="collapse collapse-search container-fluid" id="collapse-search">
        <div class="collapse-search__container">
            <form id="collapse-search__form" class="collapse-search__form" action="/" accept-charset="utf-8">
                <input id="search" name="s" value="" class="form-control" type="search" data-swplive="true" dir="ltr" spellcheck="false" autocorrect="off" autocomplete="off" autocapitalize="off" maxlength="2048" tabindex="0" placeholder="Escribe lo que deseas buscar..." aria-label="Escribe lo que deseas buscar" aria-describedby="search-form-icon">
                <a class="collapse-search__close" href="#" data-bs-toggle="collapse" data-bs-target="#collapse-search.show">Cerrar</a>
            </form>
        </div>
    </div>

</header>