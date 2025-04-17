<?php
declare( strict_types=1 );
namespace Automattic\WooCommerce\Blocks\Domain\Services;

use WC_Address_Provider;

/**
 * Service class for managing address providers.
 */
class AddressProviderService {

	/**
	 * Get all registered provider class names.
	 *
	 * @return string[] Array of fully qualified class names that extend WC_Address_Provider.
	 */
	public function get_registered_providers(): array {
		/**
		 * Filter the registered address providers.
		 *
		 * @since 10.2.0
		 * @param string[] $providers Array of fully qualified class names that extend WC_Address_Provider.
		 */
		return apply_filters( 'woocommerce_address_providers', array() );
	}

	/**
	 * Check if a specific provider is registered and available.
	 *
	 * @param string $provider_id The provider ID to check.
	 * @return bool
	 */
	public function is_provider_available( string $provider_id ): bool {
		$providers = $this->get_registered_providers();

		foreach ( $providers as $provider_class ) {
			if ( ! class_exists( $provider_class ) ) {
				continue;
			}

			$provider = new $provider_class();
			if ( $provider instanceof WC_Address_Provider && $provider->id === $provider_id ) {
				return true;
			}
		}

		return false;
	}
}
