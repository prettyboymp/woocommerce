<?php
/**
 * This file is part of the WooCommerce Email Editor package
 *
 * @package Automattic\WooCommerce\EmailEditor
 */

declare( strict_types = 1 );
namespace Automattic\WooCommerce\EmailEditor\Integrations\Core\Renderer\Blocks;

use Automattic\WooCommerce\EmailEditor\Engine\Settings_Controller;

/**
 * Renders the social links block.
 */
class Social_Links extends Abstract_Block_Renderer {

	/**
	 * Renders the block content.
	 *
	 * @param string              $block_content Block content.
	 * @param array               $parsed_block Parsed block.
	 * @param Settings_Controller $settings_controller Settings controller.
	 * @return string
	 */
	protected function render_content( $block_content, array $parsed_block, Settings_Controller $settings_controller ): string {
		/*
		 * No support for size options yet.
		 * 'Small' - 'has-small-icon-size'
		 * 'Normal' - 'has-normal-icon-size'
		 * 'Large' - 'has-large-icon-size'
		 * 'Huge' - 'has-huge-icon-size'
		 */
		// Source: https://github.com/WordPress/gutenberg/blob/406af5f2ccb4da99a983b32f6e191b9b8d907655/packages/block-library/src/social-links/edit.js#L39-L44.

		$attrs = $parsed_block['attrs'] ?? array();

		$inner_blocks = $parsed_block['innerBlocks'] ?? array();

		$content = '';
		foreach ( $inner_blocks as $block ) {
			$content .= $this->generate_social_link_content( $block, $attrs );
		}

		return str_replace(
			'{social_links_content}',
			$content,
			$this->get_block_wrapper( $attrs )
		);
	}

	/**
	 * Generates the social link content.
	 *
	 * @param array $block The block data.
	 * @param array $parent_block_attrs The parent block attributes.
	 * @return string The generated content.
	 */
	private function generate_social_link_content( $block, $parent_block_attrs ) {
		$service_name = $block['attrs']['service'] ?? '';
		$service_url  = $block['attrs']['url'] ?? '';
		$label        = $block['attrs']['label'] ?? '';

		if ( empty( $service_name ) || empty( $service_url ) ) {
			return '';
		}

		$open_in_new_tab = $parent_block_attrs['openInNewTab'] ?? false;
		$show_labels     = $parent_block_attrs['showLabels'] ?? false;

		$icon_color_value            = $parent_block_attrs['iconColorValue'] ?? '';
		$icon_background_color_value = $parent_block_attrs['iconBackgroundColorValue'] ?? '';

		$isLogosOnly = strpos( $parent_block_attrs['className'] ?? '', 'is-style-logos-only' ) !== false;

		$service_icon_url = Social_Link::get_service_icon_url( $service_name, $isLogosOnly ? 'brand' : 'white' );

		$label_html = '';
		if ( $show_labels ) {
			$text       = ! empty( $label ) ? trim( $label ) : '';
			$text       = $text ? $text : block_core_social_link_get_name( $service_name );
			$label_html = sprintf( '<span class="wp-block-social-link-label">%s</span>', esc_html( $text ) );
		}

		$anchor_style = array(
			'color'            => $icon_color_value,
			'background-color' => $icon_background_color_value,
			'text-decoration'  => 'none',
			'text-transform'   => 'none',
			'padding'          => '10px',
		);
		$anchor_html  = sprintf( ' style="%s" ', esc_attr( $this->compile_css( $anchor_style ) ) );
		if ( $open_in_new_tab ) {
			$anchor_html .= ' rel="noopener nofollow" target="_blank"';
		}

		$td_styles = array();

		$styles_css = $this->compile_css( $td_styles );

		$td_attributes = sprintf( 'class="wp-social-link wp-social-link-%1$s wp-block-social-link"', esc_attr( $service_name ) );
		if ( ! empty( $styles_css ) ) {
			$td_attributes .= sprintf( ' style="%s"', esc_attr( $styles_css ) );
		}

		return sprintf(
			'<td %1$s role="presentation" valign="middle">
				<a %2$s href="%3$s" class="wp-block-social-link-anchor">
					<img src="%4$s" alt="%1$s" width="24" height="24" />
					%5$s
				</a>
			</td>',
			$td_attributes, // The td attributes.
			$anchor_html, // The a target and rel attributes.
			esc_url( $service_url ), // The a href link.
			$service_icon_url, // The Img src.
			$label_html // The Label.
		);
	}

	/**
	 * Gets the block wrapper.
	 *
	 * @param array $attrs The block attributes.
	 * @return string The block wrapper HTML.
	 */
	private function get_block_wrapper( $attrs ) {
		$align      = $attrs['align'] ?? '';
		$class_name = $attrs['className'] ?? '';

		if ( ! in_array( $align, array( 'left', 'center', 'right' ), true ) ) {
			$align = 'left';
		}

		return sprintf(
			'<table class="wp-block-social-links %1$s" width="%2$s" border="0" cellpadding="0" cellspacing="0" role="presentation">
				<tbody>
					<tr align="%3$s">
						%4$s
					</tr>
				</tbody>
        	</table>',
			esc_attr( $class_name ),
			'100%', // Width.
			esc_attr( $align ),
			'{social_links_content}'
		);
	}
}
