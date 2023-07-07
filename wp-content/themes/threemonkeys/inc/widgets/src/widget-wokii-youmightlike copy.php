<?php

// Creating the widget 
class wokii_youmightlike_widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(

      // Base ID of your widget
      'wokii_youmightlike_widget',

      // Widget name will appear in UI
      __('Wokii You Might Like', 'wpb_widget_domain'),

      // Widget description
      array('description' => __('Widget de You Might Like Popular', 'wpb_widget_domain'), 'widget-te-puede-interesar')
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
?>
    <?php

    //if ( false === ( $tag_ids[] = get_transient( "thRelatedPosts_tags_".$post->ID ) ) ) {

    $tags = wp_get_post_tags(get_the_ID(), array('orderby' => 'count', 'order' => 'DESC'));
    $tag_ids = array();
    $tagnumber = 0;
    $invalidtags = array(3004, 3005, 3006);
    foreach ($tags as $individual_tag) {
      $tagnumber++;
      if (in_array($individual_tag->term_id, $invalidtags)) continue;
      $tag_ids[] = $individual_tag->term_id;
      if ($tagnumber == 3) break;
    }

    if (count($tag_ids) > 0) {

      $_args = array(
        'post_status' => 'publish',
        'post_type' => 'post',
        'tag__in' => $tag_ids,
        'post__not_in' => array($post->ID),
        'posts_per_page' => 3,
        'no_found_rows' => true,
        'caller_get_posts' => 1,
        'order' => 'DESC',
        'orderby' => 'date',
        'has_password' => false,
      );
    } else {

      $category_ids = get_post_primary_category(get_the_ID(), 'category', true);
      //foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
      $_args = array(
        'post_status' => 'publish',
        'post_type' => 'post',
        'category__in'     => $category_ids['all_categories'],
        'post__not_in' => array(get_the_ID()),
        'posts_per_page' => 3,
        'no_found_rows' => true,
        'caller_get_posts' => 1,
        'order' => 'DESC',
        'orderby' => 'date',
        'has_password' => false,
      );
    }
    $my_query = new WP_Query($_args);


    if ($my_query->have_posts()) {
      $_itemArgs = array();

      $_itemArgs['items_layout'] = "article-item article-item-widget";
      $_itemArgs['items_config'] = array('items_show_tags' => false, 'items_show_main_cat' => true, 'items_show_badge_cat' => false, 'items_show_date' => false, 'items_show_author' => false, 'items_show_excerpt' => false);

      while ($my_query->have_posts()) {
        $my_query->the_post();

        get_template_part('template-parts/items/article', 'item', $_itemArgs);
      }
    }
    wp_reset_query();
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
function wokii_load_youmightlike_widget()
{
  register_widget('wokii_youmightlike_widget');
}
add_action('widgets_init', 'wokii_load_youmightlike_widget');
?>