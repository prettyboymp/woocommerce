<?php // phpcs:ignore Suin.Classes.PSR4

declare( strict_types = 1 );
namespace Automattic\WooCommerce\Tests\Internal\DataStores\StockNotifications;

use Automattic\WooCommerce\Internal\StockNotifications\Notification;
use Automattic\WooCommerce\Internal\DataStores\StockNotifications\StockNotificationsDataStore;

/**
 * Class StockNotificationsDataStoreTests.
 */
class StockNotificationsDataStoreTests extends \WC_Unit_Test_Case {

	/**
	 * Tear down the test.
	 */
	public function tearDown(): void {
		parent::tearDown();
		// Clean up all notifications.
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wc_stock_notifications" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wc_stock_notificationmeta" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wc_stock_notifications_logs" );
	}

	/**
	 * Test that the stock notification data store is registered.
	 */
	public function test_stock_notification_data_store_is_registered() {
		$store = new \WC_Data_Store( 'stock_notification' );
		$this->assertTrue( is_callable( array( $store, 'read' ) ) );
		$this->assertEquals( StockNotificationsDataStore::class, $store->get_current_class_name() );
	}

	/**
	 * Test creating a notification with all properties.
	 */
	public function test_create_notification_with_all_properties() {
		$notification = new Notification();

		// Set all properties.
		$notification->set_product_id( 1 );
		$notification->set_user_id( 1 );
		$notification->set_user_email( 'test@test.com' );
		$notification->set_status( 'active' );
		$notification->set_date_created( '2024-01-01 00:00:00' );
		$notification->set_date_modified( '2024-01-01 00:00:00' );
		$notification->set_date_subscribed( '2024-01-01 00:00:00' );
		$notification->set_date_notified( '2024-01-01 00:00:00' );
		$notification->set_is_queued( true );

		$notification->save();

		// Verify all properties were saved correctly.
		$this->assertEquals( 1, $notification->get_id() );
		$this->assertEquals( 1, $notification->get_product_id() );
		$this->assertEquals( 1, $notification->get_user_id() );
		$this->assertEquals( 'test@test.com', $notification->get_user_email() );
		$this->assertEquals( 'active', $notification->get_status() );
		$this->assertEquals( '2024-01-01 00:00:00', $notification->get_date_created()->format( 'Y-m-d H:i:s' ) );
		$this->assertEquals( '2024-01-01 00:00:00', $notification->get_date_modified()->format( 'Y-m-d H:i:s' ) );
		$this->assertEquals( '2024-01-01 00:00:00', $notification->get_date_subscribed()->format( 'Y-m-d H:i:s' ) );
		$this->assertEquals( '2024-01-01 00:00:00', $notification->get_date_notified()->format( 'Y-m-d H:i:s' ) );
		$this->assertTrue( $notification->is_queued() );
	}

	/**
	 * Test validation requirements for creating a notification.
	 */
	public function test_create_notification_validation() {
		$notification = new Notification();

		// Test missing product_id.
		$result = $notification->save();
		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertEquals( 'stock_notification_validation_error', $result->get_error_code() );
		$this->assertEquals( 'Product ID is required', $result->get_error_message() );

		// Test missing user_id and user_email.
		$notification->set_product_id( 1 );
		$result = $notification->save();
		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertEquals( 'stock_notification_validation_error', $result->get_error_code() );
		$this->assertEquals( 'User ID or User Email is required', $result->get_error_message() );
	}

	/**
	 * Test default data.
	 */
	public function test_default_notification_data() {

		$notification = new Notification();
		$this->assertEquals( 0, $notification->get_id() );
		$this->assertEquals( null, $notification->get_product_id() );
		$this->assertEquals( null, $notification->get_user_id() );
		$this->assertEquals( null, $notification->get_user_email() );
		$this->assertEquals( 'pending', $notification->get_status() );
		$this->assertFalse( $notification->is_queued() );
		$this->assertEquals( null, $notification->get_date_created() );
		$this->assertEquals( null, $notification->get_date_modified() );
		$this->assertEquals( null, $notification->get_date_subscribed() );
		$this->assertEquals( null, $notification->get_date_notified() );

		$notification->set_product_id( 1 );
		$notification->set_user_id( 1 );
		$notification->save();

		$this->assertEquals( 1, $notification->get_id() );
		$this->assertEquals( 1, $notification->get_product_id() );
		$this->assertEquals( 1, $notification->get_user_id() );
		$this->assertEquals( null, $notification->get_user_email() );
		$this->assertEquals( 'pending', $notification->get_status() );
		$this->assertFalse( $notification->is_queued() );
		$this->assertEqualsWithDelta( 5, $notification->get_date_created()->getTimestamp(), time() );
		$this->assertEqualsWithDelta( 5, $notification->get_date_modified()->getTimestamp(), time() );
		$this->assertEquals( null, $notification->get_date_subscribed() );
		$this->assertEquals( null, $notification->get_date_notified() );
	}

	/**
	 * Test updating a notification with all properties.
	 */
	public function test_update_notification() {
		// Create initial notification.
		$notification = new Notification();
		$notification->set_product_id( 1 );
		$notification->set_user_id( 1 );
		$notification->set_user_email( 'test@test.com' );
		$notification->set_date_created( '2024-01-01 00:00:00' );
		$notification->save();

		// Verify dates.
		$this->assertEquals( '2024-01-01 00:00:00', $notification->get_date_created()->format( 'Y-m-d H:i:s' ) );
		$this->assertEquals( '2024-01-01 00:00:00', $notification->get_date_modified()->format( 'Y-m-d H:i:s' ) );

		// Update all properties.
		$notification->set_product_id( 2 );
		$notification->set_user_id( 2 );
		$notification->set_user_email( 'test2@test.com' );
		$notification->set_status( 'active' );
		$notification->set_date_subscribed( '2024-01-02 00:00:00' );
		$notification->set_date_notified( '2024-01-02 00:00:00' );
		$notification->set_is_queued( true );
		$notification->save();

		// Verify all properties were updated correctly.
		$this->assertEquals( 2, $notification->get_product_id() );
		$this->assertEquals( 2, $notification->get_user_id() );
		$this->assertEquals( 'test2@test.com', $notification->get_user_email() );
		$this->assertEquals( 'active', $notification->get_status() );
		$this->assertEquals( '2024-01-02 00:00:00', $notification->get_date_subscribed()->format( 'Y-m-d H:i:s' ) );
		$this->assertEquals( '2024-01-02 00:00:00', $notification->get_date_notified()->format( 'Y-m-d H:i:s' ) );
		$this->assertTrue( $notification->is_queued() );

		// Verify modified date is updated.
		$this->assertEquals( '2024-01-01 00:00:00', $notification->get_date_created()->format( 'Y-m-d H:i:s' ) );
		$this->assertNotEquals( '2024-01-01 00:00:00', $notification->get_date_modified()->format( 'Y-m-d H:i:s' ) );
	}

	/**
	 * Test deleting a notification.
	 */
	public function test_delete_notification() {
		// Create a notification.
		$notification = new Notification();
		$notification->set_product_id( 1 );
		$notification->set_user_id( 1 );
		$notification->save();

		$notification_id = $notification->get_id();
		$this->assertGreaterThan( 0, $notification_id );

		// Delete the notification.
		$notification->delete();

		// Verify the notification was deleted.
		$this->assertEquals( 0, $notification->get_id() );

		// Try to read the deleted notification.
		$this->expectException( \Exception::class );
		$this->expectExceptionMessage( 'Stock notification not found' );
		new Notification( $notification_id );
	}

	/**
	 * Test reading a non-existent notification.
	 */
	public function test_read_nonexistent_notification() {
		$this->expectException( \Exception::class );
		$this->expectExceptionMessage( 'Stock notification not found' );
		new Notification( 999999 );
	}

	/**
	 * Test adding a meta to a notification.
	 */
	public function test_add_meta_to_notification() {
		$notification = new Notification();
		$notification->set_product_id( 1 );
		$notification->set_user_id( 1 );
		$notification->add_meta_data( 'test_meta', 'test_value' );
		$notification->save();

		$this->assertEquals( 'test_value', $notification->get_meta( 'test_meta' ) );

		// Refresh the notification.
		$notification = new Notification( $notification->get_id() );
		$this->assertEquals( 'test_value', $notification->get_meta( 'test_meta' ) );
	}

	/**
	 * Test updating a meta for a notification.
	 */
	public function test_update_meta_for_notification() {
		$notification = new Notification();
		$notification->set_product_id( 1 );
		$notification->set_user_id( 1 );
		$notification->save();

		// Refetch the notification.
		$notification = new Notification( $notification->get_id() );
		$notification->add_meta_data( 'test_meta', 'test_value' );
		$notification->save();

		$notification = new Notification( $notification->get_id() );
		$this->assertEquals( 'test_value', $notification->get_meta( 'test_meta' ) );
		$notification->update_meta_data( 'test_meta', 'updated_value' );
		$notification->save();

		// Refetch the notification.
		$notification = new Notification( $notification->get_id() );

		$this->assertEquals( 'updated_value', $notification->get_meta( 'test_meta' ) );
	}

	/**
	 * Test deleting a meta for a notification.
	 */
	public function test_delete_meta_for_notification() {
		$notification = new Notification();
		$notification->set_product_id( 1 );
		$notification->set_user_id( 1 );
		$notification->add_meta_data( 'test_meta', 'test_value' );
		$notification->save();

		$notification = new Notification( $notification->get_id() );
		$this->assertEquals( 'test_value', $notification->get_meta( 'test_meta' ) );
		$notification->delete_meta_data( 'test_meta' );
		$notification->save();

		$notification = new Notification( $notification->get_id() );
		$this->assertFalse( $notification->meta_exists( 'test_meta' ) );
		$this->assertEquals( '', $notification->get_meta( 'test_meta' ) );
	}
}
