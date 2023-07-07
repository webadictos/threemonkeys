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

    <div id="header" class="header-container">

        <div class="header__toggler">

            <button href="#menuoffcanvas" role="button" data-bs-toggle="offcanvas" data-bs-target="#menuoffcanvas" aria-controls="menuoffcanvas" title="Menú" class="btn hamburguer-toggler">

                <svg viewBox="0 0 100 50" width="35" height="25" class="toggler-container">
                    <rect width="100" height="5" fill="#000000" class="toggler-rect"></rect>
                    <rect y="25" width="100" height="5" fill="#000000" class="toggler-rect"></rect>
                    <rect y="50" width="100" height="5" fill="#000000" class="toggler-rect"></rect>
                </svg>

            </button>

        </div>


        <nav class="header__menu-left navbar navbar-expand">

            <?php
            // Loading WordPress Custom Menu (theme_location).
            wp_nav_menu(
                array(
                    'menu_class'     => 'navbar-nav',
                    'container'      => '',
                    'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
                    'walker'         => new WP_Bootstrap_Navwalker(),
                    'theme_location' => 'main-menu-left',
                )
            );
            ?>

        </nav>
        <div class="header__logo">
            <a class="navbar-brand logo-navbar" href="<?php echo esc_url(home_url()); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">

                <?php
                if (!empty($logo) && !empty($logo_dark)) :
                ?>
                    <img class="main-logo d-none d-xl-inline-block" src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" width="271" height="46" loading="eager" fetchpriority="high" />
                    <img class="main-logo main-logo__dark d-xl-none" src="<?php echo esc_url($logo_dark); ?>" alt="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" width="271" height="46" loading="eager" fetchpriority="high" />

                <?php
                else :
                    echo esc_attr(get_bloginfo('name', 'display'));
                endif;
                ?>
            </a>
        </div>
        <nav class="header__menu-right navbar navbar-expand">
            <?php
            // Loading WordPress Custom Menu (theme_location).
            wp_nav_menu(
                array(
                    'menu_class'     => 'navbar-nav',
                    'container'      => '',
                    'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
                    'walker'         => new WP_Bootstrap_Navwalker(),
                    'theme_location' => 'main-menu-right',
                )
            );
            ?>
        </nav>
        <div class="header__icon">

            <button type="button" data-bs-toggle="collapse" data-bs-target="#collapse-search" aria-expanded="false" aria-controls="collapse-search" title="<?php echo __('Search', 'wa-theme'); ?>" class="btn search-toggler">

                <svg class="search-icon" fill="#000000" width="17" height="17" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                    <path d="M790.588 1468.235c-373.722 0-677.647-303.924-677.647-677.647 0-373.722 303.925-677.647 677.647-677.647 373.723 0 677.647 303.925 677.647 677.647 0 373.723-303.924 677.647-677.647 677.647Zm596.781-160.715c120.396-138.692 193.807-319.285 193.807-516.932C1581.176 354.748 1226.428 0 790.588 0S0 354.748 0 790.588s354.748 790.588 790.588 790.588c197.647 0 378.24-73.411 516.932-193.807l516.028 516.142 79.963-79.963-516.142-516.028Z" fill-rule="evenodd" />
                </svg>

            </button>

        </div>





    </div>

    <div class="collapse collapse-search container-fluid" id="collapse-search">
        <div class="collapse-search__container">
            <form id="collapse-search__form" class="collapse-search__form" action="/" accept-charset="utf-8">
                <input id="search" name="s" value="" class="form-control" type="search" data-swplive="true" dir="ltr" spellcheck="false" autocorrect="off" autocomplete="off" autocapitalize="off" maxlength="2048" tabindex="0" placeholder="BUSCAR: Los mejores..." aria-label="Escribe lo que deseas buscar" aria-describedby="search-form-icon">
                <a class="collapse-search__close" href="#" data-bs-toggle="collapse" data-bs-target="#collapse-search.show">Cerrar</a>
            </form>
        </div>
    </div>

</header>