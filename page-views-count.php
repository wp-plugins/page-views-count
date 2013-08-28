<?php
/*
Plugin Name: Page Views Count
Description: Show front end users all time views and views today on posts, pages, index pages and custom post types with the Page Views Count Plugin. Use the Page Views Count function to add page views to any content type or object created by your theme or plugins.
Version: 1.0.2
Requires at least: 3.0
Tested up to: 3.6
Author: A3 Revolution
Author URI: http://a3rev.com
License: A "Slug" license name e.g. GPL2
*/
?>
<?php
define('A3_PVC_FOLDER', dirname(plugin_basename(__FILE__)));
define('A3_PVC_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
define('A3_PVC_PLUGIN_NAME', plugin_basename(__FILE__) );

include("pvc_settings.php");
include("pvc_class.php");

/**
 * Process when plugin is activated
 */
function pvc_install(){
	update_option('a3_pvc_version', '1.3.5');
	
	// empty pvc_daily table for daily
	wp_schedule_event( strtotime( date('Y-m-d'). ' 00:00:00' ), 'daily', 'pvc_empty_daily_table_daily_event_hook' );
	
	A3_PVC::install_database();
	A3_PVC_Settings::install_settings_default();
	update_option('pvc_just_installed', true);
}
register_activation_hook(__FILE__,'pvc_install');

/**
 * On the scheduled action hook, run the function.
 */
add_action( 'pvc_empty_daily_table_daily_event_hook', 'pvc_empty_daily_table_do_daily' );
function pvc_empty_daily_table_do_daily() {
	global $wpdb;
	$wpdb->query("DELETE FROM " . $wpdb->prefix . "pvc_daily WHERE time <= '".date('Y-m-d', strtotime('-2 days'))."'");
}

/**
 * Process when plugin is deactivated
 */
register_deactivation_hook( __FILE__, 'pvc_deactivation' );
function pvc_deactivation() {
	wp_clear_scheduled_hook( 'pvc_empty_daily_table_daily_event_hook' );
}

function pvc_uninstall() {
	if ( get_option('pvc_clean_on_deletion') == 1 ) {
		delete_option('pvc_settings');
		delete_option('a3_pvc_version');
		delete_option('a3rev_pvc_plugin');
		delete_option('a3rev_auth_pvc');
		delete_option('pvc_clean_on_deletion');
		
		global $wpdb;
		$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'pvc_total');
		$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'pvc_daily');
	}
}
if ( get_option('pvc_clean_on_deletion') == 1 ) {
	register_uninstall_hook( __FILE__, 'pvc_uninstall' );
}

function a3_pvc_plugin_init() {
	if ( get_option('pvc_just_installed') ) {
		delete_option('pvc_just_installed');
		wp_redirect( admin_url( 'options-general.php?page=a3-pvc', 'relative' ) );
		exit;
	}
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
if(version_compare(get_option('a3_pvc_version'), '1.3.5') === -1){
	wp_schedule_event( strtotime( date('Y-m-d'). ' 00:00:00' ), 'daily', 'pvc_empty_daily_table_daily_event_hook' );
	global $wpdb;
	$sql = "ALTER TABLE ". $wpdb->prefix . "pvc_daily  CHANGE `id` `id` BIGINT NOT NULL AUTO_INCREMENT";
	$wpdb->query($sql);
	$sql = "ALTER TABLE ". $wpdb->prefix . "pvc_total  CHANGE `id` `id` BIGINT NOT NULL AUTO_INCREMENT";
	$wpdb->query($sql);
		
	update_option('a3_pvc_version', '1.3.5');
}

update_option('a3_pvc_version', '1.3.5');


function pvc_stats($postid, $have_echo = 1){
	return A3_PVC::custom_stats_echo($postid, $have_echo);
}
function pvc_stats_update($postid, $have_echo = 1){
	return A3_PVC::custom_stats_update_echo($postid, $have_echo);
}

function pvc_check_exclude ($postid=0) {
	return A3_PVC::pvc_check_exclude($postid);
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