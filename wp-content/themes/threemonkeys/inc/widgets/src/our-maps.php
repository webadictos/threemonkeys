<?php

// Creating the widget 
class WA_Our_Maps extends WP_Widget
{

    function __construct()
    {
        parent::__construct(

            // Base ID of your widget
            'wa_our_maps_widget',

            // Widget name will appear in UI
            __('WA -Our Maps', 'wpb_widget_domain'),

            // Widget description
            array(
                'description' => __('Our Maps', 'wpb_widget_domain'),
                'classname' => 'wa_our_maps_widget',
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


        // This is where you run the code and display the output
?>
        <?php

        //if ( false === ( $tag_ids[] = get_transient( "thRelatedPosts_tags_".$post->ID ) ) ) {

        $tags = wp_get_post_tags(get_the_ID(), array('orderby' => 'count', 'order' => 'DESC'));
        $tag_ids = array();
        $tagnumber = 0;
        $invalidtags = array();
        foreach ($tags as $individual_tag) {
            $tagnumber++;
            if (in_array($individual_tag->term_id, $invalidtags)) continue;
            $tag_ids[] = $individual_tag->term_id;
            if ($tagnumber == 3) break;
        }



        $map_category = wa_theme()->module('maps')->config('map_category') ?? '';


        //wp_get_post_categories(get_the_ID(), array('fields' => 'ids'));

        $_args = array(
            'post_status' => 'publish',
            'post_type' => 'post',
            'category_name'     => $map_category,
            'post__not_in' => array(get_the_ID()),
            'posts_per_page' => 4,
            'no_found_rows' => true,
            'caller_get_posts' => 1,
            'order' => 'DESC',
            'orderby' => 'date',
            'has_password' => false,
        );


        if (is_category()) {
            $cat = get_category_by_slug($map_category);
            $current_category = get_queried_object_id();

            $category_and = array($cat->term_id, $current_category);

            unset($_args['category_name']);

            $_args['category__and'] = $category_and;
        }



        $my_query = new WP_Query($_args);


        if ($my_query->have_posts()) {

            if (!empty($title))
                echo $args['before_title'] . $title . $args['after_title'];
        ?>
            <div class="wa_our_maps_widget__items">
                <?php
                while ($my_query->have_posts()) {
                    $my_query->the_post();

                    $_itemArgs = array(
                        'items_layout_css' => 'article-item--related',
                        'items_config' => array(
                            'items_show_main_cat' => true,
                            'items_show_author' => true,

                        ),
                    );
                    get_template_part('template-parts/items/article', 'item', $_itemArgs);
                }
                ?>
            </div>
        <?php
        }
        wp_reset_postdata();
        ?>
    <?php
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
function load_wa_our_maps_widget()
{
    register_widget('WA_Our_Maps');
}
add_action('widgets_init', 'load_wa_our_maps_widget');
?>