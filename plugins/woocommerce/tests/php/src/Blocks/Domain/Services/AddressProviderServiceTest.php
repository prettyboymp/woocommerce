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
		// Define test provider class for provider-1.
		$provider1_class = new class() extends WC_Address_Provider {
			/**
			 * Constructor.
			 * Sets up the provider with an ID and name.
			 */
			public function __construct() {
				$this->id   = 'provider-1';
				$this->name = 'Provider One';
			}
		};

		// Define test provider class for provider-2.
		$provider2_class = new class() extends WC_Address_Provider {
			/**
			 * Constructor.
			 * Sets up the provider with an ID and name.
			 */
			public function __construct() {
				$this->id   = 'provider-2';
				$this->name = 'Provider Two';
			}
		};

		// Get class names for filter.
		$provider1_class_name = get_class( $provider1_class );
		$provider2_class_name = get_class( $provider2_class );

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider1_class_name, $provider2_class_name ) {
				$providers[] = $provider1_class_name;
				$providers[] = $provider2_class_name;
				return $providers;
			}
		);

		$registered_providers = $this->sut->get_registered_providers();

		// Test that we have two provider classes registered.
		$this->assertCount( 2, $registered_providers );

		// Test that the registered providers are the correct class names.
		$this->assertContains( $provider1_class_name, $registered_providers );
		$this->assertContains( $provider2_class_name, $registered_providers );

		// Test that instantiating these classes gives us the expected providers.
		$provider1_instance = new $provider1_class_name();
		$provider2_instance = new $provider2_class_name();

		$this->assertEquals( 'Provider One', $provider1_instance->name );
		$this->assertEquals( 'Provider Two', $provider2_instance->name );
	}

	/**
	 * Test checking if a provider is available.
	 */
	public function test_is_provider_available() {
		// Define test provider class.
		$provider_class = new class() extends WC_Address_Provider {
			/**
			 * Constructor.
			 * Sets up the provider with an ID and name.
			 */
			public function __construct() {
				$this->id   = 'test-provider';
				$this->name = 'Test Provider';
			}
		};

		// Get class name for filter.
		$provider_class_name = get_class( $provider_class );

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider_class_name ) {
				$providers[] = $provider_class_name;
				return $providers;
			}
		);

		// Check if the provider is available.
		$this->assertTrue( $this->sut->is_provider_available( 'test-provider' ) );
		$this->assertFalse( $this->sut->is_provider_available( 'non-existent-provider' ) );
	}

	/**
	 * Test that multiple filters can add providers.
	 */
	public function test_multiple_provider_filters() {
		// Define test provider class for provider-1.
		$provider1_class = new class() extends WC_Address_Provider {
			/**
			 * Constructor.
			 * Sets up the provider with an ID and name.
			 */
			public function __construct() {
				$this->id   = 'provider-1';
				$this->name = 'Provider One';
			}
		};

		// Define test provider class for provider-2.
		$provider2_class = new class() extends WC_Address_Provider {
			/**
			 * Constructor.
			 * Sets up the provider with an ID and name.
			 */
			public function __construct() {
				$this->id   = 'provider-2';
				$this->name = 'Provider Two';
			}
		};

		// Get class names for filters.
		$provider1_class_name = get_class( $provider1_class );
		$provider2_class_name = get_class( $provider2_class );

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider1_class_name ) {
				$providers[] = $provider1_class_name;
				return $providers;
			},
			10
		);

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider2_class_name ) {
				$providers[] = $provider2_class_name;
				return $providers;
			},
			20
		);

		$registered_providers = $this->sut->get_registered_providers();

		// Test that we have two provider classes registered.
		$this->assertCount( 2, $registered_providers );

		// Test that both class names are registered.
		$this->assertContains( $provider1_class_name, $registered_providers );
		$this->assertContains( $provider2_class_name, $registered_providers );

		// Test that instantiating these classes gives us the expected providers.
		$provider1_instance = new $provider1_class_name();
		$provider2_instance = new $provider2_class_name();

		$this->assertEquals( 'Provider One', $provider1_instance->name );
		$this->assertEquals( 'Provider Two', $provider2_instance->name );
	}

	/**
	 * Test that later filters can override earlier ones.
	 */
	public function test_provider_filter_override() {
		// Define original provider class.
		$provider1_class = new class() extends WC_Address_Provider {
			/**
			 * Constructor.
			 * Sets up the provider with an ID and name.
			 */
			public function __construct() {
				$this->id   = 'test-provider';
				$this->name = 'Original Provider';
			}
		};

		// Define override provider class.
		$provider2_class = new class() extends WC_Address_Provider {
			/**
			 * Constructor.
			 * Sets up the provider with an ID and name.
			 */
			public function __construct() {
				$this->id   = 'test-provider';
				$this->name = 'Override Provider';
			}
		};

		// Get class names for filters.
		$provider1_class_name = get_class( $provider1_class );
		$provider2_class_name = get_class( $provider2_class );

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider1_class_name ) {
				$providers[] = $provider1_class_name;
				return $providers;
			},
			10
		);

		add_filter(
			'woocommerce_address_providers',
			function ( $providers ) use ( $provider2_class_name ) {
				$providers[] = $provider2_class_name;
				return $providers;
			},
			20
		);

		$registered_providers = $this->sut->get_registered_providers();

		// Test that both class names are registered.
		$this->assertCount( 2, $registered_providers );
		$this->assertContains( $provider1_class_name, $registered_providers );
		$this->assertContains( $provider2_class_name, $registered_providers );

		// Test that instantiating these classes gives us the expected providers.
		$provider1_instance = new $provider1_class_name();
		$provider2_instance = new $provider2_class_name();

		$this->assertEquals( 'Original Provider', $provider1_instance->name );
		$this->assertEquals( 'Override Provider', $provider2_instance->name );
	}
}
