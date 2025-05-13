<?php
declare(strict_types=1);

namespace Automattic\WooCommerce\Blocks\BlockTypes\AddToCartWithOptions;

use Automattic\WooCommerce\Blocks\BlockTypes\AbstractBlock;
use Automattic\WooCommerce\Blocks\BlockTypes\EnableBlockJsonAssetsTrait;
use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;
use Automattic\WooCommerce\Blocks\BlockTypes\AddToCartWithOptions\Utils as AddToCartWithOptionsUtils;
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

		$classes_and_styles = StyleAttributesUtils::get_classes_and_styles_by_attributes( $attributes );
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => $classes_and_styles['classes'],
				'style' => $classes_and_styles['styles'],
			)
		);

		$title = $product->get_title();

		if ( isset( $block->context['postId'] ) && $product ) {
			//Button
			if ( ! $product->is_purchasable() || $product->has_options() || ! $product->is_in_stock() ) {
				return sprintf(
					'<p %1$s>%2$s</p>',
					$wrapper_attributes,
					esc_html( $title )
				);
			//Checkbox
			} elseif ( $product->is_sold_individually() ) {
				return sprintf(
					'<label %1$s for="%2$s">%3$s</label>',
					$wrapper_attributes,
					esc_attr( 'quantity-' . $product->get_id() ),
					esc_html( $title )
				);
			//Quantity Selector
			} else {
				$input_id = AddToCartWithOptionsUtils::get_quantity_input_id( $product );

				return sprintf(
					'<label %1$s for="%2$s">%3$s</label>',
					$wrapper_attributes,
					esc_attr( $input_id ),
					esc_html( $title )
				);
			}
		}

		return '';
	}
}
