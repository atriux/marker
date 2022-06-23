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
}