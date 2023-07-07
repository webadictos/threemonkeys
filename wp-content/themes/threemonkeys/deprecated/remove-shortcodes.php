<?php

/** Remove Unused Shortcodes */

//add_shortcode( 'adsense', '__return_false' );
add_filter('the_content', 'wa_remove_unused_shortcode');
function wa_remove_unused_shortcode($content)
{
    $pattern = wa_get_unused_shortcode_regex();
    $content = preg_replace_callback('/' . $pattern . '/s', 'strip_shortcode_tag', $content);
    return $content;
}

function wa_get_unused_shortcode_regex()
{
    global $shortcode_tags;
    $tagnames = array_keys($shortcode_tags);
    $tagregexp = join('|', array_map('preg_quote', $tagnames));
    $regex = '\\[(\\[?)';
    $regex .= "(?!$tagregexp)";
    $regex .= '\\b([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*+(?:\\[(?!\\/\\2\\])[^\\[]*+)*+)\\[\\/\\2\\])?)(\\]?)';
    return $regex;
}
