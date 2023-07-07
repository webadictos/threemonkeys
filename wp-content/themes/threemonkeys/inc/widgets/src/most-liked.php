<?php

// Creating the widget 
class wa_most_liked extends WP_Widget
{

    function __construct()
    {
        parent::__construct(

            // Base ID of your widget
            'wa_most_liked_widget',

            // Widget name will appear in UI
            __('WA - Most Liked', 'wpb_widget_domain'),

            // Widget description
            array(
                'description' => __('Most Liked', 'wpb_widget_domain'),
                'classname' => 'wa_most_liked_widget',
            )
        );
    }

    // Creating widget front-end

    public function widget($args, $instance)
    {
        global $post, $wp_query;

        if (has_category('maps')) return;

        $title = apply_filters('widget_title', $instance['title']);

        $number_of_items = 3;

        $_args = array();

        // if (is_single(get_the_ID())) {
        // Obtener la categoría del post actual
        $categories = get_the_category(get_the_ID());
        $category_ids = wp_list_pluck($categories, 'term_id');

        // Configurar los argumentos de la consulta para obtener los posts con más wa_total_shares en la misma categoría
        $_args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $number_of_items,
            'meta_key' => 'wa_total_shares',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'category__in' => $category_ids,
            'post__not_in' => array(get_the_ID()),
        );
        // } elseif (is_category()) {
        //     // Obtener la categoría actual
        //     $current_category = get_queried_object();

        //     if ($current_category instanceof WP_Term) {
        //         // Configurar los argumentos de la consulta para obtener los posts con más wa_total_shares en la categoría actual
        //         $_args = array(
        //             'post_type' => 'post',
        //             'post_status' => 'publish',
        //             'posts_per_page' => $number_of_items,
        //             'meta_key' => 'wa_total_shares',
        //             'orderby' => 'meta_value_num',
        //             'order' => 'DESC',
        //             'category__in' => array($current_category->term_id),
        //         );
        //     }
        // }

        $query = new WP_Query($_args);

        // Mostrar el widget solo si hay posts en la misma categoría
        if ($query->have_posts()) {
            // Mostrar el título del widget
            echo $args['before_widget'];
            if (!empty($title))
                echo $args['before_title'] . $title . $args['after_title'];

            // Mostrar los posts
            echo '<ul>';
            while ($query->have_posts()) {
                $query->the_post();
                echo '<li><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></li>';
            }
            echo '</ul>';

            echo $args['after_widget'];

            // Restaurar la configuración original de la consulta de WordPress
            wp_reset_query();
        }
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
function load_wa_most_liked_widget()
{
    register_widget('wa_most_liked');
}
add_action('widgets_init', 'load_wa_most_liked_widget');
?>