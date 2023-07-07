<?php

// Creating the widget 
class wa_video_widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(

            // Base ID of your widget
            'wa_video_widget',

            // Widget name will appear in UI
            __('WA - Video', 'wpb_widget_domain'),

            // Widget description
            array(
                'description' => __('Video', 'wpb_widget_domain'),
                'classname' => 'wa_video_widget',
            )
        );
    }

    // Creating widget front-end

    public function widget($args, $instance)
    {
        global $post;

        $title = apply_filters('widget_title', $instance['title']);
        $esInfinito = (isset($_REQUEST['action']) &&  $_REQUEST['action']=="loadmore") ? true : false;

        if(!$esInfinito):

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        // This is where you run the code and display the output

        $videos = waGetYouTubeVideos(8);

$item = $videos->items[0];
$image = waGetYouTubeThumbnail($item->id->videoId);

?>
    
    <div class="video-player-container">
                            <div class="video-thumbnail video-player" data-youtube-id="<?php echo $item->id->videoId; ?>">
                                <div id="<?php echo $item->id->videoId; ?>">
                                    <img class="img-fluid mx-auto d-block jetpack-lazy-image" src="<?php echo $image; ?>" alt="<?php echo $item->snippet->title; ?>" data-lazy-srcset="<?php echo $image; ?> 1856w, <?php echo $image; ?> 300w, <?php echo $image; ?> 768w, <?php echo $image; ?> 1024w" data-lazy-sizes="(max-width: 1856px) 100vw, 1856px" data-lazy-src="<?php echo $image; ?>?is-pending-load=1" srcset="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="300" height="168" loading="lazy">
                                </div>
                            </div>
                            <h3 class="video-caption"><?php echo $item->snippet->title; ?></h3>
                        </div>    
        
    <?php
        echo $args['after_widget'];
        endif;
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
function load_wa_video_widget()
{
    register_widget('wa_video_widget');
}
add_action('widgets_init', 'load_wa_video_widget');
?>