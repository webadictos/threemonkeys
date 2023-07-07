<?php
// Custom_Walker_Category - in functions.php
class Custom_Walker_Category extends Walker_Category
{

    function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0)
    {
        extract($args);
        $cat_name = esc_attr($category->name);
        $cat_name = apply_filters('list-cats', $cat_name, $category);
        $linkclass = "nav-link";
        $link = '<a href="' . esc_url(get_term_link($category)) . '" ';
        if ($use_desc_for_title == 0 || empty($category->description))
            $link .= 'title="' . esc_attr(sprintf(__('Ver notas en %s', 'wa-theme'), $cat_name)) . '"';
        else
            $link .= 'title="' . esc_attr(strip_tags(apply_filters('category_description', $category->description, $category))) . '"';

        $link .= 'class="' . $linkclass . '"';
        $link .= '>';
        $link .= $cat_name . '</a>';
        if (!empty($feed_image) || !empty($feed)) {
            $link .= ' ';
            if (empty($feed_image))
                $link .= '(';
            $link .= '<a href="' . esc_url(get_term_feed_link($category->term_id, $category->taxonomy, $feed_type)) . '"';
            if (empty($feed)) {
                $alt = ' alt="' . sprintf(__('Feed for all posts filed under %s'), $cat_name) . '"';
            } else {
                $title = ' title="' . $feed . '"';
                $alt = ' alt="' . $feed . '"';
                $name = $feed;
                $link .= $title;
            }
            $link .= '>';
            if (empty($feed_image))
                $link .= $name;
            else
                $link .= "<img src='$feed_image'$alt$title" . ' />';
            $link .= '</a>';
            if (empty($feed_image))
                $link .= ')';
        }
        if (!empty($show_count))
            $link .= ' (' . intval($category->count) . ')';
        if ('list' == $args['style']) {
            $output .= "\t<li";
            $class = 'nav-item  menu-item cat-item cat-item-' . $category->term_id;
            $class .= ' menu-' . $category->slug;
            $class .= ' ';

            // YOUR CUSTOM CLASS
            $termchildren = get_term_children($category->term_id, $category->taxonomy);
            $collapseData = "";
            $haveKids = false;
            if (count($termchildren) > 0) {
                $class .=  ' i-have-kids';
                $collapseData = 'data-bs-toggle="collapse" data-bs-target=".menu-' . $category->slug . ' > .children" aria-expanded="false"';
                $haveKids = true;
            }

            if (!empty($current_category)) {
                $_current_category = get_term($current_category, $category->taxonomy);
                if ($category->term_id == $current_category)
                    $class .=  ' current-cat';
                elseif ($category->term_id == $_current_category->parent)
                    $class .=  ' current-cat-parent';
            }
            $output .=  ' class="' . $class . '"';
            // $output .= ' ' . $collapseData . ' ';
            $output .= ">\n";
            if ($haveKids) $output .= '<div class="collapse-container">';
            $output .= $link;
            if ($haveKids) $output .= '<button ' . $collapseData . '></button></div>';
        } else {
            $output .= "\t$link\n";
        }
    } // function start_el


    /**
     * Starts the list before the elements are added.
     *
     * @since 2.1.0
     *
     * @see Walker::start_lvl()
     *
     * @param string $output Used to append additional content. Passed by reference.
     * @param int    $depth  Optional. Depth of category. Used for tab indentation. Default 0.
     * @param array  $args   Optional. An array of arguments. Will only append content if style argument
     *                       value is 'list'. See wp_list_categories(). Default empty array.
     */
    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        if ('list' !== $args['style']) {
            return;
        }

        $indent  = str_repeat("\t", $depth);
        $output .= "$indent<ul class='children collapse'>\n";
    }
} // class Custom_Walker_Category