<?php
/*
Plugin Name: Events Manager
Version: 3.0.7
Plugin URI: http://wp-events-plugin.com
Description: Manage events specifying precise spatial data (Location, Town, Province, etc).
Author: Davide Benini, Marcus Sykes
Author URI: http://wp-events-plugin.com
*/

/*
Copyright (c) 2010, Davide Benini and Marcus Sykes

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/*************************************************/ 

/*
 * Some random notes
 * 
 * - for consistency and easier modifications I propose we use a common coding practice Davide... ieally, $event and $EM_Event should always be only variables used to reference an EM_Event object, no others. 
 * $EM_Event is a global variable, $event has a local scope. same for $events and $EM_Events, they should be an array of EM_Event objects. what do u think?
 * - Would be cool for functions where we pass a reference for something (event, location, category, etc) it could be either the ID, or the object itself. Makes things flexible, not hard to implement, am doing it already (see EM_Events::get) 
 */
//Features
//TODO Better data validation, both into database and outputting info (http://codex.wordpress.org/Data_Validation)
//Known Bugs
//FIXME admin panel showing future events shows event of day before
//FIXME when saving event, select screen has reversed order of events


// INCLUDES 
include_once('classes/object.php'); //Base object, any files below may depend on this 
//Template Tags & Template Logic
include_once("ajax.php");
include_once("bookings.php");
include_once("events.php");
include_once("functions.php");
include_once("locations.php");
include_once("rss.php");
include_once("shortcode.php");
include_once("template-tags.php");
include_once("template-tags-depreciated.php"); //To depreciate
//Widgets
include_once("widgets/events.php");
include_once("widgets/locations.php");
include_once("widgets/calendar.php");
include_once("widgets/booking.php");
//Classes
include_once('classes/booking.php');
include_once('classes/bookings.php');
include_once('classes/calendar.php');
include_once('classes/category.php');
include_once('classes/event.php');
include_once('classes/events.php');
include_once('classes/location.php');
include_once('classes/locations.php');
include_once("classes/mailer.php") ;
include_once('classes/map.php');
include_once('classes/people.php');
include_once('classes/person.php');
//Admin Files
if( is_admin() ){
	include_once('admin/admin.php');
	include_once('admin/bookings.php');
	include_once('admin/categories.php');
	include_once('admin/event.php');
	include_once('admin/events.php');
	include_once('admin/help.php');
	include_once('admin/locations.php');
	include_once('admin/options.php');
	include_once('admin/people.php');
}


// Setting constants
define('EM_VERSION', 3.05); //self expanatory
define('EM_CATEGORIES_TABLE', 'em_categories'); //TABLE NAME
define('EM_EVENTS_TABLE','em_events'); //TABLE NAME
define('EM_RECURRENCE_TABLE','dbem_recurrence'); //TABLE NAME   
define('EM_LOCATIONS_TABLE','em_locations'); //TABLE NAME  
define('EM_BOOKINGS_TABLE','em_bookings'); //TABLE NAME
define('EM_PEOPLE_TABLE','em_people'); //TABLE NAME
define('EM_MIN_CAPABILITY', 'edit_posts');	// Minimum user level to access calendars
define('EM_SETTING_CAPABILITY', 'activate_plugins');	// Minimum user level to access calendars   
define("EM_IMAGE_UPLOAD_DIR", "wp-content/uploads/locations-pics");
//TODO reorganize how defaults are created, e.g. is it necessary to create false entries? They are false by default... less code, but maybe not verbose enough...
       
// DEBUG constant for developing
// if you are hacking this plugin, set to TRUE, a log will show in admin pages
define('DEBUG', false);

// FILTERS
// filters for general events field (corresponding to those of  "the _title")
add_filter('dbem_general', 'wptexturize');
add_filter('dbem_general', 'convert_chars');
add_filter('dbem_general', 'trim');
// filters for the notes field  (corresponding to those of  "the _content")   
add_filter('dbem_notes', 'wptexturize');
add_filter('dbem_notes', 'convert_smilies');
add_filter('dbem_notes', 'convert_chars');
add_filter('dbem_notes', 'wpautop');
add_filter('dbem_notes', 'prepend_attachment');
// RSS general filters
add_filter('dbem_general_rss', 'strip_tags');
add_filter('dbem_general_rss', 'ent2ncr', 8);
add_filter('dbem_general_rss', 'wp_specialchars');
// RSS content filter
add_filter('dbem_notes_rss', 'convert_chars', 8);    
add_filter('dbem_notes_rss', 'ent2ncr', 8);
// Notes map filters
add_filter('dbem_notes_map', 'convert_chars', 8);
add_filter('dbem_notes_map', 'js_escape');

// LOCALIZATION  
// Localised date formats as in the jquery UI datepicker plugin
//TODO Sort out dates, (ref: output idea) 
load_plugin_textdomain('dbem', false, dirname( plugin_basename( __FILE__ ) ).'/includes/langs');

/**
 * This function will load an event into the global $EM_Event variable during page initialization, provided an event_id is given in the url via GET or POST.
 * global $EM_Recurrences also holds global array of recurrence objects when loaded in this instance for performance
 * All functions (admin and public) can now work off this object rather than it around via arguments.
 * @return null
 */
function em_load_event(){
	global $EM_Event, $EM_Recurrences, $EM_Location, $EM_Mailer;
	$EM_Recurrences = array();
	if( isset( $_REQUEST['event_id'] ) && is_numeric($_REQUEST['event_id']) ){
		$EM_Event = new EM_Event($_REQUEST['event_id']);
	}elseif( isset($_REQUEST['recurrence_id']) && is_numeric($_REQUEST['recurrence_id']) ){
		//Eventually we can just remove this.... each event has an event_id regardless of what it is.
		$EM_Event = new EM_Event($_REQUEST['recurrence_id']);
	}elseif( isset($_REQUEST['location_id']) && is_numeric($_REQUEST['location_id']) ){
		$EM_Location = new EM_Location($_REQUEST['location_id']);
	}
	$EM_Mailer = new EM_Mailer();
	define('EM_URI', get_permalink(get_option("dbem_events_page"))); //PAGE URI OF EM 
	define('EM_RSS_URI', get_bloginfo('wpurl')."/?dbem_rss=main"); //RSS PAGE URI
}
add_action('init', 'em_load_event', 1);
                   
/**
 * Settings link in the plugins page menu
 * @param array $links
 * @param string $file
 * @return array
 */
function em_set_plugin_meta($links, $file) {
	$plugin = plugin_basename(__FILE__);
	// create link
	if ($file == $plugin) {
		return array_merge(
			$links,
			array( sprintf( '<a href="admin.php?page=events-manager-options">%s</a>', __('Settings') ) )
		);
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'em_set_plugin_meta', 10, 2 );


// Create the Manage Events and the Options submenus  
function em_create_events_submenu () {
	if(function_exists('add_submenu_page')) {
		//TODO Add flexible permissions
	  	add_object_page(__('Events', 'dbem'),__('Events', 'dbem'),EM_MIN_CAPABILITY,__FILE__,'dbem_events_subpanel', '../wp-content/plugins/events-manager/includes/images/calendar-16.png');
	   	// Add a submenu to the custom top-level menu:
	   		$plugin_pages = array(); 
			$plugin_pages[] = add_submenu_page(__FILE__, __('Edit'),__('Edit'),EM_MIN_CAPABILITY,__FILE__,'dbem_events_subpanel');
			$plugin_pages[] = add_submenu_page(__FILE__, __('Add new', 'dbem'), __('Add new','dbem'), EM_MIN_CAPABILITY, 'new_event', "dbem_new_event_page");
			$plugin_pages[] = add_submenu_page(__FILE__, __('Locations', 'dbem'), __('Locations', 'dbem'), EM_MIN_CAPABILITY, 'locations', "dbem_locations_page");
			$plugin_pages[] = add_submenu_page(__FILE__, __('People', 'dbem'), __('People', 'dbem'), EM_MIN_CAPABILITY, 'people', "em_people_page");
			$plugin_pages[] = add_submenu_page(__FILE__, __('Event Categories','dbem'),__('Categories','dbem'), EM_SETTING_CAPABILITY, "events-manager-categories", 'dbem_categories_subpanel');
			$plugin_pages[] = add_submenu_page(__FILE__, __('Events Manager Settings','dbem'),__('Settings','dbem'), EM_SETTING_CAPABILITY, "events-manager-options", 'dbem_options_subpanel');
			$plugin_pages[] = add_submenu_page(__FILE__, __('Getting Help for Events Manager','dbem'),__('Help','dbem'), EM_SETTING_CAPABILITY, "events-manager-help", 'em_admin_help');
			foreach($plugin_pages as $plugin_page){
				add_action( 'admin_print_scripts-'. $plugin_page, 'em_admin_load_scripts' );
				add_action( 'admin_head-'. $plugin_page, 'em_admin_general_script' );
				add_action( 'admin_print_styles-'. $plugin_page, 'em_admin_load_styles' );
			}
  	}
}
add_action('admin_menu','em_create_events_submenu');


/**
 * Enqueing public scripts and styles 
 */
function em_enqueue_public() {
	wp_enqueue_script ( 'jquery' ); //make sure we have jquery loaded
	wp_enqueue_style('events-manager', WP_PLUGIN_URL.'/events-manager/includes/css/events_manager.css'); //main css
}
add_action ( 'template_redirect', 'em_enqueue_public' );

/**
 * Add a link to the favourites menu
 * @param array $actions
 * @return multitype:string 
 */
function em_favorite_menu($actions) {
	// add quick link to our favorite plugin
	$actions ['admin.php?page=new_event'] = array (__ ( 'Add an event', 'dbem' ), EM_MIN_CAPABILITY );
	return $actions;
}
add_filter ( 'favorite_actions', 'em_favorite_menu' );

/* Creating the wp_events table to store event data*/
function em_activate() {
	require_once(WP_PLUGIN_DIR.'/events-manager/install.php');
	em_install();
}
register_activation_hook( __FILE__,'em_activate');

if( !empty($_GET['em_reimport']) || get_option('dbem_import_fail') == '1' ){
	require_once(WP_PLUGIN_DIR.'/events-manager/install.php');
}
?>