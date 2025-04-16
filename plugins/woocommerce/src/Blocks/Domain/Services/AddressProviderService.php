<?php
declare( strict_types=1 );
namespace Automattic\WooCommerce\Blocks\Domain\Services;

use WC_Address_Provider;

/**
 * Service class for managing address providers.
 */
class AddressProviderService {
	/**
	 * Registered providers
	 *
	 * @var WC_Address_Provider[]
	 */
	private $providers = [];

	/**
	 * Register a new address provider.
	 *
	 * @param WC_Address_Provider $provider The address provider instance.
	 * @return bool True if registration was successful, false otherwise.
	 */
	public function register_provider( WC_Address_Provider $provider ): bool {
		if ( empty( $provider->id ) ) {
			_doing_it_wrong(
				'__experimental_woocommerce_register_address_provider',
				'Unable to register provider. The provider ID is required.',
				'10.1.0'
			);
			return false;
		}

		if ( empty( $provider->name ) ) {
			_doing_it_wrong(
				'__experimental_woocommerce_register_address_provider',
				esc_html( sprintf( 'Unable to register provider with id: "%s". The provider name is required.', $provider->id ) ),
				'10.1.0'
			);
			return false;
		}

		if ( isset( $this->providers[ $provider->id ] ) ) {
			_doing_it_wrong(
				'__experimental_woocommerce_register_address_provider',
				esc_html( sprintf( 'Unable to register provider with id: "%s". The provider is already registered.', $provider->id ) ),
				'10.1.0'
			);
			return false;
		}

		$this->providers[ $provider->id ] = $provider;

		return true;
	}

	/**
	 * Get all registered providers.
	 *
	 * @return WC_Address_Provider[]
	 */
	public function get_registered_providers(): array {
		return $this->providers;
	}

	/**
	 * Check if a specific provider is registered.
	 *
	 * @param string $provider_id The provider ID to check.
	 * @return bool
	 */
	public function is_provider_available( string $provider_id ): bool {
		return isset( $this->providers[ $provider_id ] );
	}
}
