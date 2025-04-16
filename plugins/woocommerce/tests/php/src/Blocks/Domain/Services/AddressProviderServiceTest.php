<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\Tests\Blocks\Domain\Services;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Automattic\WooCommerce\Blocks\Domain\Services\AddressProviderService;
use Automattic\WooCommerce\Blocks\Package;

/**
 * Tests for Address Provider Service functionality
 */
class AddressProviderServiceTest extends MockeryTestCase {

	/**
	 * System under test.
	 *
	 * @var AddressProviderService
	 */
	private $sut;

	/**
	 * Setup test case.
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->sut = Package::container()->get( AddressProviderService::class );
		add_filter( 'doing_it_wrong_trigger_error', '__return_false' );
	}

	/**
	 * Tear down test case.
	 */
	protected function tearDown(): void {
		parent::tearDown();

		// Use reflection to reset the class.
		$reflection = new \ReflectionClass( $this->sut );
		$property   = $reflection->getProperty( 'providers' );
		// This is a no op in PHP 8.1 but we still need it for PHP 7.4.
		$property->setAccessible( true );
		$property->setValue( $this->sut, [] );

		remove_filter( 'doing_it_wrong_trigger_error', '__return_false' );
		remove_all_actions( 'doing_it_wrong_run' );
	}

	/**
	 * Test registering a valid provider
	 */
	public function test_register_valid_provider() {
		$result = __experimental_woocommerce_register_address_provider( 'test-provider', 'Test Provider' );
		$this->assertTrue( $result );

		$this->assertTrue( $this->sut->is_provider_available( 'test-provider' ) );
	}

	/**
	 * Test registering a provider with empty ID.
	 */
	public function test_register_provider_empty_id() {
		$doing_it_wrong_mocker = \Mockery::mock( 'ActionCallback' );
		$doing_it_wrong_mocker->shouldReceive( 'doing_it_wrong_run' )->withArgs(
			array(
				'__experimental_woocommerce_register_address_provider',
				'Unable to register provider. The provider ID is required.',
			)
		)->once();

		add_action(
			'doing_it_wrong_run',
			array(
				$doing_it_wrong_mocker,
				'doing_it_wrong_run',
			),
			10,
			2
		);

		$result = __experimental_woocommerce_register_address_provider( '', 'Test Provider' );
		$this->assertFalse( $result );
	}

	/**
	 * Test registering a provider with empty name.
	 */
	public function test_register_provider_empty_name() {
		$doing_it_wrong_mocker = \Mockery::mock( 'ActionCallback' );
		$doing_it_wrong_mocker->shouldReceive( 'doing_it_wrong_run' )->withArgs(
			array(
				'__experimental_woocommerce_register_address_provider',
				esc_html( sprintf( 'Unable to register provider with id: "%s". The provider name is required.', 'test-provider' ) ),
			)
		)->once();

		add_action(
			'doing_it_wrong_run',
			array(
				$doing_it_wrong_mocker,
				'doing_it_wrong_run',
			),
			10,
			2
		);

		$result = __experimental_woocommerce_register_address_provider( 'test-provider', '' );
		$this->assertFalse( $result );
	}

	/**
	 * Test registering duplicate provider.
	 */
	public function test_register_duplicate_provider() {
		$result = __experimental_woocommerce_register_address_provider( 'test-provider', 'Test Provider' );
		$this->assertTrue( $result );

		$doing_it_wrong_mocker = \Mockery::mock( 'ActionCallback' );
		$doing_it_wrong_mocker->shouldReceive( 'doing_it_wrong_run' )->withArgs(
			array(
				'__experimental_woocommerce_register_address_provider',
				esc_html( sprintf( 'Unable to register provider with id: "%s". The provider is already registered.', 'test-provider' ) ),
			)
		)->once();

		add_action(
			'doing_it_wrong_run',
			array(
				$doing_it_wrong_mocker,
				'doing_it_wrong_run',
			),
			10,
			2
		);

		$result = __experimental_woocommerce_register_address_provider( 'test-provider', 'Another Provider' );
		$this->assertFalse( $result );
	}

	/**
	 * Test registration before blocks loaded.
	 */
	public function test_register_provider_before_blocks_loaded() {
		// Reset the blocks loaded state.
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- This is a test case where we need to simulate the action not firing.
		$GLOBALS['wp_actions']['woocommerce_blocks_loaded'] = 0;

		$result = __experimental_woocommerce_register_address_provider( 'test-provider', 'Test Provider' );
		$this->assertFalse( $result ); // Should return false initially.

		// Trigger blocks loaded.
		// phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		do_action( 'woocommerce_blocks_loaded' );

		// Now check that the provider is registered.
		$this->assertTrue( $this->sut->is_provider_available( 'test-provider' ) );
	}

	/**
	 * Test getting registered providers.
	 */
	public function test_get_providers() {
		__experimental_woocommerce_register_address_provider( 'provider-1', 'Provider One' );
		__experimental_woocommerce_register_address_provider( 'provider-2', 'Provider Two' );
		$providers = $this->sut->get_registered_providers();

		$this->assertCount( 2, $providers );
		$this->assertArrayHasKey( 'provider-1', $providers );
		$this->assertArrayHasKey( 'provider-2', $providers );
		$this->assertEquals( 'Provider One', $providers['provider-1']['name'] );
		$this->assertEquals( 'Provider Two', $providers['provider-2']['name'] );
	}
}
