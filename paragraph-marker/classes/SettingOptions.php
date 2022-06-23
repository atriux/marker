<?php
namespace PostHighlighter;
class SettingOptions{
	public function __construct(){
		/**
		* register wporg_settings_init to the admin_init action hook
 		*/
		add_action( 'admin_init' , [$this,'wporg_settings_init'] );
	}
	public function wporg_settings_init() {
	    // register a new setting for "general" page
		register_setting('general', 'posthighlighter_saved_page');

    	// register a new section in the "general" page
		add_settings_section(
			'wporg_settings_section',
			'Post Hightlighter Settings', [$this,'wporg_settings_section_callback'],
			'general'
		);

    	// register a new field in the "wporg_settings_section" section, inside the "general" page
		add_settings_field(
			'wporg_settings_field',
			'Saved highlighter page', [$this,'wporg_settings_field_callback'],
			'general',
			'wporg_settings_section'
		);
	}

	// section content cb
	public function wporg_settings_section_callback() {
		// echo '<p>WPOrg Section Introduction.</p>';
	}

	// field content cb
	public function wporg_settings_field_callback() {
    	// get the value of the setting we've registered with register_setting()
		$setting = get_option('posthighlighter_saved_page');
		$all_pages = get_pages();
		?>
		<select name="posthighlighter_saved_page">
			<?php
			foreach( $all_pages as $single_page ):
				$selected_attr = "";
				if( $setting == $single_page->ID )
					$selected_attr = "selected";
				echo "<option value='{$single_page->ID}' $selected_attr>{$single_page->post_title}</option>";
			endforeach;
			?>
		</select>
		<?php
	}
}
new SettingOptions;