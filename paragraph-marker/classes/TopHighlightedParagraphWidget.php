<?php
namespace PostHighlighter;
use WP_Query,WP_Widget;
class TopHighlightedParagraphWidget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'post-highlighter-top-paragraph',  // Base ID
            'Post highlighter top paragraph'   // Name
        );
        add_action( 'widgets_init', function() {
            register_widget( self::class );
        });

        add_action( 'wp_ajax_post_highlighter_chart_data', [$this,'chart_data'] );
        add_action( 'wp_ajax_nopriv_post_highlighter_chart_data', [$this,'chart_data'] );
    }
    public $args = array(
        'before_title'  => '<h4 class="widgettitle">',
        'after_title'   => '</h4>',
        'before_widget' => '<div class="widget-wrap post-highlighter">',
        'after_widget'  => '</div>'
    );
    public function widget( $args, $instance ) {
        global $wpdb;
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        $current_date = current_time("Y-m-d");
        $end_date = date_create( $current_date )->sub( new \DateInterval("P".(current_time("w")+1)."D") );
        $to_date = $end_date->format("Y-m-d H:i:s");
        $end_date->sub( new \DateInterval("P6D") );
        $from_date = $end_date->format("Y-m-d H:i:s");
        $user_paragraph_field = "{$wpdb->prefix}user_paragraphs.highlighted_on";
        extract(Helpers::get_top_highlighted_posts(10,0," $user_paragraph_field >= '$from_date' AND $user_paragraph_field <= '$to_date' "));
        include( POST_HIGHLIGHTER_PATH . "template/top-highlighted-widgets.php");
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

    /** Get chart data **/
    public function chart_data(){
        global $wpdb;
        $current_date = current_time("Y-m-d" . " 00:00:00");
        $user_paragraph_field = "{$wpdb->prefix}user_paragraphs.highlighted_on >= '$current_date'";
        extract(Helpers::get_top_highlighted_posts(1,0," $user_paragraph_field "));
        $fetched_today = $total_records;
        

        $response = [
            'status'    =>  1,
            'message'   =>  'Data fetched successfully.',
            'data'      =>  compact('fetched_today')
        ];


        wp_send_json( $response );
    }
}
new TopHighlightedParagraphWidget;