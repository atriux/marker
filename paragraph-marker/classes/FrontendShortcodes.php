<?php
namespace PostHighlighter;
class FrontendShortcodes{
	public function __construct(){
		add_action('init',function(){
			add_shortcode( 'highlighter_user', [$this,'highlighter_user'] );
			add_shortcode( 'highlighter_top', [$this,'highlighter_top'] );
		});
	}

	/** User saved highlighter paragraphs **/
	public function highlighter_user( $atts , $content , $shortcode_tag ){
		global $wpdb;
		if( !is_user_logged_in() ){
			return "<p>Please login!</p>";
		}
		$user_id = get_current_user_id();
		$atts = shortcode_atts( array(
			'per_page' => 10
		), $atts );
		$mysql_limit = $atts['per_page'];
		$current_page = ($_GET['user_page'] ?? 1);
		$mysql_offset = ($current_page - 1) * $atts['per_page'];
		$post_paragraph = $wpdb->posts;
		$user_paragraph = "{$wpdb->prefix}user_paragraphs";
		$where_clause = "WHERE {$user_paragraph}.deleted_at IS NULL AND {$user_paragraph}.user_id = {$user_id} AND $post_paragraph.post_status = 'publish'";
		$join_clause = "INNER JOIN $post_paragraph ON $post_paragraph.ID = $user_paragraph.paragraph_post_id";
		$wp_db_query = "SELECT count(*) FROM $user_paragraph $join_clause $where_clause";
		$total_records = $wpdb->get_var( $wp_db_query );
		$total_pages = (int)($total_records / $mysql_limit) + ( ($total_records % $mysql_limit) ? 1 : 0 );
		$wpdb_query = "SELECT $user_paragraph.*
		FROM $user_paragraph
		$join_clause
		$where_clause
		ORDER BY {$user_paragraph}.id DESC
		LIMIT $mysql_limit OFFSET $mysql_offset";
		$all_paragraphs = $wpdb->get_results( $wpdb_query );
		$next_link = $previous_link = ""; 
		if( $current_page != 1 )
			$previous_link = add_query_arg( 'user_page', $current_page - 1 , get_page_link() );
		if( $current_page < $total_pages )
			$next_link = add_query_arg( 'user_page', $current_page + 1 , get_page_link() );
		if( !count( $all_paragraphs ) ){
			return "No paragraph!";
		}
		ob_start();
		include( POST_HIGHLIGHTER_PATH . "template/highlighter_user.php" );
		return ob_get_clean();
	}

	/** All paragraphs order byBy most saved **/
	public function highlighter_top($atts , $content , $shortcode_tag){
		global $wpdb;
		$atts = shortcode_atts( array(
			'per_page' => 10
		), $atts );
		$mysql_limit = $atts['per_page'];
		$current_page = ($_GET['top_page'] ?? 1);
		$mysql_offset = ($current_page - 1) * $atts['per_page'];
		$post_paragraph = $wpdb->posts;
		$user_paragraph = "{$wpdb->prefix}user_paragraphs";
		$join_clause = "INNER JOIN $post_paragraph ON $post_paragraph.ID = {$user_paragraph}.paragraph_post_id";
		$where_clause = "WHERE {$user_paragraph}.deleted_at IS NULL AND $post_paragraph.post_status = 'publish'";
		
		$wpdb_query = "SELECT $user_paragraph.* , COUNT({$user_paragraph}.paragraph_post_id) as counter
		FROM $user_paragraph
		$join_clause
		$where_clause
		GROUP BY {$user_paragraph}.paragraph_post_id
		ORDER BY COUNT($user_paragraph.paragraph_post_id) DESC
		LIMIT $mysql_limit OFFSET $mysql_offset";
		$all_paragraphs = $wpdb->get_results( $wpdb_query );

		/** Pagination **/
		$next_link = $previous_link = ""; 
		
		$wp_db_query = "SELECT COUNT(*) FROM
		(SELECT DISTINCT ({$user_paragraph}.paragraph_post_id)
		FROM $user_paragraph
		$join_clause
		$where_clause) AS TEMP_TABLE";
		$total_records = $wpdb->get_var( $wp_db_query );
		$total_pages = (int)($total_records / $mysql_limit) + ( ($total_records % $mysql_limit) ? 1 : 0 );
		if( $current_page != 1 )
			$previous_link = add_query_arg( 'top_page', $current_page - 1 , get_page_link() );
		if( $current_page < $total_pages )
			$next_link = add_query_arg( 'top_page', $current_page + 1 , get_page_link() );
		if( !count( $all_paragraphs ) ){
			return "No paragraph!";
		}
		ob_start();
		include( POST_HIGHLIGHTER_PATH . "template/highlighter_top.php" );
		return ob_get_clean();
	}
}
new FrontendShortcodes;