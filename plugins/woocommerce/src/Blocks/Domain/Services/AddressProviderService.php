<?php
declare( strict_types=1 );
namespace Automattic\WooCommerce\Blocks\Domain\Services;

/**
 * Service class for managing address providers.
 */
class AddressProviderService {
	/**
	 * Registered providers
	 *
	 * @var array
	 */
	private $providers = [];

	/**
	 * Register a new address provider.
	 *
	 * @param string $id   The provider ID.
	 * @param string $name The provider name.
	 * @return bool True if registration was successful, false otherwise.
	 */
	public function register_provider( string $id, string $name ): bool {
		if ( empty( $id ) ) {
			_doing_it_wrong(
				'__experimental_woocommerce_register_address_provider',
				'Unable to register provider. The provider ID is required.',
				'10.1.0'
			);
			return false;
		}

		if ( empty( $name ) ) {
			_doing_it_wrong(
				'__experimental_woocommerce_register_address_provider',
				esc_html( sprintf( 'Unable to register provider with id: "%s". The provider name is required.', $id ) ),
				'10.1.0'
			);
			return false;
		}

		if ( isset( $this->providers[ $id ] ) ) {
			_doing_it_wrong(
				'__experimental_woocommerce_register_address_provider',
				esc_html( sprintf( 'Unable to register provider with id: "%s". The provider is already registered.', $id ) ),
				'10.1.0'
			);
			return false;
		}

		$this->providers[ $id ] = [
			'id'   => $id,
			'name' => $name,
		];

		return true;
	}

	/**
	 * Get all registered providers.
	 *
	 * @return array
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
