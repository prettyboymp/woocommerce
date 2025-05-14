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
		 * Filter the blocks that are hooked into the Product Details block.
		 *
		 * @hook woocommerce_product_details_hooked_blocks
		 *
		 * @since 10.0.0
		 * @param {array} $hooked_blocks The blocks that are hooked into the Product Details block.
		 * @return {array} The blocks that are hooked into the Product Details block.
		 */
		$hooked_blocks = apply_filters( 'woocommerce_product_details_hooked_blocks', [] );

		$validated_hooked_blocks = array_filter(
			$hooked_blocks,
			function ( $block ) {
				return isset( $block['title'] ) && isset( $block['content'] ) && is_string( $block['title'] ) && is_string( $block['content'] );
			}
		);

		foreach ( $validated_hooked_blocks as $block ) {
			$this->register_hooked_blocks( $block['title'], $block['content'] );
		}
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
		$parsed_block = $block->parsed_block;
		$parsed_block = $this->hide_empty_accordion_items( $parsed_block, $block->context );

		$inner_content = array_reduce(
			$parsed_block['innerBlocks'],
			function ( $carry, $parsed_inner_block ) use ( $block ) {
				$carry .= ( new \WP_Block( $parsed_inner_block, $block->context ) )->render();
				return $carry;
			},
			''
		);

		return sprintf(
			'<div %1$s>%2$s</div>',
			get_block_wrapper_attributes(),
			$inner_content
		);
	}

	/**
	 * Hide empty accordion items.
	 *
	 * @param array $parsed_block Parsed block.
	 * @param array $context Context.
	 *
	 * @return array Parsed block.
	 */
	private function hide_empty_accordion_items( $parsed_block, $context ) {
		if ( ! $this->has_accordion( $parsed_block ) ) {
			return $parsed_block;
		}

		if ( 'woocommerce/accordion-group' === $parsed_block['blockName'] ) {
			foreach ( $parsed_block['innerBlocks'] as $key => $inner_block ) {
				$parsed_block['innerBlocks'][ $key ] = $this->mark_accordion_item_hidden( $inner_block, $context );
			}
			$parsed_block['innerBlocks']  = array_values( array_filter( $parsed_block['innerBlocks'] ) );
			$openning_tag                 = reset( $parsed_block['innerContent'] );
			$closing_tag                  = end( $parsed_block['innerContent'] );
			$parsed_block['innerContent'] = array_merge(
				array( $openning_tag ),
				array_fill( 0, count( $parsed_block['innerBlocks'] ), null ),
				array( $closing_tag )
			);
			return $parsed_block;
		}

		foreach ( $parsed_block['innerBlocks'] as $key => $inner_block ) {
			$parsed_block['innerBlocks'][ $key ] = $this->hide_empty_accordion_items( $inner_block, $context );
		}

		return $parsed_block;
	}

	/**
	 * Mark an accordion item as hidden if it has no content.
	 *
	 * @param array $item Item to mark.
	 * @param array $context Context.
	 *
	 * @return array Item.
	 */
	private function mark_accordion_item_hidden( $item, $context ) {
		$content_block          = end( $item['innerBlocks'] );
		$rendered_content_block = ( new WP_Block( $content_block, $context ) )->render();
		$p                      = new WP_HTML_Tag_Processor( $rendered_content_block );

		$has_content = $p->next_tag( 'img' ) ||
			$p->next_tag( 'iframe' ) ||
			$p->next_tag( 'video' ) ||
			$p->next_tag( 'meter' ) ||
			! empty( wp_strip_all_tags( $rendered_content_block, true ) );

		if ( ! $has_content ) {
			return array();
		}

		return $item;
	}

	/**
	 * Check if a parsed block has an accordion.
	 *
	 * @param array $parsed_block Parsed block.
	 *
	 * @return bool True if the block has an accordion, false otherwise.
	 */
	private function has_accordion( $parsed_block ) {
		if (
			'woocommerce/accordion-group' === $parsed_block['blockName'] &&
			! empty( $parsed_block['attrs']['metadata']['isProductDetailsInnerBlock'] )
		) {
			return true;
		}

		foreach ( $parsed_block['innerBlocks'] as $inner_block ) {
			if ( $this->has_accordion( $inner_block ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Register a product details item using Block Hooks API.
	 *
	 * @param string $title The title of the item.
	 * @param string $content The content of the item.
	 * @return void
	 */
	private function register_hooked_blocks( $title, $content ) {
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
				$parsed_anchor_block
			) use (
				$title,
				$content
			) {

				if ( is_null( $parsed_hooked_block ) ) {
					return $parsed_hooked_block;
				}

				if (
					'woocommerce/accordion-group' !== $parsed_anchor_block['blockName'] ||
					'last_child' !== $relative_position ||
					! isset( $parsed_anchor_block['attrs']['metadata']['isProductDetailsInnerBlock'] ) ||
					! $parsed_anchor_block['attrs']['metadata']['isProductDetailsInnerBlock']
				) {
					return array();
				}

				return $this->create_accordion_item_from_template(
					$this->get_accordion_item_template( $parsed_anchor_block ),
					$title,
					$content
				);
			},
			10,
			4
		);
	}

	/**
	 * Create an accordion item from a template.
	 *
	 * @param array  $template Template.
	 * @param string $title Title.
	 * @param string $content Content.
	 *
	 * @return array Accordion item.
	 */
	private function create_accordion_item_from_template( $template, $title, $content ) {
		foreach ( $template['innerBlocks'] as &$block ) {
			if ( 'woocommerce/accordion-header' === $block['blockName'] ) {
				$block['innerContent'] = array_map(
					function ( $inner_content ) use ( $title ) {
						return str_replace( '{{title}}', $title, $inner_content );
					},
					$block['innerContent']
				);
			}
			if ( 'woocommerce/accordion-panel' === $block['blockName'] ) {
				$block['innerBlocks']  = parse_blocks( $content );
				$openning_tag          = reset( $block['innerContent'] );
				$closing_tag           = end( $block['innerContent'] );
				$block['innerContent'] = array_merge(
					array( $openning_tag ),
					array_fill( 0, count( $block['innerBlocks'] ), null ),
					array( $closing_tag )
				);
			}
		}

		return $template;
	}

	/**
	 * Get the accordion item template.
	 *
	 * @param array $parsed_block Parsed block.
	 *
	 * @return array Accordion item template.
	 */
	private function get_accordion_item_template( $parsed_block ) {
		$current_accordion_item = $this->get_current_accordion_item( $parsed_block );

		$default_template = parse_blocks(
			'<!-- wp:woocommerce/accordion-item -->
			<div class="wp-block-woocommerce-accordion-item"><!-- wp:woocommerce/accordion-header -->
			<h3 class="wp-block-woocommerce-accordion-header accordion-item__heading"><button class="accordion-item__toggle"><span>{{title}}</span><span class="accordion-item__toggle-icon has-icon-plus" style="width:1.2em;height:1.2em"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M11 12.5V17.5H12.5V12.5H17.5V11H12.5V6H11V11H6V12.5H11Z" fill="currentColor"></path></svg></span></button></h3>
			<!-- /wp:woocommerce/accordion-header -->

			<!-- wp:woocommerce/accordion-panel -->
			<div class="wp-block-woocommerce-accordion-panel"><div class="accordion-content__wrapper">
			<!-- wp:paragraph --><p>{{content}}</p><!-- /wp:paragraph -->
			</div></div>
			<!-- /wp:woocommerce/accordion-panel --></div>
			<!-- /wp:woocommerce/accordion-item -->'
		)[0];

		if ( ! $current_accordion_item ) {
			return $default_template;
		}

		foreach ( $current_accordion_item['innerBlocks'] as &$inner_block ) {
			if ( 'woocommerce/accordion-header' === $inner_block['blockName'] ) {
				$inner_block['innerContent'] = array_map(
					function ( $inner_content ) {
						return preg_replace( '/<span>.*?<\/span>/', '<span>{{title}}</span>', $inner_content );
					},
					$inner_block['innerContent']
				);
			}
			/**
			 * If the accordion panel is empty, the innerContent will be a single string item with
			 * both opening and closing tags. We need to replace it with the default template for
			 * create_accordion_item_from_template.
			 */
			if ( 'woocommerce/accordion-panel' === $inner_block['blockName'] && empty( $inner_block['innerBlocks'] ) ) {
				$inner_block['innerContent'] = $default_template['innerBlocks'][1]['innerContent'];
			}
		}

		return $current_accordion_item;
	}

	/**
	 * Get the current accordion item.
	 *
	 * @param array $parsed_block Parsed block.
	 *
	 * @return array Current accordion item.
	 */
	private function get_current_accordion_item( $parsed_block ) {
		if ( 'woocommerce/accordion-group' === $parsed_block['blockName'] && ! empty( $parsed_block['innerBlocks'] ) ) {
			return end( $parsed_block['innerBlocks'] );
		}

		foreach ( $parsed_block['innerBlocks'] as $inner_block ) {
			$current_accordion_item = $this->get_current_accordion_item( $inner_block );
			if ( $current_accordion_item ) {
				return $current_accordion_item;
			}
		}

		return false;
	}
}
