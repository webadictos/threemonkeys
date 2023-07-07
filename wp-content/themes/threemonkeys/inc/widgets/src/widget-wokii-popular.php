<?php

// Creating the widget 
class wokii_popular_widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(

      // Base ID of your widget
      'wokii_popular_widget',

      // Widget name will appear in UI
      __('Wokii + Popular', 'wpb_widget_domain'),

      // Widget description
      array('description' => __('Widget de Wokii Popular', 'wpb_widget_domain'),)
    );
  }

  // Creating widget front-end

  public function widget($args, $instance)
  {
    $title = apply_filters('widget_title', $instance['title']);

    // before and after widget arguments are defined by themes
    echo $args['before_widget'];
    if (!empty($title))
      echo $args['before_title'] . $title . $args['after_title'];

    // This is where you run the code and display the output
?>
    <?php




    if (function_exists('stats_get_csv')) :



      if (false === ($popularToday = get_transient("wokii_popular_today"))) {

        //  $popularToday = stats_get_csv( 'postviews', "period=days&days=3&limit=15" );

        $popularToday = stats_get_csv('postviews', array('days' => 2, 'limit' => 20));

        set_transient('wokii_popular_today', $popularToday, 30 * MINUTE_IN_SECONDS); // 30 Minutos
      }

      $i = 0;
      $exclude_ids = array(338);

      $popularIds = array();

      foreach ($popularToday as $p) {

        if (intval($p['post_id']) > 0 && !in_array(intval($p['post_id']), $exclude_ids) && intval($p['post_id']) !== get_the_ID()) :


          if (!has_post_thumbnail($p['post_id'])) continue;
          $popularIds[] = $p['post_id'];
          $i++;
          if ($i === 3) :
            break;
          endif;
        endif;
      }

      if (count($popularIds) > 0) :

        $_args = array(
          'post__in' => $popularIds,
          'no_found_rows' => true,
          'update_post_meta_cache' => false,
          'update_post_term_cache' => false,
          'post_status' => 'publish',
        );

        $articlesQuery = new WP_Query();



        $articlesQuery->query($_args);

        $_itemArgs = array();

        $_itemArgs['items_layout'] = "article-item article-item-widget";
        $_itemArgs['items_config'] = array('items_show_tags' => false, 'items_show_main_cat' => true, 'items_show_badge_cat' => false, 'items_show_date' => false, 'items_show_author' => false, 'items_show_excerpt' => false);

        while ($articlesQuery->have_posts()) : $articlesQuery->the_post();




          get_template_part('template-parts/items/article', 'item', $_itemArgs);


          $i++;

        endwhile;
        wp_reset_postdata();
      endif;




    endif;
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
function wokii_load_popular_widget()
{
  register_widget('wokii_popular_widget');
}
add_action('widgets_init', 'wokii_load_popular_widget');
?>