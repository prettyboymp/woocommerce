<?php

/**
 * Tests for WC_Fulfillment object.
 */
class WC_Fulfillment_Test extends WC_Unit_Test_Case {
	/**
	 * Test that the WC_Fulfillment object can be created.
	 */
	public function test_wc_fulfillment_object() {
		$fulfillment = new WC_Fulfillment();
		$this->assertInstanceOf( 'WC_Fulfillment', $fulfillment );
	}

	/**
	 * Test that the WC_Fulfillment object can be created with an ID.
	 */
	public function test_wc_fulfillment_object_with_id_fetches_data_and_metadata() {
		$db_fulfillment = $this->helper_create_fulfillment();
		$fulfillment    = new WC_Fulfillment( $db_fulfillment->get_id() );

		$this->assertInstanceOf( 'WC_Fulfillment', $fulfillment );
		$this->assertEquals( $db_fulfillment->get_id(), $fulfillment->get_id() );
		$this->assertEquals( $db_fulfillment->get_entity_type(), $fulfillment->get_entity_type() );
		$this->assertEquals( $db_fulfillment->get_entity_id(), $fulfillment->get_entity_id() );
		$this->assertEquals( $db_fulfillment->get_date_created(), $fulfillment->get_date_created() );
		$this->assertEquals( $db_fulfillment->get_date_deleted(), $fulfillment->get_date_deleted() );
		$this->assertEquals( $db_fulfillment->get_items(), $fulfillment->get_items() );
		$this->assertEquals( $db_fulfillment->get_meta_data(), $fulfillment->get_meta_data() );
	}

	/**
	 * Test that WC_Fulfillment object can be updated.
	 */
	public function test_wc_fulfillment_object_update() {
		$fulfillment = $this->helper_create_fulfillment(
			array(
				'entity_type' => 'order-fulfillment',
				'entity_id'   => 123,
			)
		);

		$fulfillment->set_entity_type( 'updated-entity-type' );
		$fulfillment->set_entity_id( 456 );
		$fulfillment->save();

		$this->assertEquals( 'updated-entity-type', $fulfillment->get_entity_type() );
		$this->assertEquals( 456, $fulfillment->get_entity_id() );
	}

	/**
	 * Test that WC_Fulfillment object can be soft deleted.
	 */
	public function test_wc_fulfillment_object_soft_delete() {
		$fulfillment = $this->helper_create_fulfillment(
			array(
				'entity_type' => 'order-fulfillment',
				'entity_id'   => 123,
			)
		);

		$fulfillment_id = $fulfillment->get_id();
		$this->assertNotEquals( 0, $fulfillment_id );

		$fulfillment->delete();

		$fresh_fulfillment = new WC_Fulfillment( $fulfillment_id );
		$this->assertInstanceOf( 'WC_Fulfillment', $fresh_fulfillment );
		$this->assertNotEquals( null, $fresh_fulfillment->get_date_deleted() );
	}

	/**
	 * Test that WC_Fulfillment object can be created with items.
	 */
	public function test_wc_fulfillment_object_with_items() {
		$fulfillment = $this->helper_create_fulfillment(
			array(
				'entity_type' => 'order-fulfillment',
				'entity_id'   => 123,
			)
		);

		$items = array(
			array(
				'item_id' => 1,
				'qty'     => 2,
			),
			array(
				'item_id' => 2,
				'qty'     => 3,
			),
		);

		$fulfillment->set_items( $items );
		$fulfillment->save();

		$fresh_fulfillment = new WC_Fulfillment( $fulfillment->get_id() );
		$this->assertInstanceOf( 'WC_Fulfillment', $fresh_fulfillment );
		$this->assertEquals( $fulfillment->get_id(), $fresh_fulfillment->get_id() );

		$this->assertEquals( $items, $fresh_fulfillment->get_items() );
	}

	/**
	 * Test that WC_Fulfillment object can be created with metadata.
	 */
	public function test_wc_fulfillment_object_with_metadata() {
		$fulfillment = $this->helper_create_fulfillment(
			array(
				'entity_type' => 'order-fulfillment',
				'entity_id'   => 123,
			)
		);

		$fulfillment->add_meta_data( 'test_meta_key', 'test_meta_value', true );
		$fulfillment->save();

		$this->assertEquals( 'test_meta_value', $fulfillment->get_meta( 'test_meta_key' ) );
	}

	/**
	 * Test that metadata can be updated.
	 */
	public function test_wc_fulfillment_object_update_metadata() {
		$fulfillment = $this->helper_create_fulfillment(
			array(
				'entity_type' => 'order-fulfillment',
				'entity_id'   => 123,
			)
		);

		$fulfillment->add_meta_data( 'test_meta_key', 'test_meta_value', true );
		$fulfillment->save();

		$fulfillment->update_meta_data( 'test_meta_key', 'updated_meta_value' );
		$fulfillment->save();

		$this->assertEquals( 'updated_meta_value', $fulfillment->get_meta( 'test_meta_key' ) );
	}

	/**
	 * Test that metadata can be deleted.
	 */
	public function test_wc_fulfillment_object_delete_metadata() {
		$fulfillment = $this->helper_create_fulfillment(
			array(
				'entity_type' => 'order-fulfillment',
				'entity_id'   => 123,
			)
		);

		$fulfillment->add_meta_data( 'test_meta_key', 'test_meta_value', true );
		$fulfillment->save();

		$fulfillment->delete_meta_data( 'test_meta_key' );
		$fulfillment->save();

		$this->assertEquals( '', $fulfillment->get_meta( 'test_meta_key' ) );
	}

	/**
	 * Helper function to create a fulfillment.
	 *
	 * @param array $args Arguments to create the fulfillment.
	 * @return WC_Fulfillment The created fulfillment object.
	 */
	private function helper_create_fulfillment( $args = array() ) {
		$fulfillment = new WC_Fulfillment();
		$fulfillment->set_props(
			array_merge(
				array(
					'id'          => 0,
					'entity_type' => 'order-fulfillment',
					'entity_id'   => 123,
				),
				$args
			)
		);

		$fulfillment->add_meta_data(
			'test_meta_key',
			'test_meta_value',
			true
		);

		$fulfillment->set_items(
			array(
				array(
					'item_id' => 1,
					'qty'     => 2,
				),
				array(
					'item_id' => 2,
					'qty'     => 3,
				),
			)
		);

		$fulfillment->save();

		// Check if the fulfillment was created successfully.
		$this->assertNotEquals( 0, $fulfillment->get_id() );
		$this->assertNotNull( $fulfillment->get_date_created() );

		return $fulfillment;
	}
}
