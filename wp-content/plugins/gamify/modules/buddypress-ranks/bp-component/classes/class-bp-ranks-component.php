<?php

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Class GFY_BP_Ranks_BP_Component
 */
class GFY_BP_Ranks_BP_Component extends BP_Component {
	
	/**
	 * Start the component creation process
	 * GFY_BP_Ranks_BP_Component constructor.
	 */
	function __construct() {
		parent::start(
			GFY_BP_Ranks_BP_Component_Helper::get_id(),
			GFY_BP_Ranks_BP_Component_Helper::get_title(),
			dirname( __DIR__ ),
			array(
				'adminbar_myaccount_order'  => 90
			)
		);
	}
	
	/**
	 * Include files
	 *
	 * @param array $includes
	 */
	public function includes( $includes = array() ) {
		$includes = array(
			'classes' . DIRECTORY_SEPARATOR . 'class-bp-ranks-component-notification.php',
		);

		parent::includes( $includes );
	}
	
	/**
	 * Setup globals
	 *
	 * @param array $args
	 */
	public function setup_globals( $args = array() ) {
		parent::setup_globals( array(
			'notification_callback' => class_exists( 'GFY_BP_Ranks_Component_Notification' ) ?
										array(
											GFY_BP_Ranks_Component_Notification::get_instance(),
											'format_notification'
										) : ''
		) );
	}

}