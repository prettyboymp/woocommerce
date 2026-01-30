<?php
declare(strict_types=1);

namespace Automattic\WooCommerce\Blocks\BlockTypes\AddToCartWithOptions;

use Automattic\WooCommerce\Blocks\BlockTypes\AbstractBlock;
use Automattic\WooCommerce\Blocks\BlockTypes\EnableBlockJsonAssetsTrait;
use Automattic\WooCommerce\Blocks\BlockTypes\AddToCartWithOptions\Utils as AddToCartWithOptionsUtils;
use WP_Block;

/**
 * Block type for variation selector item in add to cart with options.
 * It's responsible to render each child attribute in a form of a list item.
 */
class VariationSelectorAttribute extends AbstractBlock {

	use EnableBlockJsonAssetsTrait;

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'add-to-cart-with-options-variation-selector-attribute';

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

		$content = '';

		$product_attributes = $product->get_variation_attributes();

		foreach ( $product_attributes as $product_attribute_name => $product_attribute_terms ) {
			$content .= $this->get_product_row( $product_attribute_name, $product_attribute_terms, $block );
		}

		return $content;
	}

	/**
	 * Get product row HTML.
	 *
	 * @param string   $attribute_name Product Attribute Name.
	 * @param array    $product_attribute_terms Product Attribute Terms.
	 * @param WP_Block $block The Block.
	 * @return string Row HTML
	 */
	private function get_product_row( $attribute_name, $product_attribute_terms, $block ): string {
		global $product;

		$attribute_terms = $this->get_terms( $attribute_name, $product_attribute_terms );

		// Check if we're over the variation threshold.
		// For products with many variations, use an efficient query instead of loading all variation objects.
		if ( $this->is_over_variation_threshold( $product ) ) {
			$available_values = $this->get_available_attribute_values( $product, $attribute_name );

			// Filter terms to only those available in variations.
			$attribute_terms = array_filter(
				$attribute_terms,
				function ( $term ) use ( $available_values ) {
					return in_array( $term['value'], $available_values, true ) || in_array( '', $available_values, true );
				}
			);
		} else {
			// For products under threshold, use existing behavior with full variation objects.
			$product_variations = $product->get_available_variations( 'objects' );

			// Filter out terms which are not available in any product variation.
			$attribute_terms = array_filter(
				$attribute_terms,
				function ( $term ) use ( $product_variations, $attribute_name ) {
					foreach ( $product_variations as $variation ) {
						$attributes = $variation->get_variation_attributes();
						if (
							$term['value'] === $attributes[ wc_variation_attribute_name( $attribute_name ) ] ||
							'' === $attributes[ wc_variation_attribute_name( $attribute_name ) ]
						) {
							return true;
						}
					}
				}
			);
		}

		if ( empty( $attribute_terms ) ) {
			return '';
		}

		$block_content = AddToCartWithOptionsUtils::render_block_with_context(
			$block,
			array(
				'woocommerce/attributeId'    => 'wc_product_attribute_' . uniqid(),
				'woocommerce/attributeName'  => $attribute_name,
				'woocommerce/attributeTerms' => $attribute_terms,
			),
		);

		// Render the inner blocks of the Variation Selector Item Template block with `dynamic` set to `false`
		// to prevent calling `render_callback` and ensure that no wrapper markup is included.
		return $block_content;
	}

	/**
	 * Check if the product's variation count exceeds the threshold.
	 *
	 * Uses the same filter as legacy templates for consistency.
	 *
	 * @param \WC_Product_Variable $product The variable product.
	 * @return bool True if over threshold, false otherwise.
	 */
	private function is_over_variation_threshold( $product ): bool {
		if ( ! $product->is_type( 'variable' ) ) {
			return false;
		}

		$threshold       = apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
		$variation_count = count( $product->get_children() );

		return $variation_count > $threshold;
	}

	/**
	 * Efficiently get available attribute values without loading full variation objects.
	 *
	 * This method queries postmeta directly to get distinct attribute values,
	 * avoiding the overhead of loading complete variation objects.
	 *
	 * @param \WC_Product_Variable $product The variable product.
	 * @param string               $attribute_name The attribute name to query.
	 * @return array Array of available attribute values (slugs).
	 */
	private function get_available_attribute_values( $product, $attribute_name ): array {
		global $wpdb;

		$variation_ids = $product->get_children();
		if ( empty( $variation_ids ) ) {
			return array();
		}

		$meta_key = wc_variation_attribute_name( $attribute_name );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $variation_ids is sanitized with intval.
		$values = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT meta_value
				FROM {$wpdb->postmeta}
				WHERE post_id IN (" . implode( ',', array_map( 'intval', $variation_ids ) ) . ")
				AND meta_key = %s
				AND meta_value != ''",
				$meta_key
			)
		);

		return $values ?: array();
	}

	/**
	 * Get product attributes terms.
	 *
	 * @param string $attribute_name Product Attribute Name.
	 * @param array  $attribute_terms Product Attribute Terms.
	 * @return array[] Array of term data with structure:
	 *                 [
	 *                     'label'      => (string) Display label for the term.
	 *                     'value'      => (string) Internal value/slug for the term.
	 *                     'isSelected' => (bool)   Whether this term is the default selection.
	 *                 ]
	 */
	protected function get_terms( $attribute_name, $attribute_terms ) {
		global $product;

		$is_taxonomy = taxonomy_exists( $attribute_name );

		$selected_attribute = $product->get_variation_default_attribute( $attribute_name );

		if ( $is_taxonomy ) {
			$items = array_map(
				function ( $term ) use ( $attribute_name, $product, $selected_attribute ) {
					return array(
						'value'      => $term->slug,
						/**
						 * Filter the variation option name.
						 *
						 * @since 9.7.0
						 *
						 * @param string     $option_label    The option label.
						 * @param WP_Term|string|null $item   Term object for taxonomies, option string for custom attributes.
						 * @param string     $attribute_name  Name of the attribute.
						 * @param WC_Product $product         Product object.
						 */
						'label'      => apply_filters(
							'woocommerce_variation_option_name',
							$term->name,
							$term,
							$attribute_name,
							$product
						),
						'isSelected' => $selected_attribute === $term->slug,
					);
				},
				wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) ),
			);
		} else {
			$items = array_map(
				function ( $term ) use ( $attribute_name, $product, $selected_attribute ) {
					return array(
						'value'      => $term,
						/**
						 * Filter the variation option name.
						 *
						 * @since 9.7.0
						 *
						 * @param string     $option_label    The option label.
						 * @param WP_Term|string|null $item   Term object for taxonomies, option string for custom attributes.
						 * @param string     $attribute_name  Name of the attribute.
						 * @param WC_Product $product         Product object.
						 */
						'label'      => apply_filters(
							'woocommerce_variation_option_name',
							$term,
							null,
							$attribute_name,
							$product
						),
						'isSelected' => $selected_attribute === $term,
					);
				},
				$attribute_terms,
			);
		}

		return $items;
	}
}
