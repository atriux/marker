<?php
namespace PostHighlighter;
use WP_Query;
class SaveParagraph{
	public function __construct(){
		/** Save paragraph **/
		add_action( 'wp_ajax_save_paragraph', [$this,'save_paragraph'] );
		add_action( 'wp_ajax_nopriv_save_paragraph', [$this,'save_paragraph'] );
		/** Change paragraph saving settings **/
		add_action( 'wp_ajax_paragraph_saving_setting', [$this,'paragraph_saving_setting'] );
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
			$main_post = get_post($_POST['post_id']);
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
					'post_author'	=>	$main_post->post_author,
					'meta_input'	=>	array(
						'paragraph' => 	 $_POST['paragraph_html'],
						'post_id'	=>	 $_POST['post_id']
					)
				]);
			}
			/** Set tags to parent post tag **/
			$post_tags = get_the_terms($_POST['post_id'],'post_tag');
			if( $post_tags ){
				$post_tags = array_column( $post_tags , 'name' ) ;
				wp_set_post_terms( $paragraph_post_id , $post_tags , 'post-paragraph-tag');
			}
			/** Post thumbnail **/
			$thumbnail_id = get_post_thumbnail_id( $_POST['post_id'] );
			if( $thumbnail_id ){
				set_post_thumbnail( $paragraph_post_id , $thumbnail_id );
			}

			$select_query = "SELECT *
			FROM {$wpdb->prefix}user_paragraphs
			WHERE paragraph_post_id = {$paragraph_post_id} 
			AND deleted_at is NULL AND ";
			if( is_user_logged_in() ){
				$user_id = get_current_user_id();
				$select_query .= " user_id = {$user_id}";
			}else{
				$cookie_value = Helpers::get_cookie_value();
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
					$insert_data['cookie_id'] = $cookie_value;
				}
				$wpdb->insert( 
					"{$wpdb->prefix}user_paragraphs", 
					$insert_data
				);
				$user_paragraph_id = $wpdb->insert_id;
				echo "<p class='save-message'>Paragraph saved successfully.</p>";
			}
			echo "<p class='paragraph-html'>{$_POST['paragraph_html']}</p>";
			echo "<p>From: {$main_post->post_title} by " . get_the_author_meta('user_login',$main_post->post_author) ."</p>";
			$single_paragraph = $wpdb->get_row( "SELECT *
				FROM {$wpdb->prefix}user_paragraphs 
				WHERE id = $user_paragraph_id");
			echo "<div class='paragraph-share-social'>";
			include( POST_HIGHLIGHTER_PATH . "template/share-social.php" );
			echo "</div><a href='" . add_query_arg( ['action'=>'delete_paragraph','paragraph_id'=>$single_paragraph->id] , get_page_link() ) . "' id='delete-paragraph-link' style='display:none'>DELETE</a>";
		}else{
			echo "<p>Some thing went wrong ...</p>";
		}
		if( !is_user_logged_in() ){
			echo '<p class="warning">Paragraph saved temporarily. Log in to avoid losing it.</p>';
		}
		
		if( ($posthighlighter_saved_page = get_option('posthighlighter_saved_page')) ){
			echo "<a href='".get_permalink($posthighlighter_saved_page)."'> Saved paragraphs </a>";
		}
		exit;
	}
	/** Change paragraph saving settings **/
	public function paragraph_saving_setting(){
		update_user_meta(get_current_user_id(),'paragraph_saving',$_POST['paragraph_saving']);
		wp_send_json([
			'status'	=>	true,
			'data'		=>	[
				'button_title'	=>	($_POST['paragraph_saving'] ? "Disable Saving" : "Enable Saving"),
				'saving'		=>	($_POST['paragraph_saving'] ? 0 : 1)
			]
		]);
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
				wp_redirect( "mailto:?subject=$share_paragraph_title&body=$share_description" );
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
		//Show explain double tab PopUp cookie
		$cookie_name = "double_tapping_modal_opened";
		if( !is_user_logged_in() && !isset($_COOKIE[$cookie_name]) ){
			setcookie($cookie_name, '1', time() + 60 * 60 * 24 * 30 , '/');
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