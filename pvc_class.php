<?php
class A3_PVC
{
	public static function upgrade_version_1_2(){
		global $wpdb;
		$sql = "ALTER TABLE ". $wpdb->prefix . "pvc_total CHANGE `postnum` `postnum` VARCHAR( 255 ) NOT NULL";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE ". $wpdb->prefix . "pvc_daily CHANGE `postnum` `postnum` VARCHAR( 255 ) NOT NULL";
		$wpdb->query($sql);
	}
	public static function install_database(){
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( !empty($wpdb->charset) ) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( !empty($wpdb->collate) ) $collate .= " COLLATE $wpdb->collate";
		}
		
		$sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "pvc_daily" ." (
         `id` mediumint(9) NOT NULL AUTO_INCREMENT,
		 `time` date DEFAULT '0000-00-00' NOT NULL,
		 `postnum` varchar(255) NOT NULL,
		 `postcount` int DEFAULT '0' NOT NULL,
		 UNIQUE KEY id (id)) $collate;";

		$wpdb->query($sql);
	
	
		$sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "pvc_total" ." (
			 `id` mediumint(9) NOT NULL AUTO_INCREMENT,
			 `postnum` varchar(255) NOT NULL,
			 `postcount` int DEFAULT '0' NOT NULL,
			 UNIQUE KEY id (id)) $collate;";
	
		$wpdb->query($sql);
	}
	
	public static function pvc_fetch_post_counts( $post_id ) {
		global $wpdb;
		$nowisnow = date('Y-m-d');
	
		$sql = $wpdb->prepare( "SELECT t.postcount AS total, d.postcount AS today FROM ". $wpdb->prefix . "pvc_total AS t
			LEFT JOIN ". $wpdb->prefix . "pvc_daily AS d ON t.postnum = d.postnum
			WHERE t.postnum = %s AND d.time = %s", $post_id, $nowisnow );
		return $wpdb->get_row($sql);
	}
	
	public static function pvc_fetch_post_total( $post_id ) {
		global $wpdb;
	
		$sql = $wpdb->prepare( "SELECT t.postcount AS total FROM ". $wpdb->prefix . "pvc_total AS t 
			WHERE t.postnum = %s", $post_id );
		return $wpdb->get_var($sql);
	}
	
	public static function pvc_stats_update($post_id) {
		global $wpdb;
		
		// get the local time based off WordPress setting
		$nowisnow = date('Y-m-d');
		
		// first try and update the existing total post counter
		$results = $wpdb->query("UPDATE ". $wpdb->prefix . "pvc_total SET postcount = postcount+1 WHERE postnum = '$post_id' LIMIT 1");
		
		// if it doesn't exist, then insert two new records
		// one in the total views, another in today's views
		if ($results == 0) {
			$wpdb->query("INSERT INTO ". $wpdb->prefix . "pvc_total (postnum, postcount) VALUES ('$post_id', 1)");
			$wpdb->query("INSERT INTO ". $wpdb->prefix . "pvc_daily (time, postnum, postcount) VALUES ('$nowisnow', '$post_id', 1)");
		// post exists so let's just update the counter
		} else {
			$results2 = $wpdb->query("UPDATE ". $wpdb->prefix . "pvc_daily SET postcount = postcount+1 WHERE time = '$nowisnow' AND postnum = '$post_id' LIMIT 1");
			// insert a new record since one hasn't been created for current day
			if ($results2 == 0)
				$wpdb->query("INSERT INTO ". $wpdb->prefix . "pvc_daily (time, postnum, postcount) VALUES ('$nowisnow', '$post_id', 1)");
		}
		
		// get all the post view info so we can update meta fields
		$row = A3_PVC::pvc_fetch_post_counts( $post_id );
	}
	
	// get the total page views and daily page views for the post
	public static function pvc_stats_counter($post_id) {
		global $wpdb;
		$exclude_ids = array(3630, 3643, 5520, 3642, 3632, 3633, 3628, 2102, 6793);
		if (in_array($post_id, (array)$exclude_ids)) return '';
		// get all the post view info to display
		$results = A3_PVC::pvc_fetch_post_counts( $post_id );
		// get the stats and
		$html = '<div class="pvc_clear"></div>';
		if($results){
			$stats_html = '<p class="pvc_stats">' . number_format($results->total) . '&nbsp;' .__('total views', 'pvc') . ', ' . number_format($results->today) . '&nbsp;' .__('views today', 'pvc') . '</p>';
		}else{
			$stats_html = '';
			$total = A3_PVC::pvc_fetch_post_total($post_id);
			if($total > 0){
				$stats_html .= '<p class="pvc_stats">' . number_format($total) . '&nbsp;' .__('total views', 'pvc') . ', ' .__('no views today', 'pvc') . '</p>';
			}else{
				$stats_html .= '<p class="pvc_stats">' . __('No views yet', 'pvc') . '</p>';
			}
		}
		$html .= apply_filters('pvc_filter_stats', $stats_html);
		$html .= '<div class="pvc_clear"></div>';
		return $html;
	}
	
	public static function fixed_wordpress_seo_plugin( $ogdesc = '' ) {
		if ( function_exists( 'wpseo_set_value' ) ) {
			global $post;
			$postid = $post->ID;
			wpseo_set_value( 'opengraph-description', $ogdesc, $postid );	
		}
		return $ogdesc;
	}
	
	public static function pvc_remove_stats($content) {
		remove_action('the_content', array('A3_PVC','pvc_stats_show'));
		return $content;
	}
	
	public static function pvc_stats_show($content){
		remove_action('loop_end', array('A3_PVC', 'pvc_stats_echo'));
		remove_action('genesis_after_post_content', array('A3_PVC', 'genesis_pvc_stats_echo'));
		global $post;
		$pvc_settings = get_option('pvc_settings', array() );
		$exclude_ids = array(3630, 3643, 5520, 3642, 3632, 3633, 3628, 2102, 6793);
		$post_type = get_post_type($post->ID);
		$args=array(
			  'public'   => true,
			  '_builtin' => false
			); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args, $output, $operator );
		if(!in_array($post->ID, (array) $exclude_ids) && isset($pvc_settings['post_types']) && in_array($post_type, (array)$pvc_settings['post_types'])){
			if(is_singular() || is_singular($post_types)){
				A3_PVC::pvc_stats_update($post->ID);
			}
			$content .= A3_PVC::pvc_stats_counter($post->ID);
		}
		return $content;
	}
	
	public static function excerpt_pvc_stats_show($excerpt){
		remove_action('loop_end', array('A3_PVC', 'pvc_stats_echo'));
		remove_action('genesis_after_post_content', array('A3_PVC', 'genesis_pvc_stats_echo'));
		global $post;
		$pvc_settings = get_option('pvc_settings', array() );
		$exclude_ids = array(3630, 3643, 5520, 3642, 3632, 3633, 3628, 2102, 6793);
		$post_type = get_post_type($post->ID);
		$args=array(
			  'public'   => true,
			  '_builtin' => false
			); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args, $output, $operator );
		if(!in_array($post->ID, (array) $exclude_ids) && isset($pvc_settings['post_types']) && in_array($post_type, (array)$pvc_settings['post_types'])){
			//A3_PVC::pvc_stats_update($post->ID);
			$excerpt .= A3_PVC::pvc_stats_counter($post->ID);
		}
		return $excerpt;
	}
	
	public static function pvc_stats_echo(){
		global $post;
		$pvc_settings = get_option('pvc_settings', array() );
		$post_type = get_post_type($post->ID);
		$args=array(
			  'public'   => true,
			  '_builtin' => false
			); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args, $output, $operator );
		if( isset($pvc_settings['post_types']) && in_array($post_type, (array)$pvc_settings['post_types'])){
			//A3_PVC::pvc_stats_update($post->ID);
			echo A3_PVC::pvc_stats_counter($post->ID);
		}
	}
	
	public static function genesis_pvc_stats_echo(){
		remove_action('loop_end', array('A3_PVC', 'pvc_stats_echo'));
		global $post;
		$pvc_settings = get_option('pvc_settings', array() );
		$post_type = get_post_type($post->ID);
		$args=array(
			  'public'   => true,
			  '_builtin' => false
			); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args, $output, $operator );
		if( isset($pvc_settings['post_types']) && in_array($post_type, (array)$pvc_settings['post_types'])){
			//A3_PVC::pvc_stats_update($post->ID);
			echo A3_PVC::pvc_stats_counter($post->ID);
		}
	}
	
	public static function custom_stats_echo($postid=0, $have_echo = 1){
		if($have_echo == 1)
			echo A3_PVC::pvc_stats_counter($postid);
		else
			return A3_PVC::pvc_stats_counter($postid);
	}
	
	public static function custom_stats_update_echo($postid=0, $have_echo=1){
		A3_PVC::pvc_stats_update($postid);
		if($have_echo == 1)
			echo A3_PVC::pvc_stats_counter($postid);
		else
			return A3_PVC::pvc_stats_counter($postid);
	}
	
	public static function pvc_check_exclude($postid=0) {
		global $post;
		if ($postid == 0 || $postid == '') $postid = $post->ID;
		$pvc_settings = get_option('pvc_settings', array() );
		$post_type = get_post_type($postid);
		if ($post_type == false) return false;
		
		$exclude_ids = array(3630, 3643, 5520, 3642, 3632, 3633, 3628, 2102, 6793);
		if ( isset($pvc_settings['post_types']) && in_array($postid, $exclude_ids)) return true;
		
		return false;
	}
	
	public static function settings_plugin_links($actions) {
		$actions = array_merge( array( 'settings' => '<a href="options-general.php?page=a3-pvc">' . __( 'Settings', 'pvc' ) . '</a>' ), $actions );
		
		return $actions;
	}
	
	public static function plugin_extension_start() {
		global $wp_pvc_admin_init;
		
		$wp_pvc_admin_init->plugin_extension_start();
	}
	
	public static function plugin_extension_end() {
		global $wp_pvc_admin_init;
		
		$wp_pvc_admin_init->plugin_extension_end();
	}
	
	public static function plugin_extension() {
		$html = '';
		$html .= '<a href="http://a3rev.com/shop/" target="_blank" style="float:right;margin-top:5px; margin-left:10px;" ><img src="'.A3_PVC_URL.'/a3logo.png" /></a>';
		$html .= '<h3>'.__('Help spread the Word about this plugin', 'pvc').'</h3>';
		$html .= '<p>&nbsp;</p>';
		$html .= '<h3>'.__('View this plugins', 'wp_email_template').' <a href="http://docs.a3rev.com/user-guides/page-view-count/" target="_blank">'.__('documentation', 'wp_email_template').'</a></h3>';
		$html .= '<h3>'.__('Visit this plugins', 'wp_email_template').' <a href="http://wordpress.org/support/plugin/page-views-count/" target="_blank">'.__('support forum', 'wp_email_template').'</a></h3>';
		
		$html .= '<h3>'.__('Other FREE a3rev WordPress Plugins', 'wp_email_template').'</h3>';
		$html .= '<p>';
		$html .= '<ul style="padding-left:10px;">';
		$html .= '<li>* <a href="http://wordpress.org/plugins/contact-us-page-contact-people/" target="_blank">'.__('Contact Us page - Contact People', 'wp_email_template').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/wp-email-template/" target="_blank">'.__('WordPress Email Template', 'wp_email_template').'</a></li>';
		$html .= '</ul>';
		$html .= '</p>';
		$html .= '<p>'.__("View all", 'wp_email_template').' <a href="http://profiles.wordpress.org/a3rev/" target="_blank">'.__("16 a3rev plugins", 'wp_email_template').'</a> '.__('on the WordPress repository', 'wp_email_template').'</p>';
		
		return $html;	
	}
	
	public static function plugin_extra_links($links, $plugin_name) {
		if ( $plugin_name != A3_PVC_PLUGIN_NAME) {
			return $links;
		}
		$links[] = '<a href="http://docs.a3rev.com/user-guides/page-view-count/" target="_blank">'.__('Documentation', 'pvc').'</a>';
		$links[] = '<a href="http://wordpress.org/support/plugin/page-views-count/" target="_blank">'.__('Support', 'pvc').'</a>';
		return $links;
	}
}
?>