<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\Tests\Internal\StockNotifications\AsyncTasks;

use Automattic\WooCommerce\Internal\StockNotifications\AsyncTasks\NotificationsProcessor;
use Automattic\WooCommerce\Internal\StockNotifications\Notification;
use Automattic\WooCommerce\Internal\StockNotifications\Emails\EmailManager;
use Automattic\WooCommerce\Internal\StockNotifications\Enums\NotificationStatus;
use Automattic\WooCommerce\Enums\ProductStockStatus;
use Automattic\WooCommerce\Enums\ProductStatus;
use WC_Product;
use Exception;
use WC_Helper_Product;
use WC_Unit_Test_Case;

/**
 * Tests for NotificationsProcessor class
 */
class NotificationsProcessorTests extends WC_Unit_Test_Case {

	/**
	 * @var NotificationsProcessor
	 */
	private $sut;

	/**
	 * Email manager.
	 *
	 * @var EmailManager
	 */
	private $email_manager;

	/**
	 * Set up test case
	 */
	public function setUp(): void {
		parent::setUp();
		\WC()->queue()->cancel_all( NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS );
		$this->email_manager = new EmailManager();
		$this->sut           = new NotificationsProcessor();
		$this->sut->init( $this->email_manager );
	}

		/**
		 * Clean up after tests
		 */
	public function tearDown(): void {
		parent::tearDown();
		unset( $this->sut );
		// Clean up all notifications.
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wc_stock_notifications" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wc_stock_notificationmeta" );
	}

	/**
	 * Test schedule method creates new job when none exists
	 */
	public function test_schedule_creates_new_job_when_none_exists() {
		$product_id = 123;
		$result     = $this->sut->schedule( $product_id );

		$this->assertTrue( $result );
		$this->assertNotFalse(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product_id ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
	}

	/**
	 * Test schedule method doesn't create duplicate jobs
	 */
	public function test_schedule_prevents_duplicate_jobs() {
		$product_id = 123;

		$this->sut->schedule( $product_id );

		$result = $this->sut->schedule( $product_id );

		$this->assertFalse( $result );
	}

	/**
	 * Test that the processor handles exceptions gracefully
	 */
	public function test_schedule_next_batch() {
		$product_id = 123;
		$method     = $this->get_private_method( $this->sut, 'schedule_next_batch' );
		$result     = $method->invokeArgs( $this->sut, array( $product_id ) );

		$this->assertTrue( $result );
		$this->assertNotFalse(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product_id ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
	}

	/**
	 * Test parse_args method.
	 */
	public function test_parse_args() {
		$args   = array( 'product_id' => 123 );
		$method = $this->get_private_method( $this->sut, 'parse_args' );
		$result = $method->invokeArgs( $this->sut, array( $args ) );
		$this->assertEquals( 123, $result );

		$args = array( 'product_id' => 0 );
		$this->expectException( \Exception::class );
		$method->invokeArgs( $this->sut, array( $args ) );

		$args = array( 'product_id' => 'test' );
		$this->expectException( \Exception::class );
		$method->invokeArgs( $this->sut, array( $args ) );

		$args = array( 'product_id' => array() );
		$this->expectException( \Exception::class );
		$method->invokeArgs( $this->sut, array( $args ) );
	}

	/**
	 * Test parse_product method.
	 */
	public function test_parse_product() {
		$product = WC_Helper_Product::create_simple_product();
		$method  = $this->get_private_method( $this->sut, 'parse_product' );
		$result  = $method->invokeArgs( $this->sut, array( $product->get_id() ) );
		$this->assertInstanceOf( WC_Product::class, $result );

		$product_id = 0;
		$this->expectException( Exception::class );
		$method->invokeArgs( $this->sut, array( $product_id ) );

		$product_id = 'test';
		$this->expectException( Exception::class );
		$method->invokeArgs( $this->sut, array( $product_id ) );

		$product_id = array();
		$this->expectException( Exception::class );
		$method->invokeArgs( $this->sut, array( $product_id ) );
	}

	/**
	 * Test parse_cycle_state method.
	 */
	public function test_parse_cycle_state() {
		$product_id = 123;
		$method     = $this->get_private_method( $this->sut, 'parse_cycle_state' );
		$result     = $method->invokeArgs( $this->sut, array( $product_id ) );
		$this->assertArrayHasKey( 'cycle_start_time', $result );
		$this->assertArrayHasKey( 'total_count', $result );
		$this->assertArrayHasKey( 'sent_count', $result );
		$this->assertArrayHasKey( 'failed_count', $result );
		$this->assertArrayHasKey( 'skipped_count', $result );
		$this->assertArrayHasKey( 'duration', $result );

		$product_id = 0;
		$this->expectException( Exception::class );
		$method->invokeArgs( $this->sut, array( $product_id ) );
	}

	/**
	 * Test cycle state methods.
	 */
	public function test_cycle_state() {
		$product_id = 123;
		$method     = $this->get_private_method( $this->sut, 'parse_cycle_state' );
		$state      = $method->invokeArgs( $this->sut, array( $product_id ) );

		// Test that option is not saved upon initialization.
		$this->assertFalse( get_option( NotificationsProcessor::STATE_OPTION_PREFIX . $product_id ) );

		// Save state.
		$method = $this->get_private_method( $this->sut, 'save_cycle_state' );
		$method->invokeArgs( $this->sut, array( $product_id, $state ) );
		$this->assertNotFalse( get_option( NotificationsProcessor::STATE_OPTION_PREFIX . $product_id ) );

		// Test that option is saved upon completion.
		$method = $this->get_private_method( $this->sut, 'complete_cycle' );
		$method->invokeArgs( $this->sut, array( $product_id, $state ) );
		$this->assertFalse( get_option( NotificationsProcessor::STATE_OPTION_PREFIX . $product_id ) );
	}

	/**
	 * Test process_batch method on a simple product.
	 */
	public function test_process_batch_simple_product() {
		$product      = WC_Helper_Product::create_simple_product();
		$notification = new Notification();
		$notification->set_product_id( $product->get_id() );
		$notification->set_user_id( 1 );
		$notification->set_status( NotificationStatus::ACTIVE );
		$notification->save();

		$this->sut->process_batch( array( 'product_id' => $product->get_id() ) );
		$this->assertFalse( get_option( NotificationsProcessor::STATE_OPTION_PREFIX . $product->get_id() ) );

		// Test that the notification is sent.
		$notification = new Notification( $notification->get_id() );
		$this->assertEquals( NotificationStatus::SENT, $notification->get_status() );
		$this->assertEqualsWithDelta( time(), $notification->get_date_notified()->getTimestamp(), 10 );
		$this->assertEqualsWithDelta( time(), $notification->get_date_last_attempt()->getTimestamp(), 10 );

		// Test there is no next job.
		$this->assertEmpty(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product->get_id() ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
	}

	public function test_process_batch_variation_product() {
		$product   = WC_Helper_Product::create_variation_product();
		$variation = $product->get_children()[0];
		$variation = wc_get_product( $variation );
		$this->assertEquals( ProductStockStatus::IN_STOCK, $variation->get_stock_status() );
		$variation->set_manage_stock( true );
		$variation->set_stock_quantity( 10 );
		$variation->save();
		$this->assertEquals( ProductStockStatus::IN_STOCK, $variation->get_stock_status() );

		$notification = new Notification();
		$notification->set_product_id( $variation->get_id() );
		$notification->set_user_id( 1 );
		$notification->set_status( NotificationStatus::ACTIVE );
		$notification->save();

		$this->sut->process_batch( array( 'product_id' => $variation->get_id() ) );

		// Refetch notification.
		$notification = new Notification( $notification->get_id() );
		$this->assertEquals( NotificationStatus::SENT, $notification->get_status() );
	}

	public function test_process_batch_variable() {
		$variable = WC_Helper_Product::create_variation_product();
		$variable->set_manage_stock( true );
		$variable->set_stock_quantity( 10 );
		$variable->save();
		$this->assertEquals( true, $variable->get_manage_stock() );
		$this->assertEquals( 'instock', $variable->get_stock_status() );

		$variations     = $variable->get_children();
		$last_variation = null;
		foreach ( $variations as $variation ) {
			$variation = wc_get_product( $variation );
			$variation->set_manage_stock( true );
			$this->assertEquals( 'parent', $variation->get_manage_stock() );
			$last_variation = $variation;
		}

		$notification = new Notification();
		$notification->set_product_id( $variable->get_id() );
		$notification->set_user_id( 1 );
		$notification->set_status( NotificationStatus::ACTIVE );
		$notification->save();

		$notification_on_variation = new Notification();
		$notification_on_variation->set_product_id( $last_variation->get_id() );
		$notification_on_variation->set_user_id( 1 );
		$notification_on_variation->set_status( NotificationStatus::ACTIVE );
		$notification_on_variation->save();

		$this->sut->process_batch( array( 'product_id' => $variable->get_id() ) );

		// Refetch notifications.
		$notification              = new Notification( $notification->get_id() );
		$notification_on_variation = new Notification( $notification_on_variation->get_id() );
		$this->assertEquals( NotificationStatus::SENT, $notification->get_status() );
		$this->assertEquals( NotificationStatus::SENT, $notification_on_variation->get_status() ); // @todo: fix this.
	}

	/**
	 * Test process_batch method bail out when product is not in stock.
	 */
	public function test_process_batch_bail_out_when_product_is_not_in_stock() {
		$product = WC_Helper_Product::create_simple_product();
		$product->set_stock_status( ProductStockStatus::OUT_OF_STOCK );
		$product->save();

		$notification = new Notification();
		$notification->set_product_id( $product->get_id() );
		$notification->set_user_id( 1 );
		$notification->set_status( NotificationStatus::ACTIVE );
		$notification->save();

		$this->sut->process_batch( array( 'product_id' => $product->get_id() ) );
		$this->assertFalse( get_option( NotificationsProcessor::STATE_OPTION_PREFIX . $product->get_id() ) );

		// Test that the notification is not sent.
		$notification = new Notification( $notification->get_id() );
		$this->assertEquals( NotificationStatus::ACTIVE, $notification->get_status() );
		$this->assertEmpty( $notification->get_date_notified() );
		$this->assertEmpty( $notification->get_date_last_attempt() );

		// Test there is no next job.
		$this->assertEmpty(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product->get_id() ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
	}

	/**
	 * Test process_batch method bail out when product is not published.
	 */
	public function test_process_batch_bail_out_when_product_is_not_published() {
		$product = WC_Helper_Product::create_simple_product();
		$product->set_status( ProductStatus::DRAFT );
		$product->save();

		$notification = new Notification();
		$notification->set_product_id( $product->get_id() );
		$notification->set_user_email( 'test@test.com' ); // Signup as guest.
		$notification->set_status( NotificationStatus::ACTIVE );
		$notification->save();

		$this->sut->process_batch( array( 'product_id' => $product->get_id() ) );
		$this->assertFalse( get_option( NotificationsProcessor::STATE_OPTION_PREFIX . $product->get_id() ) );

		// Test that the notification is not sent.
		$notification = new Notification( $notification->get_id() );
		$this->assertEquals( NotificationStatus::ACTIVE, $notification->get_status() );
		$this->assertEmpty( $notification->get_date_notified() );
		$this->assertEqualsWithDelta( time(), $notification->get_date_last_attempt()->getTimestamp(), 5 );

		// Test there is no next job.
		$this->assertEmpty(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product->get_id() ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
	}

	/**
	 * Test process_batch method on a product with a pendingnotification.
	 */
	public function test_process_batch_pending() {
		$product      = WC_Helper_Product::create_simple_product();
		$notification = new Notification();
		$notification->set_product_id( $product->get_id() );
		$notification->set_user_id( 1 );
		$notification->set_status( NotificationStatus::PENDING );
		$notification->save();

		$this->sut->process_batch( array( 'product_id' => $product->get_id() ) );
		$this->assertFalse( get_option( NotificationsProcessor::STATE_OPTION_PREFIX . $product->get_id() ) );

		// Test that the notification is not sent.
		$notification = new Notification( $notification->get_id() );
		$this->assertEquals( NotificationStatus::PENDING, $notification->get_status() );
		$this->assertEmpty( $notification->get_date_notified() );
		$this->assertEmpty( $notification->get_date_last_attempt() );

		// Test there is no next job.
		$this->assertEmpty(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product->get_id() ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
	}

	/**
	 * Test process_batch method on a product with a notification that is cancelled.
	 */
	public function test_process_batch_cancelled() {
		$product      = WC_Helper_Product::create_simple_product();
		$notification = new Notification();
		$notification->set_product_id( $product->get_id() );
		$notification->set_user_id( 1 );
		$notification->set_status( NotificationStatus::CANCELLED );
		$notification->save();

		$this->sut->process_batch( array( 'product_id' => $product->get_id() ) );

		// Test that the notification is not sent.
		$notification = new Notification( $notification->get_id() );
		$this->assertEquals( NotificationStatus::CANCELLED, $notification->get_status() );
		$this->assertEmpty( $notification->get_date_notified() );
		$this->assertEmpty( $notification->get_date_last_attempt() );

		// Test there is no next job.
		$this->assertEmpty(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product->get_id() ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
	}

	/**
	 * Test process_batch method on a product with a notification that is throttled.
	 */
	public function test_process_batch_throttled() {
		$product      = WC_Helper_Product::create_simple_product();
		$notification = new Notification();
		$notification->set_product_id( $product->get_id() );
		$notification->set_user_email( 'test@test.com' ); // Signup as guest.
		$notification->set_status( NotificationStatus::ACTIVE );
		$notification->save();

		$this->sut->process_batch( array( 'product_id' => $product->get_id() ) );

		// Test that the notification is sent the first time.
		$notification = new Notification( $notification->get_id() );
		$this->assertEquals( NotificationStatus::SENT, $notification->get_status() );
		$this->assertEqualsWithDelta( time(), $notification->get_date_notified()->getTimestamp(), 5 );
		$this->assertEqualsWithDelta( time(), $notification->get_date_last_attempt()->getTimestamp(), 5 );

		// Manual Re-activation.
		$notification->set_status( NotificationStatus::ACTIVE );
		$notification->save();

		$this->sut->process_batch( array( 'product_id' => $product->get_id() ) );

		// Test that the notification is throttled for the second time.
		$notification = new Notification( $notification->get_id() );
		$this->assertEqualsWithDelta( time(), $notification->get_date_last_attempt()->getTimestamp(), 5 );
		$this->assertEquals( NotificationStatus::ACTIVE, $notification->get_status() );

		// Test there is no next job.
		$this->assertEmpty(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product->get_id() ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
	}

	/**
	 * Test process_batch method with multiple batches running in sequence.
	 */
	public function test_process_batch_multiple_batches() {
		tests_add_filter(
			'woocommerce_stock_notifications_batch_size',
			function () {
				return 1;
			}
		);

		$product      = WC_Helper_Product::create_simple_product();
		$notification = new Notification();
		$notification->set_product_id( $product->get_id() );
		$notification->set_user_email( 'test@test.com' ); // Signup as guest.
		$notification->set_status( NotificationStatus::ACTIVE );
		$notification->save();
		$notification2 = new Notification();
		$notification2->set_product_id( $product->get_id() );
		$notification2->set_user_email( 'test2@test.com' );
		$notification2->set_status( NotificationStatus::ACTIVE );
		$notification2->save();

		$this->sut->process_batch( array( 'product_id' => $product->get_id() ) );

		// Test that the notification is sent.
		$notification = new Notification( $notification->get_id() );
		$this->assertEquals( NotificationStatus::SENT, $notification->get_status() );
		$this->assertEqualsWithDelta( time(), $notification->get_date_notified()->getTimestamp(), 5 );
		$this->assertEqualsWithDelta( time(), $notification->get_date_last_attempt()->getTimestamp(), 5 );

		// Check the second notification is not sent.
		$notification2 = new Notification( $notification2->get_id() );
		$this->assertEquals( NotificationStatus::ACTIVE, $notification2->get_status() );
		$this->assertEmpty( $notification2->get_date_notified() );
		$this->assertEmpty( $notification2->get_date_last_attempt() );

		// Test there is a next job.
		$this->assertNotEmpty(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product->get_id() ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
		WC()->queue()->cancel_all( NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS );

		// Run the next job.
		$this->sut->process_batch( array( 'product_id' => $product->get_id() ) );

		// Test that the notification is sent.
		$notification2 = new Notification( $notification2->get_id() );
		$this->assertEquals( NotificationStatus::SENT, $notification2->get_status() );
		$this->assertEqualsWithDelta( time(), $notification2->get_date_notified()->getTimestamp(), 5 );

		// Since max_batch_size is 1, there will be another job scheduled to wrap up the cycle.
		$this->assertNotEmpty(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product->get_id() ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
		WC()->queue()->cancel_all( NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS );

		// Run the next job.
		$this->sut->process_batch( array( 'product_id' => $product->get_id() ) );

		// Test there is no next job.
		$this->assertEmpty(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product->get_id() ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
	}

	/**
	 * Test process_batch method on a product with no notifications.
	 */
	public function test_process_batch_no_notifications() {
		$product = WC_Helper_Product::create_simple_product();

		$this->sut->process_batch( array( 'product_id' => $product->get_id() ) );

		$this->assertFalse( get_option( NotificationsProcessor::STATE_OPTION_PREFIX . $product->get_id() ) );

		// Test there is no next job.
		$this->assertEmpty(
			WC()->queue()->get_next(
				NotificationsProcessor::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => array( 'product_id' => $product->get_id() ) ),
				NotificationsProcessor::AS_JOB_GROUP
			)
		);
	}

	// @todo: higher level tests by running the queue jobs manually.
	// @todo: test multiple products in a single request and multiple batches.

	/**
	 * Get private method.
	 */
	private function get_private_method( $object, $method_name ) {
		$method = new \ReflectionMethod( $object, $method_name );
		$method->setAccessible( true );
		return $method;
	}
}
