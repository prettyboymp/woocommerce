<?php
declare(strict_types=1);

namespace Automattic\WooCommerce\Blocks\BlockTypes\AddToCartWithOptions;

use Automattic\WooCommerce\Blocks\BlockTypes\AbstractBlock;
use Automattic\WooCommerce\Blocks\BlockTypes\EnableBlockJsonAssetsTrait;
use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;
use WP_Block;

/**
 * Block type for the label of grouped product selector items in add to cart with options.
 * It's responsible to render the label for each child product.
 */
class GroupedProductSelectorItemLabel extends AbstractBlock {

	use EnableBlockJsonAssetsTrait;

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'add-to-cart-with-options-grouped-product-selector-item-label';

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

		if ( ! isset( $block->context['postId'] ) ) {
			return '';
		}

		$product = wc_get_product( $block->context['postId'] );
		if ( ! $product instanceof \WC_Product ) {
			return '';
		}

		$classes_and_styles = StyleAttributesUtils::get_classes_and_styles_by_attributes( $attributes );

		// Check if there's an input block in the parent block's content
		$has_input = false;
		if ( isset( $block->parent ) && isset( $block->parent->parsed_block['innerBlocks'] ) ) {
			foreach ( $block->parent->parsed_block['innerBlocks'] as $inner_block ) {
				if ( 'woocommerce/add-to-cart-with-options-grouped-product-selector-item-cta' === $inner_block['blockName'] ) {
					$has_input = true;
					break;
				}
			}
		}

		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => $classes_and_styles['classes'],
				'style' => $classes_and_styles['styles'],
			)
		);

		$title = $product->get_title();

		if ( $has_input ) {
			return sprintf(
				'<label %1$s for="%2$s">%3$s</label>',
				$wrapper_attributes,
				esc_attr( 'grouped-product-' . $product->get_id() ),
				esc_html( $title )
			);
		}

		return sprintf(
			'<p %1$s>%2$s</p>',
			$wrapper_attributes,
			esc_html( $title )
		);
	}
} 