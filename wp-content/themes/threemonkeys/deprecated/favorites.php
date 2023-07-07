<?php

/**
 * Customize the Favorites Button HTML
 */
// add_filter('favorites/button/html', 'custom_favorites_button_html', 10, 4);
// function custom_favorites_button_html($html, $post_id, $favorited, $site_id)
// {
//     return $html;
// }

/**
 * Change the Favorites Preset Button HTML (Unfavorited)
 */
add_filter('favorites/button/text/default', 'custom_favorites_text_html');
function custom_favorites_text_html($html)
{
    // print_r($html);
    return '';
}

/**
 * Change the Favorites Preset Button HTML (Favorited)
 */
add_filter('favorites/button/text/active', 'custom_favorites_text_html_active');
function custom_favorites_text_html_active($html)
{
    return '';
}

// add_filter('favorites/button/html', 'wa_custom_favorites_button_html', 10, 4);
function wa_custom_favorites_button_html($html, $post_id, $favorited, $site_id)
{
    $html = '<span data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="Disabled popover">' . $html . '</span>';
    return $html;
}

add_filter('favorites/button/icon', 'custom_favorites_button_icon');
function custom_favorites_button_icon($html)
{
    return $html;
}


function wa_favorites_lang()
{

    $currentLang = apply_filters('wpml_current_language', null);

    $currentLang = !is_null($currentLang) ? $currentLang : 'es';

    $traducciones['en'] = array(
        "favorite-text" => "Add to my list",
        "favorited-text" => "Remove from my list",
    );

    if (isset($traducciones[$currentLang])) :

?>
        <style>
            :root {
                <?php foreach ($traducciones[$currentLang] as $variable => $valor) {
                    echo "--{$variable}: '{$valor}';\n";
                } ?>
            }
        </style>
<?php

    endif;
}

add_action("wp_head", "wa_favorites_lang");
