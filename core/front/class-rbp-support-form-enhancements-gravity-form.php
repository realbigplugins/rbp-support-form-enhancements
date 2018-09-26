<?php
/**
 * Class RBP_Support_Form_Enhancements_Gravity_Form
 *
 * Makes adjustments to our Gravity Form
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class RBP_Support_Form_Enhancements_Gravity_Form {

	/**
	 * RBP_Support_Form_Enhancements_Gravity_Form constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		
		add_filter( 'gform_pre_render_3', array( $this, 'populate_downloads_list' ), 10, 3 );
			
	}
	
	/**
	 * Populate our "Which Plugin?" dropdown with whatever current Plugins are on the site, automatically
	 * Private Downloads will show if an Admin is logged in
	 * 
	 * @param		array   $form         The current form to be filtered
	 * @param		boolean $ajax         Is AJAX enabled
	 * @param		array   $field_values An array of dynamic population parameter keys with their corresponding values to be populated
	 *                                                                                                               * 
	 * @access		public
	 * @since		1.0.0
	 * @return		array   Modified Form
	 */
	public function populate_downloads_list( $form, $ajax, $field_values ) {
		
		foreach ( $form['fields'] as &$field ) {
			
			if ( $field['inputName'] == 'extension' ) {
				
				global $post;
				
				$downloads = new WP_Query( array(
					'post_type' => 'download',
					'posts_per_page' => -1,
					'orderby' => 'title',
					'order' => 'ASC',
					'status' => 'publish',
					'meta_query' => array(
						'relation' => 'OR',
						array(
							'key' => '_edd_product_type',
							'compare' => 'NOT EXISTS',
						),
						array(
							'key' => '_edd_product_type',
							'value' => 'bundle',
							'compare' => '!=',
						),
					),
				) );
				
				if ( $downloads->have_posts() ) {
					
					$field->choices = array();
					
					// Hyphens to emdashes in Titles
					remove_filter( 'the_title', 'wptexturize' );
					
					while ( $downloads->have_posts() ) {
						
						$downloads->the_post();
						
						$field->choices[] = array(
							'text' => get_the_title(),
							'value' => get_the_title(),
							'isSelected' => ( isset( $_GET['extension'] ) && urldecode( $_GET['extension'] ) == get_the_title() ) ? true : false,
							'price' => '',
						);
						
					}
					
					add_filter( 'the_title', 'wptexturize' );
					
					wp_reset_postdata();
					
				}
				
				break;
				
			}
			
		}
		
		return $form;
		
	}
	
}

$instance = new RBP_Support_Form_Enhancements_Gravity_Form();