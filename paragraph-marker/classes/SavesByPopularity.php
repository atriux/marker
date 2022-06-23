<?php
namespace PostHighlighter;
require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class SavesByPopularity extends \WP_List_Table {
	private $per_page = 20;
	function __construct() {
		parent::__construct( array(
			'singular'  => 'Save by paragraph',     
			'plural'    => 'Saves by paragraph',    
			'ajax'      => false 
		));
	}

	public function column_default( $item, $column_name ){
		switch( $column_name ) {
			case 'paragraph':
			return get_post_meta( $item->post_id , 'paragraph' , true );
			break;
			case 'main_post':
			$post_id = get_post_meta( $item->post_id , 'post_id' , true );
			$post_status = get_post_status( $post_id );
			if( $post_status && $post_status == 'publish' ){
				return "<a href='".get_permalink( $post_id )."' target='_blank'> ". get_the_title($post_id) ." </a>";
			}
			return "Post removed";
			break;
			case 'number_of_saves':
			return $item->number_of_saves;
			break;
			case 'linkedin_count':
			case 'facebook_count':
			case 'twitter_count':
			case 'whatsapp_count':
			case 'email_count':
			return self::social_media_count( str_replace('_count','',$column_name) , $item );			
			break;
			default:
			return print_r( $item , true ) ;
		}
	}

	/**
	Get count of social share
	**/
	public static function social_media_count( $social_media , $item ){
		global $wpdb;
		if( empty($item->user_paragraphs) ){
			return 0;	
		}
		$social_count_query = "SELECT 
		COUNT(*)
		FROM {$wpdb->prefix}paragraphs_shared_on
		WHERE shared_on = '$social_media' AND user_paragraph_id IN ({$item->user_paragraphs})";
		return $wpdb->get_var( $social_count_query );
	}

	/**
	 ** Decide which columns to activate the sorting functionality on
	 ** @return array $sortable, the array of columns that can be sorted by the user
	 *
	 * */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'number_of_saves' => ['number_of_saves',0]
		);
		return $sortable_columns;
	}

	private function table_data( $return_total = false ){
		global $wpdb;
		$number_of_saves_clause = "COUNT({$wpdb->prefix}user_paragraphs.id)";

		$saves_by_popularity = "SELECT
		{$wpdb->posts}.ID as post_id , 
		{$wpdb->prefix}user_paragraphs.id as user_paragraphs_id , 
		$number_of_saves_clause as number_of_saves,
		GROUP_CONCAT( {$wpdb->prefix}user_paragraphs.id ) as user_paragraphs
		FROM {$wpdb->posts}
		LEFT JOIN {$wpdb->prefix}user_paragraphs ON {$wpdb->posts}.ID = {$wpdb->prefix}user_paragraphs.paragraph_post_id
		WHERE {$wpdb->posts}.post_type = 'post-paragraph'
		GROUP BY {$wpdb->posts}.ID ";

		if( !empty($_GET['orderby']) ){
			$saves_by_popularity .= " ORDER BY  $number_of_saves_clause {$_GET['order']}";
		}

		if( !$return_total ){
			$row_offset = $this->per_page * ( $this->get_pagenum() - 1 );
			$saves_by_popularity .= " LIMIT $row_offset , {$this->per_page}";
		}

		$return_record = $wpdb->get_results( $saves_by_popularity );

		if( $return_total ){
			return count( $return_record );
		}
		return $return_record;
	}

	public function get_columns(){
		return [
			'paragraph' => 'Paragraph',
			'main_post' => 'Post',
			'number_of_saves' => 'Number of saves',
			'linkedin_count'	=>	'Linkedin Count',
			'twitter_count'	=>	'Twitter Count',
			'facebook_count'	=>	'Facebook Count',
			'whatsapp_count'	=>	'Whatsapp Count',
			'email_count'		=>	'Email count',
			//'other' => 'Others',
		];
	}

	public function prepare_items()  {
		global $wpdb;  
		$columns = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$data = $this->table_data();
		$totalitems = $this->table_data( true );
		$perpage = $this->per_page;
		$this->_column_headers = array($columns,[],$sortable); 
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