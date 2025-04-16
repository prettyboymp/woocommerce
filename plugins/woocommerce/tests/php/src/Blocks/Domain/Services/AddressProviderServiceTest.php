<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\Tests\Blocks\Domain\Services;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Automattic\WooCommerce\Blocks\Domain\Services\AddressProviderService;
use Automattic\WooCommerce\Blocks\Package;
use WC_Address_Provider;

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
	}

	/**
	 * Tear down test case.
	 */
	protected function tearDown(): void {
		parent::tearDown();
		remove_all_filters( 'woocommerce_address_providers' );
	}

	/**
	 * Creates a mock provider with the given ID and name.
	 *
	 * @param string $id The provider ID.
	 * @param string $name The provider name.
	 * @return WC_Address_Provider
	 */
	private function create_mock_provider( string $id, string $name ): WC_Address_Provider {
		$provider       = \Mockery::mock( WC_Address_Provider::class );
		$provider->id   = $id;
		$provider->name = $name;
		return $provider;
	}

	/**
	 * Test getting providers when none are registered.
	 */
	public function test_get_providers_empty() {
		$providers = $this->sut->get_registered_providers();
		$this->assertEmpty( $providers );
	}

	/**
	 * Test getting registered providers.
	 */
	public function test_get_providers() {
		$provider1 = $this->create_mock_provider( 'provider-1', 'Provider One' );
		$provider2 = $this->create_mock_provider( 'provider-2', 'Provider Two' );

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider1, $provider2 ) {
				$providers[ $provider1->id ] = $provider1;
				$providers[ $provider2->id ] = $provider2;
				return $providers;
			}
		);

		$providers = $this->sut->get_registered_providers();

		$this->assertCount( 2, $providers );
		$this->assertArrayHasKey( 'provider-1', $providers );
		$this->assertArrayHasKey( 'provider-2', $providers );
		$this->assertSame( $provider1, $providers['provider-1'] );
		$this->assertSame( $provider2, $providers['provider-2'] );
	}

	/**
	 * Test checking if a provider is available.
	 */
	public function test_is_provider_available() {
		$provider = $this->create_mock_provider( 'test-provider', 'Test Provider' );

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider ) {
				$providers[ $provider->id ] = $provider;
				return $providers;
			}
		);

		$this->assertTrue( $this->sut->is_provider_available( 'test-provider' ) );
		$this->assertFalse( $this->sut->is_provider_available( 'non-existent-provider' ) );
	}

	/**
	 * Test that multiple filters can add providers.
	 */
	public function test_multiple_provider_filters() {
		$provider1 = $this->create_mock_provider( 'provider-1', 'Provider One' );
		$provider2 = $this->create_mock_provider( 'provider-2', 'Provider Two' );

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider1 ) {
				return array_merge( $providers, [ $provider1->id => $provider1 ] );
			},
			10
		);

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider2 ) {
				return array_merge( $providers, [ $provider2->id => $provider2 ] );
			},
			20
		);

		$providers = $this->sut->get_registered_providers();

		$this->assertCount( 2, $providers );
		$this->assertArrayHasKey( 'provider-1', $providers );
		$this->assertArrayHasKey( 'provider-2', $providers );
		$this->assertSame( $provider1, $providers['provider-1'] );
		$this->assertSame( $provider2, $providers['provider-2'] );
	}

	/**
	 * Test that later filters can override earlier ones.
	 */
	public function test_provider_filter_override() {
		$provider1 = $this->create_mock_provider( 'test-provider', 'Original Provider' );
		$provider2 = $this->create_mock_provider( 'test-provider', 'Override Provider' );

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider1 ) {
				return array_merge( $providers, [ $provider1->id => $provider1 ] );
			},
			10
		);

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider2 ) {
				return array_merge( $providers, [ $provider2->id => $provider2 ] );
			},
			20
		);

		$providers = $this->sut->get_registered_providers();

		$this->assertCount( 1, $providers );
		$this->assertArrayHasKey( 'test-provider', $providers );
		$this->assertSame( $provider2, $providers['test-provider'] );
		$this->assertSame( 'Override Provider', $providers['test-provider']->name );
	}
}
