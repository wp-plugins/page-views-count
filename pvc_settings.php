<?php
class A3_PVC_Settings{
	function install_settings_default(){
		$pvc_settings = get_option('pvc_settings');
		if(empty($pvc_settings) || !is_array($pvc_settings)){
			$pvc_settings = array();
			$pvc_settings['post_types'] = array('post', 'page');
		}
	}
	
	function show_settings(){
		if(isset($_REQUEST['save_settings'])){
			$pvc_settings = get_option('pvc_settings');			
			update_option('pvc_settings', $_REQUEST);
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
                                	<?php $checked = (in_array('post', (array)$pvc_settings['post_types'])) ? " checked='checked' " : ""; ?>
                                    <input type="checkbox" name="post_types[]" id="post_type_post" value="post" class="" <?php echo $checked; ?>  /> <span class="description"><?php _e('All posts including posts extracts on category and tags Archives', 'pvc'); ?></span>
                          		</td>
							</tr>
                            <tr valign="top">
                  				<th scope="row"><label for="post_type_page"><?php _e('Pages', 'pvc'); ?></label></th>
                    			<td>
                                	<?php $checked = (in_array('page', (array)$pvc_settings['post_types'])) ? " checked='checked' " : ""; ?>
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
							$checked = (in_array($post_type, (array)$pvc_settings['post_types'])) ? " checked='checked' " : "";
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
	
	function other_plugins_notice() {
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
		$html .= '<h3>'.__('More A3 Quality Plugins', 'pvc').'</h3>';
		$html .= '<p>'.__('Below is a list of the A3 plugins that are available for free download from wordpress.org', 'pvc').'</p>';
		$html .= '<h3>'.__('WordPress Plugins', 'pvc').'</h3>';
		$html .= '<p>';
		$html .= '<ul style="padding-left:10px;">';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/wp-email-template/" target="_blank">'.__('WordPress Email Template', 'pvc').'</a></li>';
		$html .= '</ul>';
		$html .= '</p>';
		$html .= '<h3>'.__('WooCommerce Plugins', 'pvc').'</h3>';
		$html .= '<p>';
		$html .= '<ul style="padding-left:10px;">';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/woocommerce-dynamic-gallery/" target="_blank">'.__('WooCommerce Dynamic Products Gallery', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/woocommerce-predictive-search/" target="_blank">'.__('WooCommerce Predictive Search', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/woocommerce-compare-products/" target="_blank">'.__('WooCommerce Compare Products', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/woo-widget-product-slideshow/" target="_blank">'.__('WooCommerce Widget Product Slideshow', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/woocommerce-email-inquiry-cart-options/" target="_blank">'.__('WooCommerce Email Inquiry & Cart Options', 'pvc').'</a></li>';
		$html .= '</ul>';
		$html .= '</p>';
		
		$html .= '<h3>'.__('WP e-Commerce Plugins', 'pvc').'</h3>';
		$html .= '<p>';
		$html .= '<ul style="padding-left:10px;">';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/wp-e-commerce-dynamic-gallery/" target="_blank">'.__('WP e-Commerce Dynamic Gallery', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/wp-e-commerce-predictive-search/" target="_blank">'.__('WP e-Commerce Predictive Search', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/wp-ecommerce-compare-products/" target="_blank">'.__('WP e-Commerce Compare Products', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/wp-e-commerce-catalog-visibility-and-email-inquiry/" target="_blank">'.__('WP e-Commerce Catalog Visibility & Email Inquiry', 'pvc').'</a></li>';
		$html .= '<li>* <a href="http://wordpress.org/extend/plugins/wp-e-commerce-grid-view/" target="_blank">'.__('WP e-Commerce Grid View', 'pvc').'</a></li>';
		$html .= '</ul>';
		$html .= '</p>';
		$html .= '<h3>'.__('Plugin Documentation', 'pvc').'</h3>';
		$html .= '<p>'.__('All of our plugins have comprehensive online documentation. Please refer to the plugins docs before raising a support request', 'pvc').'. <a href="http://docs.a3rev.com/" target="_blank">'.__('Visit the a3rev wiki.', 'pvc').'</a></p>';
		$html .= '</div>';
		return $html;	
	}
}
?>