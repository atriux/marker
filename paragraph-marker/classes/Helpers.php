<?php
namespace PostHighlighter;
class Helpers{
	/** Get link to share on social account **/
	static function get_social_share_link( $share_post , $share_on ){
		return add_query_arg([
			'user_paragraph' => $share_post->id ,
			'action' => 'share_social_network',
			'share_on'=>$share_on
		] , site_url() );
	}

	/** Button to enable or disable paragraph saving **/
	static function get_button_saving(){
		if( !is_user_logged_in() ){
			return '';
		}
		include( POST_HIGHLIGHTER_PATH . "template/get-button-saving.php");
	}

	/** Get user saved information **/
	static function get_saved_information( $key = null , $user_id = null ){
		/* Default user Id */
		if( empty($user_id) ){
			$user_id = get_current_user_id();
		}
		if( !metadata_exists('user',$user_id,'paragraph_saving') ){
			add_user_meta($user_id,'paragraph_saving',1);
		}
		if( $key ){
			return get_user_meta( $user_id , $key , true );
		}else{
			return get_user_meta( $user_id );
		}
	}
	/** Get cookie value **/
	static function get_cookie_value(){
		$cookie_name = 'post_highlighter_user_cookie';
		return $_COOKIE[$cookie_name];
	}
	/** Show double tapping explaining modal  **/
	static function show_double_tapping_modal(){
		if( is_user_logged_in() ){
			$user_id = get_current_user_id();
			$popup_opend = self::get_saved_information('double_tapping_popup_opened');
			if( $popup_opend ){
				return false;
			}
			update_user_meta( $user_id , 'double_tapping_popup_opened' , 1 );
			return true;
		}
		//var_dump( empty($_COOKIE['double_tapping_modal_opened']) );
		return empty($_COOKIE['double_tapping_modal_opened']);
	}

	/** Get top high lighted Posts **/
	static function get_top_highlighted_posts( $limit , $offset , $additional_where = "" ){
		global $wpdb;
		$post_paragraph = $wpdb->posts;
		$user_paragraph = "{$wpdb->prefix}user_paragraphs";
		$join_clause = "INNER JOIN $post_paragraph ON $post_paragraph.ID = {$user_paragraph}.paragraph_post_id";
		$where_clause = "WHERE {$user_paragraph}.deleted_at IS NULL AND $post_paragraph.post_status = 'publish'";
		if( $additional_where ){
			$where_clause .= " AND $additional_where ";
		}

		$wpdb_query = "SELECT $user_paragraph.* , COUNT({$user_paragraph}.paragraph_post_id) as counter
		FROM $user_paragraph
		$join_clause
		$where_clause
		GROUP BY {$user_paragraph}.paragraph_post_id
		ORDER BY COUNT($user_paragraph.paragraph_post_id) DESC
		LIMIT $limit OFFSET $offset";
		//die( $wpdb_query );
		$wp_db_query = "SELECT COUNT(*) FROM
		(SELECT DISTINCT ({$user_paragraph}.paragraph_post_id)
		FROM $user_paragraph
		$join_clause
		$where_clause) AS TEMP_TABLE";
		$return_result = [
			'all_paragraphs'	=>	$wpdb->get_results( $wpdb_query ),
			'total_records'		=>	$wpdb->get_var( $wp_db_query )
		];
		return $return_result;
	}
}