<?php
declare( strict_types=1 );
namespace Automattic\WooCommerce\Blocks\Domain\Services;

use WC_Address_Provider;

/**
 * Service class for managing address providers.
 */
class AddressProviderService {

	/**
	 * Get all registered providers.
	 *
	 * @return WC_Address_Provider[]
	 */
	public function get_registered_providers(): array {
		/**
		 * Filter the registered address providers.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'woocommerce_address_providers', array() );
	}

	/**
	 * Check if a specific provider is registered.
	 *
	 * @param string $provider_id The provider ID to check.
	 * @return bool
	 */
	public function is_provider_available( string $provider_id ): bool {
		$providers = $this->get_registered_providers();
		return isset( $providers[ $provider_id ] );
	}
}
