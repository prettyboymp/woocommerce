<?php
/**
 * WC_BIS_Noop class
 *
 * @package  WooCommerce Back In Stock Notifications
 * @since    9.9.0
 */

declare( strict_types=1 );

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Noop class.
 *
 * @since 9.9.0
 */
class WC_BIS_Noop {

	/**
	 * Logger instance.
	 *
	 * @var WC_Logger_Interface
	 */
	public static WC_Logger_Interface $logger;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->logger = wc_get_logger();
	}

	/**
	 * Handle method calls.
	 *
	 * @param string $method The method name.
	 * @param array  $args   The method arguments.
	 */
	public function __call( $method, $args ) {
		$this->logger->debug( 'Back In Stock Notifications are disabled and something tried to call its method ' . $method . '()' );
	}

	/**
	 * Handle static method calls.
	 *
	 * @param string $method The method name.
	 * @param array  $args   The method arguments.
	 */
	public static function __callStatic( $method, $args ) {
		self::$logger->debug( 'Back In Stock Notifications are disabled and something tried to call its static method ' . $method . '()' );
	}
}
