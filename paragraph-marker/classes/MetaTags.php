<?php
namespace PostHighlighter;
class MetaTags{
	public function __construct(){
		add_action( 'wp_head', [$this,'gretathemes_meta_description']);
	}
	public function gretathemes_meta_description() {
		global $post;
		if ( (is_singular() && get_post_type($post) == "post-paragraph") || !empty( $_GET['sub_post'] ) ) {
			$main_post_id = $post->ID;
			if( !empty( $_GET['sub_post'] ) )
				$post = get_post($_GET['sub_post']);
			$des_post = strip_tags( $post->post_content );
			$des_post = strip_shortcodes( $post->post_content );
			$des_post = str_replace( array("\n", "\r", "\t"), ' ', $des_post );
			$des_post = "An interesting read on ". get_site_url() .": " . $des_post;
			$des_post = mb_substr( $des_post, 0, 250, 'utf8' ) . " ... ";
			$post_paragraph_id = get_post_meta($post->ID,'post_id',true);
			$posttags = get_the_tags( $post_paragraph_id );
			if ($posttags) {
				foreach($posttags as $tag) {
					$des_post .= "#$tag->name "; 
				}
			}
			$post_link = get_permalink($post);
			$post_title = get_the_title($post);
			if( $post_paragraph_id ){
				$post_link = get_permalink($post_paragraph_id);
				$post_link = add_query_arg( 'sub_post', $main_post_id  , $post_link );
				$post_title = get_the_title($post_paragraph_id);
			}
			echo '<meta name="description" property="og:description" content="'.$des_post.'">' . "\n";
			echo "<meta property='og:title' content='$post_title'/>" . "\n";
			echo "<meta property='og:url' content='$post_link'/>" . "\n";
			
		}
	}
}
new MetaTags;