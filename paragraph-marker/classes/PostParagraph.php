<?php
namespace PostHighlighter;

class PostParagraph{
	public function __construct(){
		add_action( 'init', [$this,'create_post_paragraph'] );// Regsiter custom post type	
		add_action('admin_menu', [$this,'register_custom_submenu']);// Wordpress admin menu
		add_filter( 'the_content', [$this,'add_link_to_main_post'], 1 );
		add_filter( 'the_excerpt', [$this,'add_link_to_main_post'], 1 );
	}
	public function add_link_to_main_post( $content ) {
		if ( get_post_type() == 'post-paragraph' ) {
			$post_paragraph = get_post_meta( get_the_ID() , 'post_id' , true );
			if( $post_paragraph && ( $post_paragraph_link = get_permalink($post_paragraph) ) )
				return $content . " Click <a href='$post_paragraph_link' target='_blank'>here</a> to go to main post.";
		}

		return $content ;
	}

	/** Create custom post **/
	public function create_post_paragraph(){
		register_post_type( 'post-paragraph',
			array(
				'labels' => array(
					'name' => __( 'Post Paragraph' ),
					'singular_name' => __( 'Post Paragraph' )
				),
				'public' => true,
				'has_archive' => true,
				'show_in_rest' => true,
				'supports'	=>	['title','editor','excerpt']
			)
		);
	}

	/** Add sub menu under custom post type **/
	public function register_custom_submenu(){
		/** Save by popularity **/
		add_submenu_page(
			'edit.php?post_type=post-paragraph',
			'Saves by Popularity',
			'Saves by Popularity',
			'manage_options',
			'post-highlighter-saves-by-popularity',
			[$this,'saves_by_popularity']
		);
		/** All Saves **/
		add_submenu_page(
			'edit.php?post_type=post-paragraph',
			'All Saves',
			'All Saves',
			'manage_options',
			'post-highlighter-all-saves',
			[$this,'all_saves']
		);
	}

	public function saves_by_popularity(){
		include(POST_HIGHLIGHTER_PATH . "template/saves-by-popularity.php");
	}

	public function all_saves(){
		include(POST_HIGHLIGHTER_PATH . "template/all-saves.php");
	}
}
new PostParagraph;