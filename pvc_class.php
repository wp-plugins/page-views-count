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
	
	public static function pvc_fetch_posts_stats( $post_ids ) {
		global $wpdb;
		$nowisnow = date('Y-m-d');
		
		if ( !is_array( $post_ids ) ) $post_ids = array( $post_ids );
		
		$sql = $wpdb->prepare( "SELECT t.postnum AS post_id, t.postcount AS total, d.postcount AS today FROM ". $wpdb->prefix . "pvc_total AS t
			LEFT JOIN ". $wpdb->prefix . "pvc_daily AS d ON t.postnum = d.postnum
			WHERE t.postnum IN ( ".implode( ',', $post_ids )." ) AND d.time = %s", $nowisnow );
		return $wpdb->get_results($sql);
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
		$results = $wpdb->query( $wpdb->prepare( "UPDATE ". $wpdb->prefix . "pvc_total SET postcount = postcount+1 WHERE postnum = '%s' LIMIT 1", $post_id ) );
		
		// if it doesn't exist, then insert two new records
		// one in the total views, another in today's views
		if ($results == 0) {
			$wpdb->query( $wpdb->prepare( "INSERT INTO ". $wpdb->prefix . "pvc_total (postnum, postcount) VALUES ('%s', 1)", $post_id ) );
			$wpdb->query( $wpdb->prepare ( "INSERT INTO ". $wpdb->prefix . "pvc_daily (time, postnum, postcount) VALUES ('%s', '%s', 1)", $nowisnow, $post_id ) );
		// post exists so let's just update the counter
		} else {
			$results2 = $wpdb->query( $wpdb->prepare ( "UPDATE ". $wpdb->prefix . "pvc_daily SET postcount = postcount+1 WHERE time = '%s' AND postnum = '%s' LIMIT 1", $nowisnow, $post_id ) );
			// insert a new record since one hasn't been created for current day
			if ($results2 == 0)
				$wpdb->query( $wpdb->prepare( "INSERT INTO ". $wpdb->prefix . "pvc_daily (time, postnum, postcount) VALUES ('%s', '%s', 1)", $nowisnow, $post_id ) );
		}
		
		// get all the post view info so we can update meta fields
		//$row = A3_PVC::pvc_fetch_post_counts( $post_id );
	}
	
	public static function pvc_get_stats($post_id) {
		global $wpdb;
		
		$output_html = '';
		// get all the post view info to display
		$results = A3_PVC::pvc_fetch_post_counts( $post_id );
		// get the stats and
		if ( $results ){
			$output_html .= number_format( $results->total ) . '&nbsp;' .__('total views', 'pvc') . ', ' . number_format( $results->today ) . '&nbsp;' .__('views today', 'pvc');
		} else {
			$total = A3_PVC::pvc_fetch_post_total( $post_id );
			if ( $total > 0 ) {
				$output_html .= number_format( $total ) . '&nbsp;' .__('total views', 'pvc') . ', ' .__('no views today', 'pvc');
			} else {
				$output_html .=  __('No views yet', 'pvc');
			}
		}
		$output_html = apply_filters( 'pvc_filter_get_stats', $output_html, $post_id );
		
		return $output_html;
	}
	
	// get the total page views and daily page views for the post
	public static function pvc_stats_counter( $post_id, $increase_views = false ) {
		global $wpdb;
		global $pvc_settings;
		
		$exclude_ids = array(3630, 3643, 5520, 3642, 3632, 3633, 3628, 2102, 6793);
		if (in_array($post_id, (array)$exclude_ids)) return '';
		
		$load_by_ajax_update_class = '';
		if ( $increase_views ) $load_by_ajax_update_class = 'pvc_load_by_ajax_update';

		// get the stats and
		$html = '<div class="pvc_clear"></div>';
		
		if ( $pvc_settings['enable_ajax_load'] == 'yes' ) {
			$stats_html = '<p id="pvc_stats_'.$post_id.'" class="pvc_stats '.$load_by_ajax_update_class.'" element-id="'.$post_id.'"><img src="'.A3_PVC_URL.'/ajax-loader.gif" border=0 /></p>';
		} else {
			$stats_html = '<p class="pvc_stats" element-id="'.$post_id.'">' . A3_PVC::pvc_get_stats( $post_id ) . '</p>';
		}
		
		$html .= apply_filters( 'pvc_filter_stats', $stats_html, $post_id );
		$html .= '<div class="pvc_clear"></div>';
		return $html;
	}
	
	public static function pvc_backbone_load_stats() {
		$post_ids	= $_REQUEST['post_ids'];
		
		$data = array();
		$ids = array();
		if ( is_array( $post_ids ) && count( $post_ids ) > 0 ) {
			foreach ( $post_ids as $post_id => $post_data ) {
				$ids[] = $post_id;
				if ( isset( $post_data['ask_update'] ) && $post_data['ask_update'] == 'true' ) {
					A3_PVC::pvc_stats_update( $post_id );	
				}
			}
			$results = A3_PVC::pvc_fetch_posts_stats( $ids );
			if ( $results ) {
				foreach( $results as $result ) {
					$data[$result->post_id] = array (
						'post_id'		=> (int) $result->post_id,
						'total_view' 	=> (int) $result->total,
						'today_view' 	=> (int) $result->today
					);
					$ids = array_diff( $ids, array( $result->post_id ) );
				}
			}
			
			foreach ( $ids as $post_id ) {
				$total = A3_PVC::pvc_fetch_post_total( $post_id );
				$data[$post_id] = array (
					'post_id'		=> (int) $post_id,
					'total_view' 	=> (int) $total,
					'today_view' 	=> 0
				);
			}
		}
		header( 'Content-Type: application/json', true, 200 );
		die( json_encode( $data ) );
	}
		
	public static function register_plugin_scripts() {
		global $pvc_settings;
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		
		wp_enqueue_style( 'a3-pvc-style', A3_PVC_CSS_URL . '/style'.$suffix.'.css', false, '1.0.6' );
		
		if ( $pvc_settings['enable_ajax_load'] != 'yes' ) return;
		
	?>
    <!-- PVC Template -->
    <script type="text/template" id="pvc-stats-view-template">
	<% if ( total_view > 0 ) { %>
		<%= total_view %> <%= total_view > 1 ? "<?php _e('total views', 'pvc'); ?>" : "<?php _e('total view', 'pvc'); ?>" %>,
		<% if ( today_view > 0 ) { %>
			<%= today_view %> <%= today_view > 1 ? "<?php _e('views today', 'pvc'); ?>" : "<?php _e('view today', 'pvc'); ?>" %>
		<% } else { %>
		<?php _e('no views today', 'pvc'); ?>
		<% } %> 
	<% } else { %>
	<?php _e('No views yet', 'pvc'); ?>
	<% } %> 
	</script>
    <?php
		wp_enqueue_script( 'a3-pvc-backbone', A3_PVC_JS_URL . '/pvc.backbone'.$suffix.'.js', array( 'jquery', 'backbone', 'underscore' ), '1.0.0' );
		wp_localize_script( 'a3-pvc-backbone', 'vars', array( 'api_url' => admin_url( 'admin-ajax.php' ) ) );
		//wp_localize_script( 'a3-pvc-backbone', 'vars', array( 'api_url' => add_query_arg( array( 'pvc-api' => 'pvc_backbone_load_stats' ) , get_option('siteurl') . '/' ) ) );
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
		if ( ! $post ) return;
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
				if ( ! isset( $pvc_settings['enable_ajax_load'] ) || $pvc_settings['enable_ajax_load'] != 'yes' ) {
					A3_PVC::pvc_stats_update($post->ID);
				}
				$content .= A3_PVC::pvc_stats_counter($post->ID, true );
			} else {
				$content .= A3_PVC::pvc_stats_counter($post->ID);
			}
		}
		return $content;
	}
	
	public static function excerpt_pvc_stats_show($excerpt){
		remove_action('loop_end', array('A3_PVC', 'pvc_stats_echo'));
		remove_action('genesis_after_post_content', array('A3_PVC', 'genesis_pvc_stats_echo'));
		global $post;
		if ( ! $post ) return;
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
		$output = '';
		
		$pvc_settings = get_option('pvc_settings', array() );
		if ( ! isset( $pvc_settings['enable_ajax_load'] ) || $pvc_settings['enable_ajax_load'] != 'yes' ) {
			A3_PVC::pvc_stats_update($postid);
		}
		
		$output .= A3_PVC::pvc_stats_counter($postid, true );
		
		if ( $have_echo == 1 )
			echo $output;
		else
			return $output;
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
		$html .= '<a href="http://a3rev.com/shop/" target="_blank" style="float:right;margin-top:5px; margin-left:10px;" ><div class="a3-plugin-ui-icon a3-plugin-ui-a3-rev-logo"></div></a>';
		$html .= '<h3>'.__('Help spread the Word about this plugin', 'pvc').'</h3>';
		$html .= '<p>&nbsp;</p>';
		$html .= '<h3>'.__('View this plugins', 'wp_email_template').' <a href="http://docs.a3rev.com/user-guides/page-view-count/" target="_blank">'.__('documentation', 'pvc').'</a></h3>';
		$html .= '<h3>'.__('Visit this plugins', 'wp_email_template').' <a href="http://wordpress.org/support/plugin/page-views-count/" target="_blank">'.__('support forum', 'pvc').'</a></h3>';
		
		$html .= '<h3>'.__('Other FREE a3rev WordPress Plugins', 'pvc').'</h3>';
		$html .= '<p>';
		$html .= '<ul style="padding-left:10px;">';
		$html .= '<li>* <a href="http://wordpress.org/plugins/a3-responsive-slider/" target="_blank">'.__('a3 Responsive Slider', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/contact-us-page-contact-people/" target="_blank">'.__('Contact Us page - Contact People', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/wp-email-template/" target="_blank">'.__('WordPress Email Template', 'pvc').'</a></li>';
		$html .= '</ul>';
		$html .= '</p>';
		$html .= '<p>'.__("View all", 'pvc').' <a href="http://profiles.wordpress.org/a3rev/" target="_blank">'.__("17 a3rev plugins", 'pvc').'</a> '.__('on the WordPress repository', 'pvc').'</p>';
		
		return $html;	
	}
	
	public static function a3_wp_admin() {
		wp_enqueue_style( 'a3rev-wp-admin-style', A3_PVC_CSS_URL . '/a3_wp_admin.css' );
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