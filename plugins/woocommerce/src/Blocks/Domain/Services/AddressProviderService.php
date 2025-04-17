<?php
declare( strict_types=1 );
namespace Automattic\WooCommerce\Blocks\Domain\Services;

use WC_Address_Provider;

/**
 * Service class for managing address providers.
 */
class AddressProviderService {

	/**
	 * Cached provider class names from the last filter call.
	 *
	 * @var string[]
	 */
	private $cached_provider_class_names = [];

	/**
	 * Cached provider instances.
	 *
	 * @var WC_Address_Provider[]
	 */
	private $cached_providers = [];

	/**
	 * Get all registered providers.
	 *
	 * @return WC_Address_Provider[] array of WC_Address_Providers.
	 */
	public function get_registered_providers(): array {
		/**
		 * Filter the registered address providers.
		 *
		 * @since 10.2.0
		 * @param WC_Address_Provider[] $providers Array of fully qualified class names that extend WC_Address_Provider.
		 */
		$provider_class_names = apply_filters( 'woocommerce_address_providers', [] );

		// If the class names haven't changed, return the cached instances
		if ( $this->cached_provider_class_names === $provider_class_names && ! empty( $this->cached_providers ) ) {
			return $this->cached_providers;
		}

		$providers = [];

		foreach ( $provider_class_names as $provider_class_name ) {
			// Ensure the class exists and is a valid WC_Address_Provider subclass.
			if ( class_exists( $provider_class_name ) && is_subclass_of( $provider_class_name, WC_Address_Provider::class ) ) {
				$provider_instance = new $provider_class_name();

				// Validate the instance has the necessary properties.
				if ( ! empty( $provider_instance->id ) && ! empty( $provider_instance->name ) ) {
					$providers[] = $provider_instance;
				}
			}
		}

		// Update the cache
		$this->cached_provider_class_names = $provider_class_names;
		$this->cached_providers = $providers;

		return $providers;
	}

	/**
	 * Check if a specific provider is registered and available.
	 *
	 * @param string $provider_id The provider ID to check.
	 * @return bool
	 */
	public function is_provider_available( string $provider_id ): bool {
		$providers = $this->get_registered_providers();

		foreach ( $providers as $provider ) {
			if ( $provider->id === $provider_id ) {
				return true;
			}
		}

		return false;
	}
}
