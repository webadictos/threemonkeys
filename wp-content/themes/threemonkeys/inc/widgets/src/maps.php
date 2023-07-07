<?php

// Creating the widget 
class WA_Maps extends WP_Widget
{

    function __construct()
    {
        parent::__construct(

            // Base ID of your widget
            'wa_maps_widget',

            // Widget name will appear in UI
            __('WA - Maps', 'wpb_widget_domain'),

            // Widget description
            array(
                'description' => __('Maps - Places', 'wpb_widget_domain'),
                'classname' => 'wa_maps_widget',
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
        // if (!empty($title))
        echo $args['before_title'] . get_the_title() . $args['after_title'];

?>
        <div id="map-widget-<?php echo get_the_ID(); ?>" class="map-widget" data-map-id="<?php echo get_the_ID(); ?>">

            <div class="map-container">

            </div>

            <div class="map-places-container">

            </div>

        </div>

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
function load_wa_maps_widget()
{
    register_widget('WA_Maps');
}
add_action('widgets_init', 'load_wa_maps_widget');
?>