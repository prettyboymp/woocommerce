<?php
declare(strict_types=1);
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use WP_Block;
use WP_HTML_Tag_Processor;

/**
 * BlockifiedProductDetails class.
 */
class BlockifiedProductDetails extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'blockified-product-details';

	/**
	 * Get the frontend style handle for this block type.
	 *
	 * @return null
	 */
	protected function get_block_type_style() {
		return null;
	}

	/**
	 * Initialize the block type.
	 *
	 * @return void
	 */
	protected function initialize() {
		parent::initialize();

		/**
		 * Filter the items that are hooked into the Product Details block.
		 *
		 * @hook woocommerce_product_details_hooked_items
		 *
		 * @param {array} $hooked_items The items that are hooked into the Product Details block.
		 * @return {array} The items that are hooked into the Product Details block.
		 */
		$hooked_items = apply_filters( 'woocommerce_product_details_hooked_items', [] );

		$validated_hooked_items = array_filter( $hooked_items, function( $item ) {
			return isset( $item['title'] ) && isset( $item['content'] ) && is_string( $item['title'] ) && is_string( $item['content'] );
		} );

		foreach ( $validated_hooked_items as $item ) {
			$this->register_product_details_item( $item['title'], $item['content'] );
		}
	}

	/**
	 * Register a product details item using Block Hooks API.
	 *
	 * @param string $title The title of the item.
	 * @param string $content The content of the item.
	 * @return void
	 */
	protected function register_product_details_item($title, $content) {
		$slug = sanitize_title( $title );

		add_filter(
			'hooked_block_types',
			function ( $hooked_block_types, $relative_position, $anchor_block_type ) use ( $slug ) {
				if ( 'woocommerce/accordion-group' === $anchor_block_type && 'last_child' === $relative_position ) {
					$hooked_block_types[] = $slug;
				}
				return $hooked_block_types;
			},
			10,
			3
		);

		add_filter(
			"hooked_block_{$slug}",
			function (
				$parsed_hooked_block,
				$hooked_block_type,
				$relative_position,
				$parsed_anchor_block,
			) use ( $title, $content ) {

				if ( is_null( $parsed_hooked_block ) ) {
					return $parsed_hooked_block;
				}

				if (
					'woocommerce/accordion-group' !== $parsed_anchor_block['blockName'] ||
					$relative_position !== 'last_child' ||
					! isset( $parsed_anchor_block['attrs']['metadata']['isProductDetailsInnerBlock'] ) ||
					! $parsed_anchor_block['attrs']['metadata']['isProductDetailsInnerBlock']
				) {
					return array();
				}

				$accordion_item_template = '<!-- wp:woocommerce/accordion-item -->
				<div class="wp-block-woocommerce-accordion-item"><!-- wp:woocommerce/accordion-header -->
				<h3 class="wp-block-woocommerce-accordion-header accordion-item__heading"><button class="accordion-item__toggle"><span>%1$s</span><span class="accordion-item__toggle-icon has-icon-plus" style="width:1.2em;height:1.2em"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M11 12.5V17.5H12.5V12.5H17.5V11H12.5V6H11V11H6V12.5H11Z" fill="currentColor"></path></svg></span></button></h3>
				<!-- /wp:woocommerce/accordion-header -->

				<!-- wp:woocommerce/accordion-panel -->
				<div class="wp-block-woocommerce-accordion-panel"><div class="accordion-content__wrapper">%2$s</div></div>
				<!-- /wp:woocommerce/accordion-panel --></div>
				<!-- /wp:woocommerce/accordion-item -->';

				$accordion_item_block = parse_blocks( sprintf( $accordion_item_template, $title, $content ) );

				return $accordion_item_block[0];
			},
			10,
			4
		);
	}

	/**
	 * Render the block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content Block content.
	 * @param WP_Block $block Block instance.
	 *
	 * @return string Rendered block output.
	 */
	protected function render( $attributes, $content, $block ) {
		return $this->hide_empty_accordion_items( $content, $block );
	}

	/**
	 * Hide empty accordion items.
	 *
	 * @param string   $content Block content.
	 * @param WP_Block $block Block instance.
	 *
	 * @return string Rendered block output.
	 */
	private function hide_empty_accordion_items( $content, $block ) {
		$accordion_items = $this->find_accordion_items( $block->parsed_block );

		if ( ! $accordion_items ) {
			return $content;
		}

		$accordion_items_visibility = array_map(
			function ( $item ) use ( $block ) {
				$content_block          = end( $item['innerBlocks'] );
				$rendered_content_block = ( new WP_Block( $content_block, $block->context ) )->render();
				$p                      = new WP_HTML_Tag_Processor( $rendered_content_block );

				return $p->next_tag( 'img' ) ||
					$p->next_tag( 'iframe' ) ||
					$p->next_tag( 'video' ) ||
					$p->next_tag( 'meter' ) ||
					! empty( wp_strip_all_tags( $rendered_content_block, true ) );
			},
			$accordion_items
		);

		$p = new WP_HTML_Tag_Processor( $content );

		$counter = 0;
		while ( $p->next_tag( array( 'class_name' => 'wp-block-woocommerce-accordion-item' ) ) ) {
			if ( ! $accordion_items_visibility[ $counter ] ) {
				$p->set_attribute( 'style', 'display:none;' );
				$p->set_attribute( 'hidden', true );
			}
			++$counter;
		}

		return $p->get_updated_html();
	}

	/**
	 * Find accordion items.
	 *
	 * @param array $block Block instance.
	 *
	 * @return array|false Accordion items.
	 */
	private function find_accordion_items( $block ) {
		if ( 'woocommerce/accordion-group' === $block['blockName'] ) {
			return $block['innerBlocks'];
		}

		foreach ( $block['innerBlocks'] as $inner_block ) {
			$items = $this->find_accordion_items( $inner_block );
			if ( $items ) {
				return $items;
			}
		}

		return false;
	}

	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @see $this->register_block_type()
	 * @param string $key Data to get, or default to everything.
	 * @return array|string|null
	 */
	protected function get_block_type_script( $key = null ) {
		return null;
	}
}
