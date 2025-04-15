<?php // phpcs:ignore Suin.Classes.PSR4

declare( strict_types = 1 );
namespace Automattic\WooCommerce\Tests\Internal\DataStores\StockNotifications;

use Automattic\WooCommerce\Internal\StockNotifications\Notification;

/**
 * NotificationTests data tests.
 */
class NotificationTests extends \WC_Unit_Test_Case {

	/**
	 * Test the product getter.
	 */
	public function test_product_getter() {

		$product      = \WC_Helper_Product::create_simple_product();
		$product2     = \WC_Helper_Product::create_simple_product();
		$notification = new Notification();
		$notification->set_product_id( $product->get_id() );
		$notification->save();

		$notification_product = $notification->get_product();
		$this->assertInstanceOf( \WC_Product::class, $notification_product );
		$this->assertEquals( $product->get_id(), $notification_product->get_id() );

		$notification->set_product_id( $product2->get_id() );
		$notification->save();

		$notification_product = $notification->get_product();
		$this->assertEquals( $product2->get_id(), $notification_product->get_id() );
	}
}