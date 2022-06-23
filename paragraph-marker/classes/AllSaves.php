<?php
namespace PostHighlighter;
require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class AllSaves extends \WP_List_Table {
	private $per_page = 20;
	function __construct() {
		parent::__construct( array(
			'singular'  => 'All Save',     
			'plural'    => 'All Saves',    
			'ajax'      => false 
		));
	}

	public function column_default( $item, $column_name ){
		switch( $column_name ) {
			case('saved_paragraph'):
			return get_post_meta( $item->paragraph_post_id , 'paragraph' , true );
			break;
			case('highlighed_on'):
			return $item->highlighted_on;
			break;
			case('ip_address'):
			return $item->ip_address;
			break;
			case('deleted_at'):
			return ($item->deleted_at ? $item->deleted_at : 'Not yet');
			break;
			case('saved_from_country'):
			if( $geo_location = json_decode( $item->ip_info ) ){
				return "{$geo_location->geoplugin_city} {$geo_location->geoplugin_region} {$geo_location->geoplugin_countryName}";
			}
			return "NA";
			break;
			default:
			return print_r( $item , true ) ;
		}
	}

	


	private function table_data( $return_total = false ){
		global $wpdb;
		$all_saves = "SELECT *
		FROM {$wpdb->prefix}user_paragraphs
		ORDER BY id DESC";
		if( !$return_total ){
			$row_offset = $this->per_page * ( $this->get_pagenum() - 1 );
			$all_saves .= " LIMIT $row_offset , {$this->per_page} ";
		}

		$return_record = $wpdb->get_results( $all_saves );
		if( $return_total ){
			return count( $return_record );
		}
		return $return_record;
	}

	public function get_columns(){
		return [
			'saved_paragraph' => 'Paragraph',
			'highlighed_on' => 'Highlighted On',
			'ip_address'	=>	'Ip Address',
			'saved_from_country'	=>	'Saved from country',
			'deleted_at'		=>	'Deleted at'
		];
	}

	public function prepare_items()  {
		global $wpdb;  
		$columns = $this->get_columns();
		$data = $this->table_data();
		$totalitems = $this->table_data( true );
		$perpage = $this->per_page;
		$this->_column_headers = array($columns,[],[]); 
		$totalpages = ceil($totalitems/$perpage); 
		$currentPage = $this->get_pagenum();
		//$data = array_slice($data,(($currentPage-1)*$perpage),$perpage);
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		) );

		$this->items =$data;
	}
}