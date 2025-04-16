<?php

declare( strict_types = 1 );
namespace Automattic\WooCommerce\Tests\Internal\DataStores\StockNotifications;

use Automattic\WooCommerce\Internal\DataStores\StockNotifications\StockNotificationsDataStore;

/**
 * Class StockNotificationsActivityLogsDataStoreTests.
 */
class StockNotificationsActivityLogsDataStoreTests extends \WC_Unit_Test_Case {

	/**
	 * The data store instance.
	 *
	 * @var StockNotificationsActivityLogsDataStore
	 */
	private $data_store;

	/**
	 * Set up the test.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->data_store = wc_get_container()->get( StockNotificationsDataStore::class );
	}

	/**
	 * Tear down the test.
	 */
	public function tearDown(): void {
		parent::tearDown();
		// Clean up all logs.
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wc_stock_notifications_logs" );
	}

	/**
	 * Test creating an activity log.
	 */
	public function test_create_activity_log() {
		$args = array(
			'notification_id' => 1,
			'action'          => 'created',
			'user_id'         => 1,
			'user_email'      => 'test@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log',
		);

		$log_id = $this->data_store->create_activity_log( $args );
		$this->assertGreaterThan( 0, $log_id );

		$log = $this->data_store->query_activity_logs(
			array(
				'notification_id' => $args['notification_id'],
			)
		);
		$this->assertIsArray( $log );
		$this->assertEquals( $log_id, $log[0]['id'] );
		$this->assertEquals( $args['notification_id'], $log[0]['notification_id'] );
		$this->assertEquals( $args['action'], $log[0]['action'] );
		$this->assertEquals( $args['user_id'], $log[0]['user_id'] );
		$this->assertEquals( $args['user_email'], $log[0]['user_email'] );
		$this->assertEquals( $args['ip_address'], $log[0]['ip_address'] );
		$this->assertEquals( $args['note'], $log[0]['note'] );
	}

	/**
	 * Test querying logs by notification ID.
	 */
	public function test_query_logs_by_notification_id() {
		// Create test logs.
		$args1 = array(
			'notification_id' => 1,
			'action'          => 'created',
			'user_id'         => 1,
			'user_email'      => 'test1@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 1',
		);

		$args2 = array(
			'notification_id' => 2,
			'action'          => 'created',
			'user_id'         => 1,
			'user_email'      => 'test2@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 2',
		);

		$this->data_store->create_activity_log( $args1 );
		$this->data_store->create_activity_log( $args2 );

		$logs = $this->data_store->query_activity_logs(
			array(
				'notification_id' => 1,
			)
		);

		$this->assertCount( 1, $logs );
		$this->assertEquals( $args1['notification_id'], $logs[0]['notification_id'] );
	}

	/**
	 * Test querying logs by action.
	 */
	public function test_query_logs_by_action() {
		// Create test logs.
		$args1 = array(
			'notification_id' => 1,
			'action'          => 'created',
			'user_id'         => 1,
			'user_email'      => 'test1@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 1',
		);

		$args2 = array(
			'notification_id' => 2,
			'action'          => 'updated',
			'user_id'         => 1,
			'user_email'      => 'test2@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 2',
		);

		$this->data_store->create_activity_log( $args1 );
		$this->data_store->create_activity_log( $args2 );

		$logs = $this->data_store->query_activity_logs(
			array(
				'action' => 'created',
			)
		);

		$this->assertCount( 1, $logs );
		$this->assertEquals( $args1['action'], $logs[0]['action'] );
	}

	/**
	 * Test querying logs by user ID.
	 */
	public function test_query_logs_by_user_id() {
		// Create test logs.
		$args1 = array(
			'notification_id' => 1,
			'action'          => 'created',
			'user_id'         => 1,
			'user_email'      => 'test1@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 1',
		);

		$args2 = array(
			'notification_id' => 2,
			'action'          => 'created',
			'user_id'         => 2,
			'user_email'      => 'test2@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 2',
		);

		$this->data_store->create_activity_log( $args1 );
		$this->data_store->create_activity_log( $args2 );

		$logs = $this->data_store->query_activity_logs(
			array(
				'user_id' => 1,
			)
		);

		$this->assertCount( 1, $logs );
		$this->assertEquals( $args1['user_id'], $logs[0]['user_id'] );
	}

	/**
	 * Test querying logs by user email.
	 */
	public function test_query_logs_by_user_email() {
		// Create test logs.
		$args1 = array(
			'notification_id' => 1,
			'action'          => 'created',
			'user_id'         => 1,
			'user_email'      => 'test1@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 1',
		);

		$args2 = array(
			'notification_id' => 2,
			'action'          => 'created',
			'user_id'         => 2,
			'user_email'      => 'test2@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 2',
		);

		$this->data_store->create_activity_log( $args1 );
		$this->data_store->create_activity_log( $args2 );

		$logs = $this->data_store->query_activity_logs(
			array(
				'user_email' => 'test1@test.com',
			)
		);

		$this->assertCount( 1, $logs );
		$this->assertEquals( $args1['user_email'], $logs[0]['user_email'] );
	}

	/**
	 * Test querying logs with limit and offset.
	 */
	public function test_query_logs_with_limit_and_offset() {
		// Create test logs.
		$args1 = array(
			'notification_id' => 1,
			'action'          => 'created',
			'user_id'         => 1,
			'user_email'      => 'test1@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 1',
		);

		$args2 = array(
			'notification_id' => 2,
			'action'          => 'created',
			'user_id'         => 2,
			'user_email'      => 'test2@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 2',
		);

		$args3 = array(
			'notification_id' => 3,
			'action'          => 'created',
			'user_id'         => 3,
			'user_email'      => 'test3@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 3',
		);

		$this->data_store->create_activity_log( $args1 );
		$this->data_store->create_activity_log( $args2 );
		$this->data_store->create_activity_log( $args3 );

		$logs = $this->data_store->query_activity_logs(
			array(
				'limit'  => 1,
				'offset' => 1,
			)
		);

		$this->assertCount( 1, $logs );
		$this->assertEquals( $args2['notification_id'], $logs[0]['notification_id'] );
	}

	/**
	 * Test querying logs with return type count.
	 */
	public function test_query_logs_with_return_type_count() {
		// Create test logs.
		$args1 = array(
			'notification_id' => 1,
			'action'          => 'created',
			'user_id'         => 1,
			'user_email'      => 'test1@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 1',
		);

		$args2 = array(
			'notification_id' => 2,
			'action'          => 'created',
			'user_id'         => 2,
			'user_email'      => 'test2@test.com',
			'ip_address'      => '127.0.0.1',
			'note'            => 'Test log 2',
		);

		$this->data_store->create_activity_log( $args1 );
		$this->data_store->create_activity_log( $args2 );

		$count = $this->data_store->query_activity_logs(
			array(
				'return' => 'count',
			)
		);

		$this->assertEquals( 2, $count );
	}
}
