<?php
/**
 * Provides helper functions.
 *
 * @since	  1.0.0
 *
 * @package	RBP_Support_Form_Enhancements
 * @subpackage RBP_Support_Form_Enhancements/core
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since		1.0.0
 *
 * @return		RBP_Support_Form_Enhancements
 */
function RBPSUPPORTFORMENHANCEMENTS() {
	return RBP_Support_Form_Enhancements::instance();
}