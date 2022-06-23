<?php
namespace PostHighlighter;
class CustomHelpers{
	/** Current user ip address **/
	public static function user_ip_address(){
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}

	/** Get IP information **/
	public static function get_ip_info( $ip ){
		$url        = "http://www.geoplugin.net/json.gp?ip=$ip";
		try {
			$get_content = json_decode(file_get_contents($url));
		}catch(\Exception $e) {
			$get_content = false;
			self::$api_error =  true;
		}
		if( $get_content && $get_content->geoplugin_status == 200 ){
			return $get_content;
		}
		return false;
	}
}