<?php
/**
 * Process when plugin is activated
 */
function pvc_install(){
	update_option('a3_pvc_version', '1.3.8');
	
	// empty pvc_daily table for daily
	wp_schedule_event( strtotime( date('Y-m-d'). ' 00:00:00' ), 'daily', 'pvc_empty_daily_table_daily_event_hook' );
	
	A3_PVC::install_database();
	
	// Set Settings Default from Admin Init
	global $wp_pvc_admin_init;
	$wp_pvc_admin_init->set_default_settings();
	
	update_option('pvc_just_installed', true);
}

/**
 * Process when plugin is deactivated
 */
function pvc_deactivation() {
	wp_clear_scheduled_hook( 'pvc_empty_daily_table_daily_event_hook' );
}

update_option('a3rev_pvc_plugin', 'a3_page_view_count');
update_option('a3rev_auth_pvc', '');

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

// Add custom style to dashboard
add_action( 'admin_enqueue_scripts', array( 'A3_PVC', 'a3_wp_admin' ) );

// Add extra link on left of Deactivate link on Plugin manager page
add_action('plugin_action_links_'.A3_PVC_PLUGIN_NAME, array('A3_PVC', 'settings_plugin_links') );

// Add text on right of Visit the plugin on Plugin manager page
add_filter( 'plugin_row_meta', array('A3_PVC', 'plugin_extra_links'), 10, 2 );

	
// Need to call Admin Init to show Admin UI
global $wp_pvc_admin_init;
$wp_pvc_admin_init->init();

// Add upgrade notice to Dashboard pages
add_filter( $wp_pvc_admin_init->plugin_name . '_plugin_extension', array( 'A3_PVC', 'plugin_extension' ) );
		
$admin_pages = $wp_pvc_admin_init->admin_pages();
if ( is_array( $admin_pages ) && count( $admin_pages ) > 0 ) {
	foreach ( $admin_pages as $admin_page ) {
		add_action( $wp_pvc_admin_init->plugin_name . '-' . $admin_page . '_tab_start', array( 'A3_PVC', 'plugin_extension_start' ) );
		add_action( $wp_pvc_admin_init->plugin_name . '-' . $admin_page . '_tab_end', array( 'A3_PVC', 'plugin_extension_end' ) );
	}
}
	
/**
 * On the scheduled action hook, run the function.
 */
add_action( 'pvc_empty_daily_table_daily_event_hook', 'pvc_empty_daily_table_do_daily' );
function pvc_empty_daily_table_do_daily() {
	global $wpdb;
	$wpdb->query("DELETE FROM " . $wpdb->prefix . "pvc_daily WHERE time <= '".date('Y-m-d', strtotime('-2 days'))."'");
}

add_action('wp_head', 'a3_pvc_include_style');
function a3_pvc_include_style(){
	echo '<style type="text/css">.pvc_clear{clear:both} .pvc_stats{background:url("'.A3_PVC_URL.'/chart-bar.png") no-repeat scroll 0 5px transparent !important;padding: 5px 5px 5px 25px !important;float:left;}</style>';
}
add_action('genesis_after_post_content', array('A3_PVC', 'genesis_pvc_stats_echo'));
//add_action('loop_end', array('A3_PVC', 'pvc_stats_echo'), 9);
add_filter('the_content', array('A3_PVC','pvc_stats_show'), 8);
add_filter('the_excerpt', array('A3_PVC','excerpt_pvc_stats_show'), 8);
//add_filter('get_the_excerpt', array('A3_PVC','excerpt_pvc_stats_show'), 8);

// Fixed for Wordpress SEO plugin
add_filter( 'wpseo_opengraph_desc', array( 'A3_PVC', 'fixed_wordpress_seo_plugin' ) );

// Check upgrade functions
add_action('plugins_loaded', 'pvc_lite_upgrade_plugin');
function pvc_lite_upgrade_plugin () {
	
	if(version_compare(get_option('a3_pvc_version'), '1.2') === -1){
		update_option('a3_pvc_version', '1.2');
		A3_PVC::upgrade_version_1_2();
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
	if(version_compare(get_option('a3_pvc_version'), '1.3.6') === -1){
		$pvc_settings = get_option( 'pvc_settings' );
		if ( isset( $pvc_settings['post_types'] ) && is_array( $pvc_settings['post_types'] ) && count( $pvc_settings['post_types'] ) > 0 ) {
			$post_types_new = array();
			foreach ( $pvc_settings['post_types'] as $post_type ) {
				$post_types_new[$post_type] = $post_type;
			}
			$pvc_settings['post_types'] = $post_types_new;
			update_option( 'pvc_settings', $pvc_settings );
		}
		update_option('a3_pvc_version', '1.3.6');
	}
	
	update_option('a3_pvc_version', '1.3.8');

}

?>