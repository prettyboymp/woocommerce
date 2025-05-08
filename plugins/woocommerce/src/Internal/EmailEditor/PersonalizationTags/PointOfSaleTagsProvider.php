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
				__( 'Point of Sale Store Name', 'woocommerce' ),
				'woocommerce/point-of-sale-store-name',
				__( 'Point of Sale', 'woocommerce' ),
				function (): string {
					return get_option( 'woocommerce_pos_store_name' ) ?? '';
				},
			)
		);

		$registry->register(
			new Personalization_Tag(
				__( 'Point of Sale Store Email', 'woocommerce' ),
				'woocommerce/point-of-sale-store-email',
				__( 'Point of Sale', 'woocommerce' ),
				function (): string {
					return get_option( 'woocommerce_pos_store_email' ) ?? '';
				},
			)
		);

		$registry->register(
			new Personalization_Tag(
				__( 'Point of Sale Store Phone', 'woocommerce' ),
				'woocommerce/point-of-sale-store-phone',
				__( 'Point of Sale', 'woocommerce' ),
				function (): string {
					return get_option( 'woocommerce_pos_store_phone' ) ?? '';
				},
			)
		);

		$registry->register(
			new Personalization_Tag(
				__( 'Point of Sale Store Address', 'woocommerce' ),
				'woocommerce/point-of-sale-store-address',
				__( 'Point of Sale', 'woocommerce' ),
				function (): string {
					return get_option( 'woocommerce_pos_store_address' ) ?? '';
				},
			)
		);

		$registry->register(
			new Personalization_Tag(
				__( 'Point of Sale Refund & Returns Policy', 'woocommerce' ),
				'woocommerce/point-of-sale-refund-returns-policy',
				__( 'Point of Sale', 'woocommerce' ),
				function (): string {
					return get_option( 'woocommerce_pos_refund_returns_policy' ) ?? '';
				},
			)
		);
	}
}
