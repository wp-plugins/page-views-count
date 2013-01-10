<?php
/*
Plugin Name: Page Views Count
Description: Show front end users all time views and views today on posts, pages, index pages and custom post types with the Page Views Count Plugin. Use the Page Views Count function to add page views to any content type or object created by your theme or plugins.
Version: 1.0.1
Requires at least: 3.0
Tested up to: 3.5
Author: A3 Revolution
Author URI: http://a3rev.com
License: A "Slug" license name e.g. GPL2
*/
?>
<?php
define('A3_PVC_FOLDER', dirname(plugin_basename(__FILE__)));
define('A3_PVC_URL', WP_CONTENT_URL.'/plugins/'.A3_PVC_FOLDER);
define('A3_PVC_PLUGIN_NAME', plugin_basename(__FILE__) );

include("pvc_settings.php");
include("pvc_class.php");

function pvc_install(){
	update_option('a3_pvc_version', '1.3.4');
	A3_PVC::install_database();
	A3_PVC_Settings::install_settings_default();
}
register_activation_hook(__FILE__,'pvc_install');

function a3_pvc_plugin_init() {
	// Add language
	load_plugin_textdomain( 'pvc', false, A3_PVC_FOLDER.'/languages' );
}
add_action( 'init', 'a3_pvc_plugin_init' );

update_option('a3rev_pvc_plugin', 'a3_page_view_count');
update_option('a3rev_auth_pvc', '');
if(version_compare(get_option('a3_pvc_version'), '1.2') === -1){
	update_option('a3_pvc_version', '1.2');
	A3_PVC::upgrade_version_1_2();
}
if(version_compare(get_option('a3_pvc_version'), '1.3.0') === -1){
	A3_PVC_Settings::install_settings_default();
	update_option('a3_pvc_version', '1.3.0');
}

update_option('a3_pvc_version', '1.3.4');


function pvc_stats($postid, $have_echo = 1){
	return A3_PVC::custom_stats_echo($postid, $have_echo);
}
function pvc_stats_update($postid, $have_echo = 1){
	return A3_PVC::custom_stats_update_echo($postid, $have_echo);
}

// Add Admin Menu
add_action('admin_menu', 'a3_register_pvc_menu');
function a3_register_pvc_menu() {
	if (function_exists ( 'add_submenu_page' )) {
		add_submenu_page( 'options-general.php', __('Page Views Count', 'pvc'), __('Page Views Count', 'pvc'), 'manage_options', 'a3-pvc', array('A3_PVC_Settings', 'show_settings') );
	}else{
		global $menu;
		if(version_compare(get_bloginfo("version"), '2.9', '>='))
			$menu['4.356'] = array( '', 'manage_options', 'separator-genesis', '', 'wp-menu-separator' );
		add_menu_page(__('Page Views Count', 'pvc'), __('Page Views Count', 'pvc'), 'manage_options', 'a3-pvc', array('A3_PVC_Settings', 'show_settings'), '', '4.355');
	}
}

?>