<?php
namespace PostHighlighter;
use PostHighlighter\CustomHelpers;
use WP_Query;
class SaveParagraph{
	public function __construct(){
		/** Save paragraph **/
		add_action( 'wp_ajax_save_paragraph', [$this,'save_paragraph'] );
		add_action( 'wp_ajax_nopriv_save_paragraph', [$this,'save_paragraph'] );
		/** Share on social network **/
		add_action( 'init', [$this,'share_social_network']  );
		/** Create cookie **/
		add_action( 'init' , [$this,'create_cookie']);
		/** Delete paragraph **/
		add_action('init' , [$this,'delete_paragraph']);
	}
	/** Save paragraph **/
	public function save_paragraph(){
		global $wpdb;
		if( !empty($_POST['post_id']) && !empty($_POST['paragraph_html']) ){
			$wp_args = array(
				'post_type'  => 'post-paragraph',
				'meta_query' => array(
					array(
						'key'     => 'paragraph',
						'value'   => $_POST['paragraph_html']
					),
					array(
						'key' 		=> 'post_id',
						'value'   	=> $_POST['post_id'],
						'type'    	=> 'numeric'
					),
				)
			);
			$post_paragraph = new WP_Query( $wp_args );
			if ( $post_paragraph->have_posts() ) {
				while ( $post_paragraph->have_posts() ) {
					$post_paragraph->the_post();
					$paragraph_post_id = get_the_ID();
					break;
				}
			} else {
				$paragraph_post_id = wp_insert_post([
					'post_title' =>	(strlen($_POST['paragraph_html']) > 50 ? substr($_POST['paragraph_html'],0,50). " ..." : $_POST['paragraph_html'] ),
					'post_status'	=>	'publish',
					'post_type'	=>	'post-paragraph',
					'post_content'	=>	$_POST['paragraph_html'],
					'meta_input'	=>	array(
						'paragraph' => 	 $_POST['paragraph_html'],
						'post_id'	=>	 $_POST['post_id']
					)
				]);
			}


			
			//echo "<p>Post paragraph ID is $paragraph_post_id.</p>";
			$select_query = "SELECT *
			FROM {$wpdb->prefix}user_paragraphs
			WHERE paragraph_post_id = {$paragraph_post_id} 
			AND deleted_at is NULL AND ";
			if( is_user_logged_in() ){
				$user_id = get_current_user_id();
				$select_query .= " user_id = {$user_id}";
			}else{
				$cookie_name = 'post_highlighter_user_cookie';
				$cookie_value = $_COOKIE[$cookie_name];
				$select_query .= " cookie_id = '{$cookie_value}'";
			}
			/** Check or add user paragraph **/
			if( $user_paragraph = $wpdb->get_row($select_query) ){
				$user_paragraph_id = $user_paragraph->id;
				echo "<p class='save-message'>Paragraph was already saved.</p>";
			}else{
				$user_ip_address = CustomHelpers::user_ip_address();
				$user_ip_info = CustomHelpers::get_ip_info( $user_ip_address );
				$insert_data = array(
					'paragraph_post_id'	=>	$paragraph_post_id,
					'highlighted_on'	=>	current_time('mysql'),
					'ip_address'		=>	$user_ip_address,
					'ip_info'			=>	json_encode( $user_ip_info )
				);
				if( is_user_logged_in() ){
					$insert_data['user_id'] = get_current_user_id();
				}else{
					$insert_data['cookie_id'] = $_COOKIE[$cookie_name];
				}

				$wpdb->insert( 
					"{$wpdb->prefix}user_paragraphs", 
					$insert_data
				);
				$user_paragraph_id = $wpdb->insert_id;
				echo "<p class='save-message'>Paragraph saved successfully.</p>";
			}
			echo "<p class='paragraph-html'>{$_POST['paragraph_html']}</p>";
			$single_paragraph = $wpdb->get_row( "SELECT *
				FROM {$wpdb->prefix}user_paragraphs 
				WHERE id = $user_paragraph_id");
			echo "<div class='paragraph-share-social'>";
			include( POST_HIGHLIGHTER_PATH . "template/share-social.php" );
			echo "</div>";
		}else{
			echo "<p>Some thing went wrong ...</p>";
		}
		if( !is_user_logged_in() )
			echo '<p class="warning">Paragraph saved temporarily. Log in to avoid losing it.</p>';
		elseif( ($posthighlighter_saved_page = get_option('posthighlighter_saved_page')) && ($posthighlighter_saved_page = get_page_link($posthighlighter_saved_page) ) ){
			echo "<a href='$posthighlighter_saved_page'> Saved paragraphs </a>";
		}
		exit;
	}

	/** Share to social network **/
	public function share_social_network(){
		if( !empty($_GET['action']) && $_GET['action'] == 'share_social_network' ){
			global $wpdb;
			$table_name = $wpdb->prefix . 'paragraphs_shared_on';
			$wpdb->insert( 
				$table_name, 
				array( 
					'user_paragraph_id' => $_REQUEST['user_paragraph'], 
					'shared_on' => $_REQUEST['share_on'],
					'shared_at' => current_time('mysql'),
					'shared_by'	=>	get_current_user_id()
				)
			);
			$user_paragraph_record = $wpdb->get_row(
				"SELECT * FROM {$wpdb->prefix}user_paragraphs WHERE id = {$_REQUEST['user_paragraph']}"
			);
			$share_paragraph = get_post_meta($user_paragraph_record->paragraph_post_id,'paragraph',true);
			$share_paragraph_title = get_the_title($user_paragraph_record->paragraph_post_id);
			$share_paragraph_title = preg_replace('/[^A-Za-z0-9 ]/','',$share_paragraph_title);
			$share_post_link = get_permalink(get_post_meta($user_paragraph_record->paragraph_post_id,'post_id',true));
			$share_description = "An interesting read on ". get_site_url() .": $share_paragraph Here is the entire article: $share_post_link ";

			$posttags = get_the_tags( get_post_meta($user_paragraph_record->paragraph_post_id,'post_id',true) );
			$hash_tags = '';
			if ($posttags) {
				foreach($posttags as $tag) {
					$hash_tags .= "$$tag->name "; 
				}
			}
			$share_description .= $hash_tags;
			$share_description = urlencode($share_description);
			switch( $_REQUEST['share_on'] ){
				case('whatsapp'):
				wp_redirect( "https://api.whatsapp.com/send?text=$share_description" );
				break;
				case('linkedin'):
				wp_redirect( "https://www.linkedin.com/sharing/share-offsite/?url=" . get_permalink($user_paragraph_record->paragraph_post_id) );
				break;
				case('email'):
				wp_redirect( "https://mail.google.com/mail/?view=cm&su=$share_paragraph_title&body=$share_description" );
				break;
				case('twitter'):
				$share_description = "An interesting read on ". get_site_url() .": $share_paragraph Here is the entire article: $share_post_link ";
				$share_description = urlencode($share_description);
				$hash_tags = '';
				if ($posttags) {
					foreach($posttags as $tag) {
						$hash_tags .= "$tag->name,"; 
					}
				}
				$hash_tags = trim($hash_tags,",");
				wp_redirect( "https://twitter.com/intent/tweet?url=$share_post_link&text=$share_description&hashtags=$hash_tags" );
				break;
				case('facebook'):
				wp_redirect( "https://www.facebook.com/sharer.php?u=" . get_permalink($user_paragraph_record->paragraph_post_id) );
				break;
				case('default'):
				echo "SOMETHING WENT WRONG";
			}
			exit;
		}

	}

	/** Create cookie **/
	public function create_cookie(){
		global $wpdb;
		$cookie_name = 'post_highlighter_user_cookie';
		if( !is_user_logged_in() ){
			if( !isset($_COOKIE[$cookie_name]) ){
				setcookie($cookie_name, wp_generate_password(100,false), time()+86400*30);
			}
		}elseif( isset($_COOKIE[$cookie_name]) ){
			$where_update = [
				'cookie_id'=>$_COOKIE[$cookie_name]
			];
			$data_update = [
				'user_id'=>get_current_user_id()
			];
			$wpdb->update("{$wpdb->prefix}user_paragraphs" , $data_update , $where_update );
			setcookie($cookie_name, '', time()-100);
		}
	}
	/** Delete paragraph **/
	public function delete_paragraph(){
		if( isset( $_GET['action'] ) && $_GET['action'] == 'delete_paragraph' && !empty( $_GET['paragraph_id'] ) ){
			global $wpdb;
			$wpdb->update("{$wpdb->prefix}user_paragraphs" , 
				['deleted_at'=>current_time('mysql')] ,
				['id'=>$_GET['paragraph_id'] ] 
			);
			$redirect_back_link = $_SERVER['HTTP_REFERER'];
			$redirect_to = add_query_arg( 'highligher_success' , 'Paragraph removed successfully' , $redirect_back_link);
			wp_redirect( $redirect_to );
			exit;
		}
	}
}
new SaveParagraph;