<?php
/*
Plugin Name: Page Views Count
Description: Show front end users all time views and views today on posts, pages, index pages and custom post types with the Page Views Count Plugin. Use the Page Views Count function to add page views to any content type or object created by your theme or plugins.
Version: 1.0.7
Requires at least: 3.7
Tested up to: 4.0
Author: A3 Revolution
Author URI: http://a3rev.com
License: A "Slug" license name e.g. GPL2
*/
?>
<?php
define('A3_PVC_FOLDER', dirname(plugin_basename(__FILE__)));
define('A3_PVC_DIR', WP_CONTENT_DIR . '/plugins/' . A3_PVC_FOLDER);
define('A3_PVC_PLUGIN_NAME', plugin_basename(__FILE__));
define('A3_PVC_URL', untrailingslashit(plugins_url('/', __FILE__)));
define('A3_PVC_CSS_URL', A3_PVC_URL . '/assets/css');
define('A3_PVC_JS_URL', A3_PVC_URL . '/assets/js');

// API Class
//include_once( 'api/pvc-api.php' );

include ('admin/admin-ui.php');
include ('admin/admin-interface.php');

include ('admin/admin-pages/admin-pvc-page.php');

include ('admin/admin-init.php');

include ("pvc_class.php");

include ('admin/plugin-init.php');

/**
 * Process when plugin is activated
 */
register_activation_hook(__FILE__, 'pvc_install');

/**
 * Process when plugin is deactivated
 */
register_deactivation_hook(__FILE__, 'pvc_deactivation');

function pvc_uninstall()
{
    if (get_option('pvc_clean_on_deletion') == 1) {
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
if (get_option('pvc_clean_on_deletion') == 1) {
    register_uninstall_hook(__FILE__, 'pvc_uninstall');
}

function pvc_stats($postid, $have_echo = 1)
{
    return A3_PVC::custom_stats_echo($postid, $have_echo);
}
function pvc_stats_update($postid, $have_echo = 1)
{
    return A3_PVC::custom_stats_update_echo($postid, $have_echo);
}

function pvc_check_exclude($postid    = 0)
{
    return A3_PVC::pvc_check_exclude($postid);
}
?>