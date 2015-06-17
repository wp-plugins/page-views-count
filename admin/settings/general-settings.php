<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WP PVC General Settings

TABLE OF CONTENTS

- var parent_tab
- var subtab_data
- var option_name
- var form_key
- var position
- var form_fields
- var form_messages

- __construct()
- subtab_init()
- set_default_settings()
- get_settings()
- subtab_data()
- add_subtab()
- settings_form()
- init_form_fields()

-----------------------------------------------------------------------------------*/

class WP_PVC_General_Settings extends WP_PVC_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'general';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = 'pvc_settings';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'pvc_settings';
	
	/**
	 * @var string
	 * You can change the order show of this sub tab in list sub tabs
	 */
	private $position = 1;
	
	/**
	 * @var array
	 */
	public $form_fields = array();
	
	/**
	 * @var array
	 */
	public $form_messages = array();
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		//$this->init_form_fields();
		//$this->subtab_init();
		
		$this->form_messages = array(
				'success_message'	=> __( 'Page View Count Settings successfully saved.', 'pvc' ),
				'error_message'		=> __( 'Error: Page View Count Settings can not save.', 'pvc' ),
				'reset_message'		=> __( 'Page View Count Settings successfully reseted.', 'pvc' ),
			);
			
		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
		
		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_init' , array( $this, 'clean_on_deletion' ) );
		
		add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );
		
		add_action( $this->plugin_name . '_settings_' . 'pvc_page_view_count_function' . '_before', array( $this, 'page_view_count_function_content' ) );
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* subtab_init() */
	/* Sub Tab Init */
	/*-----------------------------------------------------------------------------------*/
	public function subtab_init() {
		
		add_filter( $this->plugin_name . '-' . $this->parent_tab . '_settings_subtabs_array', array( $this, 'add_subtab' ), $this->position );
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* set_default_settings()
	/* Set default settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function set_default_settings() {
		global $wp_pvc_admin_interface;
		$this->init_form_fields();
		$wp_pvc_admin_interface->reset_settings( $this->form_fields, $this->option_name, false );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* clean_on_deletion()
	/* Process when clean on deletion option is un selected */
	/*-----------------------------------------------------------------------------------*/
	public function clean_on_deletion() {
		if ( ( isset( $_POST['bt_save_settings'] ) || isset( $_POST['bt_reset_settings'] ) ) && get_option( 'pvc_clean_on_deletion' ) == 0  )  {
			$uninstallable_plugins = (array) get_option('uninstall_plugins');
			unset($uninstallable_plugins[A3_PVC_PLUGIN_NAME]);
			update_option('uninstall_plugins', $uninstallable_plugins);
		}
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* get_settings()
	/* Get settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function get_settings() {
		global $wp_pvc_admin_interface;
		$this->init_form_fields();
		$wp_pvc_admin_interface->get_settings( $this->form_fields, $this->option_name );
	}
	
	/**
	 * subtab_data()
	 * Get SubTab Data
	 * =============================================
	 * array ( 
	 *		'name'				=> 'my_subtab_name'				: (required) Enter your subtab name that you want to set for this subtab
	 *		'label'				=> 'My SubTab Name'				: (required) Enter the subtab label
	 * 		'callback_function'	=> 'my_callback_function'		: (required) The callback function is called to show content of this subtab
	 * )
	 *
	 */
	public function subtab_data() {
		
		$subtab_data = array( 
			'name'				=> 'general',
			'label'				=> __( 'General', 'pvc' ),
			'callback_function'	=> 'wp_pvc_general_settings_form',
		);
		
		if ( $this->subtab_data ) return $this->subtab_data;
		return $this->subtab_data = $subtab_data;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* add_subtab() */
	/* Add Subtab to Admin Init
	/*-----------------------------------------------------------------------------------*/
	public function add_subtab( $subtabs_array ) {
	
		if ( ! is_array( $subtabs_array ) ) $subtabs_array = array();
		$subtabs_array[] = $this->subtab_data();
		
		return $subtabs_array;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* settings_form() */
	/* Call the form from Admin Interface
	/*-----------------------------------------------------------------------------------*/
	public function settings_form() {
		global $wp_pvc_admin_interface;
		$this->init_form_fields();
		
		$output = '';
		$output .= $wp_pvc_admin_interface->admin_forms( $this->form_fields, $this->form_key, $this->option_name, $this->form_messages );
		
		return $output;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* init_form_fields() */
	/* Init all fields of this form */
	/*-----------------------------------------------------------------------------------*/
	public function init_form_fields() {
		
  		// Define settings			
     	$this->form_fields = array(
		
			array(
            	'name' 		=> __( 'Page Views Count Load', 'pvc' ),
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Ajax Load', 'pvc' ),
				'desc' 		=> __( 'ON to load page views counter on front end by ajax event (recommended). Prevents caching plugins and CDNs from caching the count. If using caching you must clear the cache to see changes after turning this setting ON or OFF.', 'pvc' ),
				'id' 		=> 'enable_ajax_load',
				'type' 		=> 'onoff_checkbox',
				'default' 	=> 'no',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'pvc' ),
				'unchecked_label' 	=> __( 'OFF', 'pvc' ),
			),
		
			array(
            	'name' 		=> __( 'Activate on Posts and Pages', 'pvc' ),
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Posts', 'pvc' ),
				'desc' 		=> __( 'All posts including posts extracts on category and tags Archives', 'pvc' ),
				'id' 		=> 'post_types[post]',
				'type' 		=> 'onoff_checkbox',
				'default' 	=> 'post',
				'checked_value'		=> 'post',
				'unchecked_value'	=> '',
				'checked_label'		=> __( 'ON', 'pvc' ),
				'unchecked_label' 	=> __( 'OFF', 'pvc' ),
			),
			array(  
				'name' 		=> __( 'Pages', 'pvc' ),
				'id' 		=> 'post_types[page]',
				'type' 		=> 'onoff_checkbox',
				'default' 	=> 'page',
				'checked_value'		=> 'page',
				'unchecked_value'	=> '',
				'checked_label'		=> __( 'ON', 'pvc' ),
				'unchecked_label' 	=> __( 'OFF', 'pvc' ),
			),
        );
		
		$post_types = get_post_types( array( 'public' => true, '_builtin' => false ) , 'objects' );
		
		if ( is_array( $post_types ) && count( $post_types ) > 0 ) {
			$form_fields_custom_posts = array();
			$form_fields_custom_posts[] = array(
            	'name' 		=> __( 'Activate on these Custom Post Types', 'pvc' ),
                'type' 		=> 'heading',
           	);
			foreach ( $post_types as $post_type => $post_type_data ) {
				$form_fields_custom_posts[] = array(  
					'name' 		=> $post_type_data->labels->name,
					'id' 		=> 'post_types['.$post_type.']',
					'type' 		=> 'onoff_checkbox',
					'default' 	=> '',
					'checked_value'		=> $post_type,
					'unchecked_value'	=> '',
					'checked_label'		=> __( 'ON', 'pvc' ),
					'unchecked_label' 	=> __( 'OFF', 'pvc' ),
				);
			}
			
			$this->form_fields = array_merge( $this->form_fields, $form_fields_custom_posts );
		}
		
		$this->form_fields = array_merge( $this->form_fields, array(
			
			array(
            	'name' 		=> __( 'House Keeping :', 'pvc' ),
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Clean up on Deletion', 'pvc' ),
				'desc' 		=> __( "On deletion (not deactivate) the plugin will completely remove all tables and data it created, leaving no trace it was ever here.", 'pvc' ),
				'id' 		=> 'pvc_clean_on_deletion',
				'type' 		=> 'onoff_checkbox',
				'default'	=> '1',
				'separate_option'	=> true,
				'checked_label'		=> __( 'ON', 'pvc' ),
				'unchecked_label' 	=> __( 'OFF', 'pvc' ),
			),
			
			array(
				'id'		=> 'pvc_page_view_count_function',
                'type' 		=> 'heading',
           	),
        ) );
		
		$this->form_fields = apply_filters( $this->option_name . '_settings_fields', $this->form_fields );
	}
	
	public function page_view_count_function_content() {
	?>
    			<h3><?php _e('Page Views Count Function', 'pvc'); ?></h3>
					<table class="form-table">
               	 		<tbody>
                        	<tr valign="top">
                  				<th scope="row" colspan="2"><?php _e("There are 2 functions that you can use to manually add Page Views Count to any content or post type that is created by your theme or plugin that creates it's own table instead of using custom post types", 'pvc'); ?>.</th>
							</tr>
                			<tr valign="top">
                  				<th scope="row"><?php _e('Single post,  page, object', 'pvc'); ?></th>
                    			<td>
                                <p style="margin-bottom:10px;">&lt;?php pvc_stats_update( $postid, 1 ); ?&gt; <br /><span class="description"><?php _e( 'Increase Page Views Count and echo stats of this post', 'pvc' ); ?></span></p>
                                <p>&lt;?php pvc_stats_update( $postid, 0 ); ?&gt; <br /><span class="description"><?php _e( 'Increase Page Views Count and return stats of this post', 'pvc' ); ?></span></p></td>
							</tr>
                            <tr valign="top">
                  				<th scope="row"><?php _e('Index pages', 'pvc'); ?></th>
                    			<td>
                                <p style="margin-bottom:10px;">&lt;?php pvc_stats( $postid, 1 ); ?&gt; <br /><span class="description"><?php _e( 'Echo stats of this post', 'pvc' ); ?></span></p>
                                <p>&lt;?php pvc_stats( $postid, 0 ); ?&gt; <br /><span class="description"><?php _e( 'Return stats of this post', 'pvc' ); ?></span></p>
                                </td>
							</tr>
                        </tbody>
              		</table>
    <?php	
	}
}

global $wp_pvc_general_settings;
$wp_pvc_general_settings = new WP_PVC_General_Settings();

/** 
 * wp_pvc_general_settings_form()
 * Define the callback function to show subtab content
 */
function wp_pvc_general_settings_form() {
	global $wp_pvc_general_settings;
	$wp_pvc_general_settings->settings_form();
}

?>