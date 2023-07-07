<?php

add_filter('web_app_manifest', function ($manifest) {
    $manifest['display'] = 'fullscreen';
    $manifest['icons'] = array_map(
        static function ($icon) {
            $icon['purpose'] = 'any maskable';
            return $icon;
        },
        $manifest['icons']
    );
    //$manifest['short_name'] = 'Venture & Pleasure';
    return $manifest;
});


add_action('wp_front_service_worker', function (\WP_Service_Worker_Scripts $scripts) {
    $scripts->caching_routes()->register(
        '/wp-content/cache/autoptimize/css/.*\.(?:css)(\?.*)?$',
        array(
            'strategy'  => WP_Service_Worker_Caching_Routes::STRATEGY_CACHE_FIRST,
            'cacheName' => 'autoptimize',
            'plugins'   => array(
                'expiration' => array(
                    'maxEntries'    => 60,
                    'maxAgeSeconds' => 60 * 60 * 24,
                ),
            ),
        )
    );
});

add_action('wp_front_service_worker', function (\WP_Service_Worker_Scripts $scripts) {
    $scripts->caching_routes()->register(
        '^https://i0.wp.com/.*$',
        array(
            'strategy'  => WP_Service_Worker_Caching_Routes::STRATEGY_CACHE_FIRST,
            'cacheName' => 'photoncdn',
            'plugins'   => array(
                // 'statuses' => array(0, 200),
                'expiration' => array(
                    'maxEntries'    => 60,
                    'maxAgeSeconds' => 60 * 60 * 24,
                ),
            ),
        )
    );
});

add_action('wp_front_service_worker', function (\WP_Service_Worker_Scripts $scripts) {
    $scripts->caching_routes()->register(
        '/wp-content/themes/.*\.(?:woff2|:eot|:woff|:ttf|:svg)(\?.*)?$',
        array(
            'strategy'  => WP_Service_Worker_Caching_Routes::STRATEGY_CACHE_FIRST,
            'cacheName' => 'themefonts',
            'plugins'   => array(
                'expiration' => array(
                    'maxEntries'    => 60,
                    'maxAgeSeconds' => 60 * 60 * 24,
                ),
            ),
        )
    );
});


// function wa_apple_splash_images($images)
// {
//     $images[] = array(
//         'href' => 'https://guia.tmphost.net/wp-content/uploads/2022/04/guia-gastronomica-square.png'
//     );

//     return $images;
// }
// add_filter('apple_touch_startup_images', 'wa_apple_splash_images');

if (!function_exists('wa_add_apple_splash')) {
    function wa_add_apple_splash()
    {
        echo '    <link
            rel="apple-touch-startup-image"
            href="https://guia.tmphost.net/wp-content/uploads/2022/04/guia-gastronomica-square.png"
          />';
    }
}
// add_action('wp_head', 'wa_add_apple_splash');
