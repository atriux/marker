<?php
namespace PostHighlighter;
class PluginActive{
	
	public static function CreateTables(){
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'user_paragraphs';
		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id BIGINT(9) NULL,
		paragraph_post_id BIGINT(9) NOT NULL,
		highlighted_on datetime NOT NULL,
		cookie_id varchar(100) NULL,
		ip_address varchar(100) NULL,
		ip_info text NULL,
		deleted_at datetime NULL,
		PRIMARY KEY  (id)
		) $charset_collate;";//
		dbDelta( $sql );
		$table_name = $wpdb->prefix . 'paragraphs_shared_on';
		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_paragraph_id BIGINT(9) NULL,
		shared_on varchar(100) NOT NULL,
		shared_at datetime NOT NULL,
		shared_by BIGINT(9) NULL,
		PRIMARY KEY  (id)
		) $charset_collate;";//
		dbDelta( $sql );
	}
}