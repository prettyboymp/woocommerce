<?php
/**
 * OrderFulfillmentManager class file.
 *
 * @package Automattic\WooCommerce\Tests\Internal\Fulfillments
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Tests\Internal\Fulfillments;

use Automattic\WooCommerce\RestApi\UnitTests\Helpers\OrderHelper;
use Automattic\WooCommerce\Internal\Fulfillments\Fulfillment;
use Automattic\WooCommerce\Internal\Fulfillments\OrderFulfillmentManager;
use WC_Order;

/**
 * This class tests the OrderFulfillmentManager.
 */
class OrderFulfillmentManagerTest extends \WP_UnitTestCase {

	/**
	 * @var int
	 */
	protected $order_id;

	/**
	 * @var WC_Order
	 */
	protected $order;

	/**
	 * @var OrderFulfillmentManager
	 */
	protected $manager;

	/**
	 * Set up the test case.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->order   = OrderHelper::create_order();
		$this->manager = new OrderFulfillmentManager( $this->order );
	}

	/**
	 * Test the get_fulfillable_id method.
	 */
	public function test_get_fulfillable_id() {
		$this->assertEquals( (string) $this->order->get_id(), $this->manager->get_fulfillable_id() );
	}

	/**
	 * Test the get_fulfillable_type method.
	 */
	public function test_get_fulfillable_type() {
		$this->assertEquals( WC_Order::class, $this->manager->get_fulfillable_type() );
	}

	/**
	 * Test the set_fulfillment_status method.
	 */
	public function test_get_set_fulfillment_status() {
		$this->manager->set_fulfillment_status( 'unfulfilled' );
		$this->assertEquals( 'unfulfilled', $this->manager->get_fulfillment_status() );
	}

	/**
	 * Test the get_fulfillments method.
	 */
	public function test_add_get_fulfillments() {
		$fulfillment = new Fulfillment();
		$fulfillment->set_entity_id( (string) $this->order->get_id() );
		$fulfillment->set_entity_type( WC_Order::class );
		$fulfillment->set_status( 'unfulfilled' );
		$fulfillment->set_items(
			array(
				array(
					'item_id' => 1,
					'qty'     => 2,
				),
			)
		);

		$this->manager->add_fulfillment( $fulfillment );

		$fulfillments = $this->manager->get_fulfillments();

		$this->assertCount( 1, $fulfillments );
		$this->assertEquals( 'unfulfilled', $fulfillments[0]->get_status() );
	}

	/**
	 * Test the update_fulfillment method.
	 */
	public function test_update_fulfillment() {
		$fulfillment = new Fulfillment();
		$fulfillment->set_entity_id( (string) $this->order->get_id() );
		$fulfillment->set_entity_type( WC_Order::class );
		$fulfillment->set_status( 'unfulfilled' );
		$fulfillment->set_items(
			array(
				array(
					'item_id' => 1,
					'qty'     => 2,
				),
			)
		);

		$this->manager->add_fulfillment( $fulfillment );

		$fulfillment->set_status( 'fulfilled' );
		$this->manager->update_fulfillment( $fulfillment );

		$fulfillments = $this->manager->get_fulfillments();
		$this->assertCount( 1, $fulfillments );
		$this->assertEquals( 'fulfilled', $fulfillments[0]->get_status() );
	}

	/**
	 * Test order fulfillment status when no fulfillments.
	 */
	public function test_order_fulfillment_status_no_fulfillments() {
		$this->assertEquals( 'no_fulfillments', $this->manager->get_fulfillment_status() );
	}

	/**
	 * Test order fulfillment status updates.
	 */
	public function test_update_order_fulfillment_status() {
		$fulfillment = new Fulfillment();
		$fulfillment->set_entity_id( (string) $this->order->get_id() );
		$fulfillment->set_entity_type( WC_Order::class );
		$fulfillment->set_status( 'unfulfilled' );
		$fulfillment->set_is_fulfilled( false );
		$fulfillment->set_items(
			array(
				array(
					'item_id' => 1,
					'qty'     => 2,
				),
			)
		);

		$this->manager->add_fulfillment( $fulfillment );

		$fulfillment_2 = new Fulfillment();
		$fulfillment_2->set_entity_id( (string) $this->order->get_id() );
		$fulfillment_2->set_entity_type( WC_Order::class );
		$fulfillment_2->set_status( 'unfulfilled' );
		$fulfillment_2->set_is_fulfilled( false );
		$fulfillment_2->set_items(
			array(
				array(
					'item_id' => 1,
					'qty'     => 2,
				),
			)
		);

		$this->manager->add_fulfillment( $fulfillment_2 );

		$this->assertEquals( 'unfulfilled', $this->manager->get_fulfillment_status() );

		$fulfillment->set_status( 'fulfilled' );
		$fulfillment->set_is_fulfilled( true );
		$this->manager->update_fulfillment( $fulfillment );

		$this->assertEquals( 'partially_fulfilled', $this->manager->get_fulfillment_status() );

		$fulfillment_2->set_status( 'fulfilled' );
		$fulfillment_2->set_is_fulfilled( true );
		$this->manager->update_fulfillment( $fulfillment_2 );

		$this->assertEquals( 'fulfilled', $this->manager->get_fulfillment_status() );
	}
}
