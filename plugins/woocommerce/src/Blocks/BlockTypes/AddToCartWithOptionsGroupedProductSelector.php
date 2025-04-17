<?php
declare(strict_types=1);

namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * Block type for grouped product selector in add to cart with options.
 */
class AddToCartWithOptionsGroupedProductSelector extends AbstractBlock {

	use EnableBlockJsonAssetsTrait;

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'add-to-cart-with-options-grouped-product-selector';

	/**
	 * Render the block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content Block content.
	 * @param WP_Block $block Block instance.
	 * @return string Rendered block output.
	 */
	protected function render( $attributes, $content, $block ): string {
		global $product;

		wp_enqueue_script_module( $this->get_full_block_name() );

		if ( $product instanceof \WC_Product && $product->is_type( 'grouped' ) ) {
			return $content;
		}

		return '';
	}
}
