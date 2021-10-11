<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class GFY_Admin_Notifier {
	
	const ERROR     = 'error';
	const WARNING   = 'warning';
	const SUCCESS   = 'success';
	const INFO      = 'info';
	
	/**
	 * Holds current instance
	 * @var null
	 */
	private static $_instance = null;
	
	/**
	 * Get instance
	 * @return GFY_Admin_Notifier|null
	 */
	public static function get_instance() {
		
		if ( null == static::$_instance ) {
			static::$_instance = new GFY_Admin_Notifier();
		}
		
		return static::$_instance;
		
	}

	/**
	 * Holds set notifications
	 * @var array
	 */
	private $notifications = array();
	
	/**
	 * GFY_Admin_Notifier constructor.
	 */
	private function __construct() {
		$this->setup_actions();
	}
	
	/**
	 * A dummy magic method to prevent GFY_Admin_Notifier from being cloned.
	 *
	 */
	public function __clone() {
		throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
	}
	
	/**
	 * Setup actions
	 */
	private function setup_actions() {
		add_action( 'admin_notices', array( $this, 'render_notification' ) );
	}
	
	/**
	 * Render notifications
	 */
	public function render_notification() {
		if( empty( $this->notifications ) ) {
			return;
		}
		
		foreach( $this->notifications as $notification ) {
			$classes = array(
				sprintf( 'notice-%s', $notification['level'] ),
			);
			if( $notification['dismissible'] ) {
				$classes[] = 'is-dismissible';
			}
			
			printf( '<div class="notice %s"><p>%s</p></div>', implode( ' ', $classes ), $notification['text'] );
		}
	}
	
	/**
	 * Add notification
	 * @param $text
	 * @param bool $dismissible
	 * @param string $level
	 *
	 * @return bool
	 */
	public function add_notification( $text, $dismissible = true, $level = GFY_Admin_Notifier::WARNING ) {
		
		if( did_action( 'admin_notices' ) ) {
			gfy_doing_it_wrong( __FUNCTION__, __( 'Late to setup admin notifications.', 'gamify' ), '1.0.0' );

			return false;
		}
			
		$this->notifications[] = array(
			'text'        => $text,
			'level'       => $level,
			'dismissible' => $dismissible
		);
		return true;
	}
	
}

GFY_Admin_Notifier::get_instance();