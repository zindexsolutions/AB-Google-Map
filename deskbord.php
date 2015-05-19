<?php
/**
* Plugin Name: AB Google Map
* Plugin URI: http:www.yuvrajkhavad.in
* Version: 1.0 
* Description: Google map and icon with diffrect style
* Author: Yuvraj Khavad
* Author URI: http:www.yuvrajkhavad.in
**/
session_start();
global $wpdb;
$ab_plugin_url = WP_PLUGIN_URL."/".str_replace(basename(__FILE__),"",plugin_basename(__FILE__));
$ab_plugin_path = ABSPATH."wp-content/plugins/ab_google_map/";
define("AB_PRO_PATH",$ab_plugin_url);
define("SHOW_PAGE","10");
$conn=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);	
mysql_select_db(DB_NAME,$conn);

//here define all table here
define("AB_GOOGLE_MAP",$wpdb->prefix."ab_google_map");
// define all url
define("AB_DESH_URL",get_bloginfo("siteurl").'/wp-admin/admin.php?page=ab_gm');

// code run at plugin active
register_activation_hook(__FILE__,'ab_InstallMe');
function ab_InstallMe()
{ 
	global $wpdb;
	$sql="CREATE TABLE IF NOT EXISTS `wp_ab_google_map` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  	`address` varchar(255) NOT NULL,
  	`latitude` varchar(255) NOT NULL,
  	`longitude` varchar(255) NOT NULL,
  	`upload_icon` varchar(255) NOT NULL)";	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	
	// code for save default data
	$wpdb->insert(
		AB_GOOGLE_MAP, 
		array( 
			'address' => 'New York, NY, United States', 
			'latitude' => '40.7033127',
			'longitude' => '-73.979681', 
			'upload_icon' => $ab_plugin_path.'/images/ab_gm_marker.png'			
		) 
	);
}
/* Here Activation Plugins Hook End */

register_deactivation_hook(__FILE__,'ab_UnstallMe');
function ab_UnstallMe()
{
	global $wpdb;
    $ab_gm_table = AB_GOOGLE_MAP;
	$wpdb->query("DROP TABLE IF EXISTS $ab_gm_table");
}
// code for all menus
add_action('admin_menu', 'ab_create_menu');
function ab_create_menu()
{
	add_menu_page( 'AB Google Map', 'AB Google Map', 10, 'ab_gm', 'ab_gm_management');
	add_submenu_page( 'ab_gm', 'Upload Map Style', 'Upload Map Style', 10, 'uploadP_map_style', 'ab_gm_add_style' );
}

/*********** code for media uploader-START ***********/ 
add_action('admin_print_scripts', 'ab_my_admin_scripts');
function ab_my_admin_scripts() 
{
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('my-upload', AB_PRO_PATH.'js/my-script.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('my-upload');

}
add_action('admin_print_styles', 'ab_my_admin_styles');
function ab_my_admin_styles() 
{
	wp_enqueue_style('thickbox');
	wp_enqueue_style('message_new', AB_PRO_PATH.'css/message.css');
}
/*********** code for media uploader-END ***********/ 

add_action('admin_init', 'ab_editor_admin_init');
function ab_editor_admin_init() 
{
  	wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));
	wp_enqueue_style('thickbox');
}

include("paging/nicePaging.php");
include("include/function.php");
include("include/ab_gmap_shortcode.php");
wp_enqueue_script('myscript',AB_PRO_PATH.'js/functions.js');
wp_enqueue_script('google-maps150', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyASm3CwaK9qtcZEWYa-iQwHaGi3gcosAJc&sensor=false');
function ab_gm_management()
{	
	include("include/ab_form.php");
	$ab_gm=new ab_gm();
	$action=$_GET["action"];
	$id=(empty($_GET["id"]))?'0':$_GET["id"];

	if(empty($action))
	{
		$ab_gm->showForm($id,$action);
	}
	else if(($action)=='delete')
	{
		$ab_gm->deleteLocation($id);
	}
	else if(($action)=='edit')
	{
		$ab_gm->showForm($id,$action);
	}
}
// code for add new style
function ab_gm_add_style()
{
	include("include/ab_add_style_form.php");
	$ab_add_gm=new ab_gm_add_style();
	if(empty($action))
	{
		$ab_add_gm->show_add_style_form($id,$action);
	}
}
?>
