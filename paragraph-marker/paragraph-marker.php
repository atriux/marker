<?php
/**
 * Plugin Name:       Paragraph Marker
 * Plugin URI:        https://github.com/atriux/marker
 * Description:       Save interesting paragraphs by double clicking on them and storing them in your personal archive.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Aleksandar Dordevic
 * Author URI:        https://www.rehumanise.net/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       paragraph-marker
 * Domain Path:       /languages
 */


define("POST_HIGHLIGHTER_PATH",plugin_dir_path( __FILE__ ));
define("POST_HIGHLIGHTER_URL",plugins_url('/',__FILE__));

include( "classes/EnqueueAssets.php");//Include assets
include( "classes/PluginActive.php" );//Create table
include( "classes/PostParagraph.php");//Custom post-paragraph post
include( "classes/SaveParagraph.php" );//Save paragraph
include( "classes/CustomHelpers.php" );//Custom helpers
include( "classes/FrontendShortcodes.php");//Frontend Shortcodes
include( "classes/SettingOptions.php");//Settings Option

include("classes/SavesByPopularity.php");//Save by popularity
include("classes/AllSaves.php");//All Saves
include("classes/MetaTags.php");//Add meta tags for saved paragraphs

include("classes/TopHighlightedParagraphWidget.php");//Widgets - to show get top paragraphs
include("classes/ChartWidgets.php");//Widgets - to show information of saved paragraphs

include("classes/Helpers.php");
register_activation_hook( __FILE__, ['PostHighlighter\PluginActive','CreateTables'] );