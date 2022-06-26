<?php
namespace PostHighlighter;
class EnqueueAssets{
	public function __construct(){
		add_action( 'wp_enqueue_scripts', [$this,'adding_scripts'] ); 
		add_filter( 'the_content', [$this,'add_div_around_content'] );
		add_action('wp_footer', [$this,'include_footer'] ); 
	}
	/** Include Js and Css **/
	public function adding_scripts(){
		/** JS include **/
		$js_data = array('ajax_url' => admin_url( 'admin-ajax.php' ));
		if( is_singular() &&  is_main_query() && !is_front_page() && !is_home() ){
			global $post;
			$js_data['post_id'] = $post->ID;
		}
		wp_enqueue_script("post-highlighter-chart","https://cdn.jsdelivr.net/npm/chart.js",['jquery'],null,true);
		wp_enqueue_script("post-highlighter", POST_HIGHLIGHTER_URL . "assets/js/post-highlighter.js" , array('jquery','post-highlighter-chart'),time(), true);
		wp_localize_script( "post-highlighter", 'post_highlighter', $js_data );
		/** Css include **/
		wp_enqueue_style('post-highlighter-css', POST_HIGHLIGHTER_URL . "assets/css/post-highlighter.css");
		wp_enqueue_style('post-highlighter-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
	}
	/** Add dive around content **/
	public function add_div_around_content( $content ) {
		// Check if we're inside the main loop in a single Post.
		if ( is_singular() && in_the_loop() && is_main_query() && !is_front_page() && !is_home() && 
			( !is_user_logged_in() || Helpers::get_saved_information('paragraph_saving'))  ) {

			return "<div class='post-highligher-get-paragraphs'>" . $content . "</div>";
		}
		return $content;
	}
	/** Add poup in footer **/
	public function include_footer() { 
		if( is_singular() && !is_front_page() && !is_home() ){
			include( POST_HIGHLIGHTER_PATH . "template/bookmark-popup.php");
		}
	}
}
new EnqueueAssets;