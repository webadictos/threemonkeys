<?php

class WA_Video_Channel_Module extends WA_Module
{
    public function init()
    {


        // $this->loader->add_filter('wa_theme_get_wa_theme_options_page_fields', $this, 'add_settings', 10, 2);
        // $this->loader->add_filter('wa_theme_get_wa_meta_article_metabox_fields', $this, 'add_metabox_fields', 10, 2);

        $this->loader->add_action('save_post', $this, 'get_embed_url_on_save', 10, 1);

        $this->loader->add_filter('rewrite_rules_array', $this, 'modify_videos_archive_rewrite_rules');

        $this->loader->add_filter('get_categories_from_videos', $this, 'get_categories_from_videos', 10, 1);

        $this->loader->add_action('pre_get_posts', $this, 'include_videos_in_archive_pages', 10, 1);
    }

    public function get_embed_url_on_save($post_id)
    {
        if (get_post_type($post_id) !== "fp_video") return;

        $blocks = parse_blocks(get_post_field('post_content', $post_id)); // Obtener todos los bloques del post
        foreach ($blocks as $block) {
            if ($block['blockName'] === 'core/embed') { // Buscar el bloque "embed"
                $embed_url = $block['attrs']['url']; // Obtener la URL del atributo "url"
                $embed_code = wp_oembed_get($embed_url);

                // preg_match('/src=["\'](.*?)["\']/', $embed_code, $matches);
                // if (isset($matches[1])) {
                //     $embed_url = $matches[1];
                // }
                preg_match('/src=["\'](.*?)["\']/', $embed_code, $matches);
                if (isset($matches[1])) {
                    $embed_url_match = $matches[1];
                    // Obtener la extensión del archivo de la URL
                    $url_info = parse_url($embed_url_match);
                    $path = isset($url_info['path']) ? $url_info['path'] : '';
                    $extension = pathinfo($path, PATHINFO_EXTENSION);

                    if ($extension !== 'js') {
                        $embed_url = $embed_url_match;
                    }
                }

                update_post_meta($post_id, '_wa_embed_url', $embed_url); // Guardar la URL como metadata "mi_metadata"
            }
        }
    }


    public function modify_videos_archive_rewrite_rules($rules)
    {
        $new_rules = array(
            'to-watch/category/([^/]+)/?$' => 'index.php?category_name=$matches[1]&post_type=fp_video',
        );

        return $new_rules + $rules;
    }

    public function get_categories_from_videos($_categories)
    {
        // Obtener los términos de la taxonomía "category"
        $categories = get_terms(array(
            'taxonomy' => 'category',
            'hide_empty' => false,
        ));

        // Filtrar los términos que contienen el post_type "videos"
        $videos_categories = array_filter($categories, function ($category) {
            $videos = get_posts(array(
                'post_type' => 'fp_video',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'category',
                        'field' => 'slug',
                        'terms' => $category->slug,
                    ),
                ),
            ));
            return !empty($videos);
        });

        wp_reset_postdata();

        return $videos_categories;
    }

    function include_videos_in_archive_pages($query)
    {
        if ($query->is_main_query() && !is_admin() && (is_category() || is_tag() && empty($query->query_vars['suppress_filters']))) {
            $query->set('post_type', array('post', 'fp_video'));
        }
    }
}
