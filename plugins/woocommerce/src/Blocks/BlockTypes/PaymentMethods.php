<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use WP_Block;

/**
 * PaymentMethods class.
 *
 * @internal
 */
class PaymentMethods extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'payment-methods';

	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @param string $key Data to get, or default to everything.
	 * @return array|string
	 */
	protected function get_block_type_script( $key = null ) {
		return null;
	}

	/**
	 * Get the frontend style handle for this block type.
	 *
	 * @return string[]
	 */
	protected function get_block_type_style() {
		return array_merge( parent::get_block_type_style(), [ 'wc-blocks-packages-style' ] );
	}

	/**
	 * Render the block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		$payment_methods = $attributes['formattedPaymentMethods'];
		$output = '';
		$show_as_icons = isset( $attributes['showAsIcons'] ) ? $attributes['showAsIcons'] : false;

		if ( ! empty( $payment_methods ) ) {
			$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'wc-block-payment-methods' ] );
			$output .= sprintf( '<div %s>', $wrapper_attributes );
			$output .= '<ul class="wc-block-payment-methods__list">';
			foreach ( $payment_methods as $method ) {
				if ( $show_as_icons && ! empty( $method['icons'] ) && isset( $method['icons'][0] ) ) {
					$output .= sprintf(
						'<li class="wc-block-payment-methods__list-item"><img src="%s" alt="%s" class="wc-block-payment-methods__list-item-icon"></li>',
						esc_url( $method['icons'][0] ),
						esc_attr( $method['ariaLabel'] )
					);
				} else {
					$output .= sprintf(
						'<li class="wc-block-payment-methods__list-item">%s</li>',
						esc_html( $method['ariaLabel'] )
					);
				}
			}
			$output .= '</ul>';
			$output .= '</div>';
		} else {
			$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'wc-block-payment-methods wc-block-payment-methods--empty' ] );
			$output .= sprintf( '<div %s>', $wrapper_attributes );
			$output .= '</div>';
		}

		return $output;
	}

	/**
	 * Enqueue frontend assets for this block, just in time for rendering.
	 *
	 * @param array    $attributes Any attributes that currently are available from the block.
	 * @param string   $content    The block content.
	 * @param WP_Block $block      The block object.
	 */
	protected function enqueue_assets( array $attributes, $content = '', $block = null ) {
		parent::enqueue_assets( $attributes, $content, $block );
	}
}
