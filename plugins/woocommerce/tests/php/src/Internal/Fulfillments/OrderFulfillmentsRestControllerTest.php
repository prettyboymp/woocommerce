<?php
declare( strict_types = 1 );

namespace Automattic\WooCommerce\Tests\Internal\Fulfillments;

use Automattic\WooCommerce\Internal\Fulfillments\OrderFulfillmentsRestController;
use Automattic\WooCommerce\Tests\Internal\Fulfillments\Helpers\FulfillmentsHelper;
use WC_Helper_Order;
use WC_Order;
use WC_REST_Unit_Test_Case;
use WP_Http;
use WP_REST_Request;

/**
 * Class OrderFulfillmentsRestControllerTest
 *
 * @package Automattic\WooCommerce\Tests\Internal\Orders
 */
class OrderFulfillmentsRestControllerTest extends WC_REST_Unit_Test_Case {
	/**
	 * @var OrderFulfillmentsRestController
	 */
	private OrderFulfillmentsRestController $controller;

	/**
	 * Array of created orders' ID's. Keeping it to be deleted in tearDownAfterClass.
	 *
	 * @var array
	 */
	private static array $created_order_ids = array();

	/**
	 * Created user ID for testing purposes.
	 *
	 * @var int
	 */
	private static int $created_user_id = 1000;

	/**
	 * Setup test case.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->controller = new OrderFulfillmentsRestController();
		$this->controller->register_routes();
	}

	/**
	 * Initializes the test environment before all tests on this file are run.
	 */
	public static function setupBeforeClass(): void {
		parent::setupBeforeClass();

		self::$created_user_id = wp_create_user( 'test_user', 'password', 'nonadmin@example.com' );

		for ( $order_number = 1; $order_number <= 10; $order_number++ ) {
			$order                     = WC_Helper_Order::create_order( get_current_user_id() );
			self::$created_order_ids[] = $order->get_id();
			for ( $fulfillment = 1; $fulfillment <= 10; $fulfillment++ ) {
				FulfillmentsHelper::create_fulfillment(
					array(
						'entity_type' => WC_Order::class,
						'entity_id'   => $order->get_id(),
					)
				);
			}
		}
	}

	/**
	 * Destroys the test environment after all tests on this file are run.
	 */
	public static function tearDownAfterClass(): void {
		// Delete the created orders and their fulfillments.
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wc_order_fulfillments;" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wc_order_fulfillment_meta;" );
		foreach ( self::$created_order_ids as $order_id ) {
			WC_Helper_Order::delete_order( $order_id );
		}

		// Delete the created user.
		wp_delete_user( self::$created_user_id );
		parent::tearDownAfterClass();
	}

	/**
	 * Test the get_items method.
	 */
	public function test_get_fulfillments_nominal() {
		// Do the request for an order which the current user owns.
		$request  = new WP_REST_Request( 'GET', '/wc/v3/orders/' . self::$created_order_ids[0] . '/fulfillments' );
		$response = $this->server->dispatch( $request );

		// Check the response.
		$this->assertEquals( 200, $response->get_status() );
		$this->assertIsArray( $response->get_data() );
		$this->assertArrayHasKey( 'fulfillments', $response->get_data() );
		$fulfillments = $response->get_data()['fulfillments'];
		$this->assertIsArray( $fulfillments );
		$this->assertCount( 10, $fulfillments );
		$this->assertEquals( 10, count( $fulfillments ) );

		foreach ( $fulfillments as $fulfillment ) {
			$this->assertEquals( WC_Order::class, $fulfillment['entity_type'] );
			$this->assertEquals( self::$created_order_ids[0], $fulfillment['entity_id'] );
		}
	}

	/**
	 * Test the get_items method with an invalid order ID.
	 */
	public function test_get_fulfillments_invalid_order_id() {
		// Do the request with an invalid order ID.
		$request  = new WP_REST_Request( 'GET', '/wc/v3/orders/999999/fulfillments' );
		$response = $this->server->dispatch( $request );

		// Check the response.
		$this->assertEquals( WP_Http::NOT_FOUND, $response->get_status() );
		$this->assertEquals( 'Invalid order ID.', $response->get_data()['message'] );
	}

	/**
	 * Test the get_items method with a non-matching user.
	 */
	public function test_get_fulfillments_invalid_user() {
		// Prepare the test environment.
		$current_user = wp_get_current_user();
		$this->assertEquals( 0, $current_user->ID );
		wp_set_current_user( self::$created_user_id );
		$this->assertEquals( self::$created_user_id, get_current_user_id() );
		$this->assertFalse( current_user_can( 'manage_woocommerce' ) ); // phpcs:ignore WordPress.WP.Capabilities.Unknown

		// Do the request as a non-admin user, for another user's order.
		$request  = new WP_REST_Request( 'GET', '/wc/v3/orders/' . self::$created_order_ids[0] . '/fulfillments' );
		$response = $this->server->dispatch( $request );

		// Check the response.
		$this->assertEquals( WP_Http::FORBIDDEN, $response->get_status() );
		$this->assertEquals(
			array(
				'code'    => 'woocommerce_rest_cannot_view',
				'message' => 'Sorry, you cannot view resources.',
				'data'    => array( 'status' => WP_Http::FORBIDDEN ),
			),
			$response->get_data()
		);

		// Clean up the test environment.
		wp_set_current_user( $current_user->ID );
	}

	/**
	 * Test the get_items method with an administrator.
	 */
	public function test_get_fulfillments_with_admin() {
		// Prepare the test environment.
		$current_user = wp_get_current_user();
		$this->assertEquals( 0, $current_user->ID );
		$this->assertFalse( current_user_can( 'manage_woocommerce' ) ); // phpcs:ignore WordPress.WP.Capabilities.Unknown
		wp_set_current_user( 1 );
		$this->assertTrue( current_user_can( 'manage_woocommerce' ) ); // phpcs:ignore WordPress.WP.Capabilities.Unknown
		$this->assertEquals( 1, get_current_user_id() );

		// Do the request as an admin user, for another user's order.
		$request  = new WP_REST_Request( 'GET', '/wc/v3/orders/' . self::$created_order_ids[0] . '/fulfillments' );
		$response = $this->server->dispatch( $request );

		// Check the response.
		$this->assertEquals( WP_Http::OK, $response->get_status() );
		$this->assertIsArray( $response->get_data() );
		$this->assertArrayHasKey( 'fulfillments', $response->get_data() );

		// Clean up the test environment.
		wp_set_current_user( $current_user->ID );
	}
}
