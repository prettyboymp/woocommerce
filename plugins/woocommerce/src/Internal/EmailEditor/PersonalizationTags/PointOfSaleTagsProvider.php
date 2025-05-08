<?php

declare( strict_types=1 );

namespace Automattic\WooCommerce\Internal\EmailEditor\PersonalizationTags;

use Automattic\WooCommerce\EmailEditor\Engine\PersonalizationTags\Personalization_Tag;
use Automattic\WooCommerce\EmailEditor\Engine\PersonalizationTags\Personalization_Tags_Registry;

/**
 * Provider for POS-related personalization tags.
 *
 * @internal
 */
class PointOfSaleTagsProvider extends AbstractTagProvider {
	/**
	 * Register POS tags with the registry.
	 *
	 * @param Personalization_Tags_Registry $registry The personalization tags registry.
	 * @return void
	 */
	public function register_tags( Personalization_Tags_Registry $registry ): void {
		$registry->register(
			new Personalization_Tag(
				__( 'POS Store Name', 'woocommerce' ),
				'woocommerce/pos-store-name',
				__( 'POS', 'woocommerce' ),
				function (): string {
					return get_option( 'woocommerce_pos_store_name' ) ?? '';
				},
			)
		);

		$registry->register(
			new Personalization_Tag(
				__( 'POS Store Email', 'woocommerce' ),
				'woocommerce/pos-store-email',
				__( 'POS', 'woocommerce' ),
				function (): string {
					return get_option( 'woocommerce_pos_store_email' ) ?? '';
				},
			)
		);

		$registry->register(
			new Personalization_Tag(
				__( 'POS Store Phone', 'woocommerce' ),
				'woocommerce/pos-store-phone',
				__( 'POS', 'woocommerce' ),
				function (): string {
					return get_option( 'woocommerce_pos_store_phone' ) ?? '';
				},
			)
		);

		$registry->register(
			new Personalization_Tag(
				__( 'POS Store Address', 'woocommerce' ),
				'woocommerce/pos-store-address',
				__( 'POS', 'woocommerce' ),
				function (): string {
					return get_option( 'woocommerce_pos_store_address' ) ?? '';
				},
			)
		);

		$registry->register(
			new Personalization_Tag(
				__( 'POS Refund & Returns Policy', 'woocommerce' ),
				'woocommerce/pos-refund-returns-policy',
				__( 'POS', 'woocommerce' ),
				function (): string {
					return get_option( 'woocommerce_pos_refund_returns_policy' ) ?? '';
				},
			)
		);
	}
}
