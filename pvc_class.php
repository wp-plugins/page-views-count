<?php
add_action('wp_head', 'a3_pvc_include_style');
function a3_pvc_include_style(){
	echo '<style type="text/css">.pvc_clear{clear:both} .pvc_stats{background:url("'.A3_PVC_URL.'/chart-bar.png") no-repeat scroll 0 5px transparent !important;padding: 5px 5px 5px 25px !important;float:left;}</style>';
}
add_action('genesis_after_post_content', array('A3_PVC', 'genesis_pvc_stats_echo'));
//add_action('loop_end', array('A3_PVC', 'pvc_stats_echo'), 9);
add_filter('the_content', array('A3_PVC','pvc_stats_show'), 8);
add_filter('the_excerpt', array('A3_PVC','excerpt_pvc_stats_show'), 8);
//add_filter('get_the_excerpt', array('A3_PVC','excerpt_pvc_stats_show'), 8);

add_action('plugin_action_links_'.A3_PVC_PLUGIN_NAME, array('A3_PVC', 'settings_plugin_links') );

// Add text on right of Visit the plugin on Plugin manager page
add_filter( 'plugin_row_meta', array('A3_PVC', 'plugin_extra_links'), 10, 2 );

class A3_PVC{
	function upgrade_version_1_2(){
		global $wpdb;
		$sql = "ALTER TABLE ". $wpdb->prefix . "pvc_total CHANGE `postnum` `postnum` VARCHAR( 255 ) NOT NULL";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE ". $wpdb->prefix . "pvc_daily CHANGE `postnum` `postnum` VARCHAR( 255 ) NOT NULL";
		$wpdb->query($sql);
	}
	function install_database(){
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
	
	function pvc_fetch_post_counts( $post_id ) {
		global $wpdb;
		$nowisnow = date('Y-m-d');
	
		$sql = $wpdb->prepare( "SELECT t.postcount AS total, d.postcount AS today FROM ". $wpdb->prefix . "pvc_total AS t
			LEFT JOIN ". $wpdb->prefix . "pvc_daily AS d ON t.postnum = d.postnum
			WHERE t.postnum = %s AND d.time = %s", $post_id, $nowisnow );
		return $wpdb->get_row($sql);
	}
	
	function pvc_fetch_post_total( $post_id ) {
		global $wpdb;
	
		$sql = $wpdb->prepare( "SELECT t.postcount AS total FROM ". $wpdb->prefix . "pvc_total AS t 
			WHERE t.postnum = %s", $post_id );
		return $wpdb->get_var($sql);
	}
	
	function pvc_stats_update($post_id) {
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
	function pvc_stats_counter($post_id) {
		global $wpdb;
		// get all the post view info to display
		$results = A3_PVC::pvc_fetch_post_counts( $post_id );
		// get the stats and
		$html = '<div class="pvc_clear"></div>';
		if($results){
			$stats_html = '<p class="pvc_stats">' . number_format($results->total) . '&nbsp;' .__('total views', 'pvc') . ', ' . number_format($results->today) . '&nbsp;' .__('views today', 'pvc') . '</p>';
		}else{
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
	
	function pvc_remove_stats($content) {
		remove_action('the_content', array('A3_PVC','pvc_stats_show'));
		return $content;
	}
	
	function pvc_stats_show($content){
		remove_action('loop_end', array('A3_PVC', 'pvc_stats_echo'));
		remove_action('genesis_after_post_content', array('A3_PVC', 'genesis_pvc_stats_echo'));
		global $post;
		$pvc_settings = get_option('pvc_settings');
		$post_type = get_post_type($post->ID);
		$args=array(
			  'public'   => true,
			  '_builtin' => false
			); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args, $output, $operator );
		if(in_array($post_type, (array)$pvc_settings['post_types'])){
			if(is_singular() || is_singular($post_types)){
				A3_PVC::pvc_stats_update($post->ID);
			}
			$content .= A3_PVC::pvc_stats_counter($post->ID);
		}
		return $content;
	}
	
	function excerpt_pvc_stats_show($excerpt){
		remove_action('loop_end', array('A3_PVC', 'pvc_stats_echo'));
		remove_action('genesis_after_post_content', array('A3_PVC', 'genesis_pvc_stats_echo'));
		global $post;
		$pvc_settings = get_option('pvc_settings');
		$post_type = get_post_type($post->ID);
		$args=array(
			  'public'   => true,
			  '_builtin' => false
			); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args, $output, $operator );
		if(in_array($post_type, (array)$pvc_settings['post_types'])){
			//A3_PVC::pvc_stats_update($post->ID);
			$excerpt .= A3_PVC::pvc_stats_counter($post->ID);
		}
		return $excerpt;
	}
	
	function pvc_stats_echo(){
		global $post;
		$pvc_settings = get_option('pvc_settings');
		$post_type = get_post_type($post->ID);
		$args=array(
			  'public'   => true,
			  '_builtin' => false
			); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args, $output, $operator );
		if(in_array($post_type, (array)$pvc_settings['post_types'])){
			//A3_PVC::pvc_stats_update($post->ID);
			echo A3_PVC::pvc_stats_counter($post->ID);
		}
	}
	
	function genesis_pvc_stats_echo(){
		remove_action('loop_end', array('A3_PVC', 'pvc_stats_echo'));
		global $post;
		$pvc_settings = get_option('pvc_settings');
		$post_type = get_post_type($post->ID);
		$args=array(
			  'public'   => true,
			  '_builtin' => false
			); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args, $output, $operator );
		if(in_array($post_type, (array)$pvc_settings['post_types'])){
			//A3_PVC::pvc_stats_update($post->ID);
			echo A3_PVC::pvc_stats_counter($post->ID);
		}
	}
	
	function custom_stats_echo($postid=0, $have_echo = 1){
		if($have_echo == 1)
			echo A3_PVC::pvc_stats_counter($postid);
		else
			return A3_PVC::pvc_stats_counter($postid);
	}
	
	function custom_stats_update_echo($postid=0, $have_echo=1){
		A3_PVC::pvc_stats_update($postid);
		if($have_echo == 1)
			echo A3_PVC::pvc_stats_counter($postid);
		else
			return A3_PVC::pvc_stats_counter($postid);
	}
	
	function settings_plugin_links($actions) {
		$actions = array_merge( array( 'settings' => '<a href="options-general.php?page=a3-pvc">' . __( 'Settings', 'pvc' ) . '</a>' ), $actions );
		
		return $actions;
	}
	
	function plugin_extra_links($links, $plugin_name) {
		if ( $plugin_name != A3_PVC_PLUGIN_NAME) {
			return $links;
		}
		$links[] = '<a href="http://docs.a3rev.com/user-guides/page-view-count/" target="_blank">'.__('Documentation', 'pvc').'</a>';
		return $links;
	}
}
?>