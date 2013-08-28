<?php
class A3_PVC_Settings
{
	public static function install_settings_default() {
		$pvc_settings = get_option('pvc_settings', array() );
		if ( count( $pvc_settings ) < 1 ) {
			$pvc_settings = array();
			$pvc_settings['post_types'] = array('post', 'page');
			
			update_option('pvc_settings', $pvc_settings);
		}
		if ( get_option('pvc_clean_on_deletion') == '' ) {
			update_option('pvc_clean_on_deletion', 0);
		}
	}
	
	public static function show_settings() {
		if(isset($_REQUEST['save_settings'])){
			$pvc_settings = get_option('pvc_settings');			
			update_option('pvc_settings', $_REQUEST);
			
			if ( isset($_REQUEST['pvc_clean_on_deletion']) ) {
				update_option('pvc_clean_on_deletion',  $_REQUEST['pvc_clean_on_deletion']);
			} else { 
				update_option('pvc_clean_on_deletion',  0);
				$uninstallable_plugins = (array) get_option('uninstall_plugins');
				unset($uninstallable_plugins[A3_PVC_PLUGIN_NAME]);
				update_option('uninstall_plugins', $uninstallable_plugins);
			}
		
			echo '<div id="message" class="updated fade"><p>'.__('Page Views Count successfully updated', 'pvc').'</p></div>';
		}
		$pvc_settings = get_option('pvc_settings');
		?>
        <style type="text/css">
		#a3rev_plugins_area { padding-right:470px; position:relative; min-height:550px; }
	   	#a3rev_plugins_notice { background:#FFFBCC; border:2px solid #E6DB55; -webkit-border-radius:10px;-moz-border-radius:10px;-o-border-radius:10px; border-radius: 10px; color: #555555; float: right; margin: 0px; padding: 0px 15px; position: absolute; text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8); width: 420px; right:0px; top:0px;}
        </style>
        <div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
				<h2><?php _e('Page Views Count Settings', 'pvc'); ?></h2>
                <div id="a3rev_plugins_area"><?php echo A3_PVC_Settings::other_plugins_notice(); ?>
				<form action="" method="post" name="settingfr" id="settingfr" enctype="multipart/form-data">
					<h3><?php _e('Post and Page Type', 'pvc'); ?></h3>
					<table class="form-table">
               	 		<tbody>  
                			<tr valign="top">
                  				<th scope="row"><label for="post_type_post"><?php _e('Posts', 'pvc'); ?></label></th>
                    			<td>
                                	<?php $checked = ( isset($pvc_settings['post_types']) && in_array('post', (array)$pvc_settings['post_types'])) ? " checked='checked' " : ""; ?>
                                    <input type="checkbox" name="post_types[]" id="post_type_post" value="post" class="" <?php echo $checked; ?>  /> <span class="description"><?php _e('All posts including posts extracts on category and tags Archives', 'pvc'); ?></span>
                          		</td>
							</tr>
                            <tr valign="top">
                  				<th scope="row"><label for="post_type_page"><?php _e('Pages', 'pvc'); ?></label></th>
                    			<td>
                                	<?php $checked = ( isset($pvc_settings['post_types']) && in_array('page', (array)$pvc_settings['post_types'])) ? " checked='checked' " : ""; ?>
                                    <input type="checkbox" name="post_types[]" id="post_type_page" value="page" class="" <?php echo $checked; ?>  />
                          		</td>
							</tr>          	
                		</tbody>
              		</table>
                    <h3><?php _e('Custom Post Types', 'pvc'); ?></h3>
					<table class="form-table">
               	 		<tbody>
                        <?php
						$post_types=get_post_types(array('public' => true, '_builtin' => false),'objects');
						foreach($post_types as $post_type => $post_type_data){
							$checked = ( isset($pvc_settings['post_types']) && in_array($post_type, (array)$pvc_settings['post_types'])) ? " checked='checked' " : "";
						?>
                			<tr valign="top">
                  				<th scope="row"><label for="post_type_<?php echo $post_type; ?>"><?php echo $post_type_data->labels->name; ?></label></th>
                    			<td>
                                    <input type="checkbox" name="post_types[]" id="post_type_<?php echo $post_type; ?>" value="<?php echo $post_type; ?>" class="" <?php echo $checked; ?>  /> <label for="post_type_<?php echo $post_type; ?>"></label><br />
                          		</td>
							</tr>
                		<?php } ?>
                        </tbody>
              		</table>
                    <h3><?php _e('House Keeping', 'pvc');?> :</h3>		
                    <table class="form-table">
                        <tr valign="top" class="">
                            <th class="titledesc" scope="row"><label for="pvc_clean_on_deletion"><?php _e('Clean up on Deletion', 'pvc');?></label></th>
                            <td class="forminp">
                                    <label>
                                    <input <?php checked( get_option('pvc_clean_on_deletion'), 1); ?> type="checkbox" value="1" id="pvc_clean_on_deletion" name="pvc_clean_on_deletion">
                                    <?php _e('Check this box and if you ever delete this plugin it will completely remove all tables and data it created, leaving no trace it was ever here.', 'pvc');?></label> <br>
                            </td>
                        </tr>
                    </table>
                    <h3><?php _e('Page Views Count Function', 'pvc'); ?></h3>
					<table class="form-table">
               	 		<tbody>
                        	<tr valign="top">
                  				<th scope="row" colspan="2"><?php _e("There are 2 functions that you can use to manually add Page Views count to any content or post type that is created by your theme or plugin that creates it's own table intead of using cutom post types", 'pvc'); ?>.</th>
							</tr>
                			<tr valign="top">
                  				<th scope="row"><?php _e('Single post,  page, object', 'pvc'); ?></th>
                    			<td>&lt;?php pvc_stats_update($postid); ?&gt;</td>
							</tr>
                            <tr valign="top">
                  				<th scope="row"><?php _e('Index pages', 'pvc'); ?></th>
                    			<td>&lt;?php pvc_stats($postid); ?&gt;</td>
							</tr>
                            <tr valign="top">
                  				<th scope="row" colspan="2"><?php _e('See', 'pvc'); ?> <a href="http://docs.a3rev.com/user-guides/page-view-count/" target="_blank"><?php _e('the plugins wiki', 'pvc'); ?></a> <?php _e('docs for detailed explanation on using the funtions', 'pvc'); ?></th>
							</tr>
                        </tbody>
              		</table>
            	<p class="submit">
              		<input type="submit" value="<?php _e('Save Changes', 'pvc'); ?>" class="button-primary" name="save_settings">
            	</p>
          	</form>
            </div>
		</div>
<?php	
	}
	
	public static function other_plugins_notice() {
		$html = '';
		$html .= '<div id="a3rev_plugins_notice">';
		$html .= '<a href="http://a3rev.com/shop/" target="_blank" style="float:right;margin-top:5px; margin-left:10px;" ><img src="'.A3_PVC_URL.'/a3logo.png" /></a>';
		$html .= '<h3>'.__('Help spread the Word about this plugin', 'pvc').'</h3>';
		$html .= '<p>'.__("Things you can do to help others find this plugin", 'pvc');
		$html .= '<ul style="padding-left:10px;">';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/page-views-count/" target="_blank">'.__('Rate this plugin 5', 'pvc').' <img src="'.A3_PVC_URL.'/stars.png" align="top" /> '.__('on WordPress.org', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/page-views-count/" target="_blank">'.__('Mark the plugin as a fourite', 'pvc').'</a></li>';
		$html .= '</ul>';
		$html .= '</p>';
		$html .= '<h3>'.__('More FREE a3rev WordPress Plugins', 'pvc').'</h3>';
		$html .= '<p>';
		$html .= '<ul style="padding-left:10px;">';
		$html .= '<li>* <a href="http://wordpress.org/plugins/contact-us-page-contact-people/" target="_blank">'.__('Contact Us page - Contact People', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/wp-email-template/" target="_blank">'.__('WordPress Email Template', 'pvc').'</a></li>';
		$html .= '</ul>';
		$html .= '</p>';
		$html .= '<h3>'.__('FREE a3rev WooCommerce Plugins', 'pvc').'</h3>';
		$html .= '<p>';
		$html .= '<ul style="padding-left:10px;">';
		$html .= '<li>* <a href="http://wordpress.org/plugins/woocommerce-product-sort-and-display/" target="_blank">'.__('WooCommerce Product Sort & Display', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/woocommerce-products-quick-view/" target="_blank">'.__('WooCommerce Products Quick View', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/woocommerce-dynamic-gallery/" target="_blank">'.__('WooCommerce Dynamic Products Gallery', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/woocommerce-predictive-search/" target="_blank">'.__('WooCommerce Predictive Search', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/woocommerce-compare-products/" target="_blank">'.__('WooCommerce Compare Products', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/woo-widget-product-slideshow/" target="_blank">'.__('WooCommerce Widget Product Slideshow', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/woocommerce-email-inquiry-cart-options/" target="_blank">'.__('WooCommerce Email Inquiry & Cart Options', 'pvc').'</a></li>';
		$html .= '</ul>';
		$html .= '</p>';
		
		$html .= '<h3>'.__('FREE a3rev WP e-Commerce Plugins', 'pvc').'</h3>';
		$html .= '<p>';
		$html .= '<ul style="padding-left:10px;">';
		$html .= '<li>* <a href="http://wordpress.org/plugins/wp-e-commerce-products-quick-view/" target="_blank">'.__('WP e-Commerce Products Quick View', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/wp-e-commerce-dynamic-gallery/" target="_blank">'.__('WP e-Commerce Dynamic Gallery', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/wp-e-commerce-predictive-search/" target="_blank">'.__('WP e-Commerce Predictive Search', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/wp-ecommerce-compare-products/" target="_blank">'.__('WP e-Commerce Compare Products', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/wp-e-commerce-catalog-visibility-and-email-inquiry/" target="_blank">'.__('WP e-Commerce Catalog Visibility & Email Inquiry', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/plugins/wp-e-commerce-grid-view/" target="_blank">'.__('WP e-Commerce Grid View', 'pvc').'</a></li>';
		$html .= '</ul>';
		$html .= '</p>';
		$html .= '<h3>'.__('Plugin Documentation', 'pvc').'</h3>';
		$html .= '<p>'.__('All of our plugins have comprehensive online documentation. Please refer to the plugins docs before raising a support request', 'pvc').'. <a href="http://docs.a3rev.com/user-guides/page-view-count/" target="_blank">'.__('Visit the a3rev wiki.', 'pvc').'</a></p>';
		$html .= '</div>';
		return $html;	
	}
}
?>