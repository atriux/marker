<?php
namespace PostHighlighter;
use WP_Query,WP_Widget;
class Post_Highlighter_chart extends WP_Widget {
    function __construct() {
        parent::__construct(
            'post-highlighter-chart',  // Base ID
            'Post highlighter chart'   // Name
        );
        add_action( 'widgets_init', function() {
            register_widget( self::class );
        });
    }
    public $args = array(
        'before_title'  => '<h4 class="widgettitle post-highlighter">',
        'after_title'   => '</h4>',
        'before_widget' => '<div class="widget-wrap">',
        'after_widget'  => '</div>'
    );
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        echo '<div class="chart-outer"><canvas class="canvas-chart"></canvas></div>';
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
        ?>
        <p>
        	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
        		<?php echo esc_html__( 'Title:', 'text_domain' ); ?>
        	</label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}
new Post_Highlighter_chart;