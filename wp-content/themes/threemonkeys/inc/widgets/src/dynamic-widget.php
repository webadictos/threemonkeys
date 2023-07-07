<?php

// Creating the widget 
class wa_dynamic_widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(

            // Base ID of your widget
            'wa_dynamic_widget',

            // Widget name will appear in UI
            __('WA - Dynamic Widget Category', 'wpb_widget_domain'),

            // Widget description
            array(
                'description' => __('Most Liked', 'wpb_widget_domain'),
                'classname' => 'wa_dynamic_widget',
            )
        );
    }

    // Creating widget front-end

    public function widget($args, $instance)
    {
        global $post;

        $title = apply_filters('widget_title', $instance['title']);

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        // This is where you run the code and display the output


        $_args = array(
            'post_status' => 'publish',
            'post_type' => 'post',
            'cat' => get_query_var('cat'),
            'posts_per_page' => 1,
            'no_found_rows' => true,
            'caller_get_posts' => 1,
            'order' => 'DESC',
            'meta_key' => 'wa_total_shares',
            'orderby' => 'meta_value_num',
            'has_password' => false,
            'post__not_in' => $GLOBALS['showed_ids'] ?? array(),
        );
        $my_query = new WP_Query($_args);

        if (!$my_query->have_posts()) {
            $current_id = get_queried_object_id();

            $parent = WA_Theme()->helper('main-term')->get_parent_term($current_id, true, 'category');

            unset($_args['cat']);

            $_args['category__in'] = array($parent->term_id);

            $my_query = new WP_Query($_args);
        }

        if ($my_query->have_posts()) {

            while ($my_query->have_posts()) {
                $my_query->the_post();

                $_itemArgs = array(
                    'items_layout_css' => 'article-item-cuatro',
                    'items_config' => array(
                        'items_show_tags' => false,
                        'items_show_main_cat' => false,
                        'items_show_badge_cat' => true,
                        'items_show_date' => false,
                        'items_show_author' => true,
                        'items_show_excerpt' => false,
                        'items_show_arrow' => true,
                        'items_show_more_btn' => false,
                        'image_animation' => true,
                        'item_badge_text' => 'Most Liked',
                    ),
                );
                get_template_part('template-parts/items/article', 'item', $_itemArgs);
            }
        }
        wp_reset_query();
        wp_reset_postdata();



        echo $args['after_widget'];
    }

    // Widget Backend 
    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Título', 'wpb_widget_domain');
        }
        // Widget admin form
?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titulo:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
<?php
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

    // Class wpb_widget ends here
}


// Register and load the widget
function load_wa_dynamic_widget()
{
    register_widget('wa_dynamic_widget');
}
add_action('widgets_init', 'load_wa_dynamic_widget');
?>