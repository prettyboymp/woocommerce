<?php
/**
 * This file is part of the WooCommerce Email Editor package
 *
 * @package Automattic\WooCommerce\EmailEditor
 */

declare( strict_types = 1 );
namespace Automattic\WooCommerce\EmailEditor\Integrations\Core\Renderer\Blocks;

use Automattic\WooCommerce\EmailEditor\Engine\Settings_Controller;
use SVG\SVG;

/**
 * Renders a social link block.
 */
class Social_Link extends Abstract_Block_Renderer {

	/**
	 * Renders the block content.
	 * Note: This is currently not used in the email editor.
	 *
	 * @param string              $block_content Block content.
	 * @param array               $parsed_block Parsed block.
	 * @param Settings_Controller $settings_controller Settings controller.
	 * @return string
	 */
	protected function render_content( $block_content, array $parsed_block, Settings_Controller $settings_controller ): string {
		// not using this for now.
		return $block_content;
	}

	public static function get_service_icon_url( $service ) {
		$services = block_core_social_link_services();

		if ( !isset( $services[ $service ] ) ) {
			return '';
		}

		$service_data = $services[ $service ];

		// get url to icons/service.png
		$service_icon_url = self::get_service_png_url( $service );

		if ( $service_icon_url && ! file_exists( self::get_service_png_path( $service ) ) ) {
			// create image file from svg
		 	$service_icon_url = self::create_image_from_svg( $service, $service_data );
		}

		return $service_icon_url;
	}

	public static function create_image_from_svg( $service, $service_data ) {
		// we can use PHP-SVG library to create an image from the SVG
		// https://github.com/meyfa/php-svg
		$svg = $service_data['icon'];

    	$image = SVG::fromString($svg);
   	 	// the background will be transparent by default.
		// TODO: Image quality is not good. Need to fix.
    	$rasterImage = $image->toRasterImage(24, 24);
    	imagepng($rasterImage, self::get_service_png_path( $service ) );

		return self::get_service_png_url( $service );
	}

	public static function get_service_png_url( $service ) {
		return plugins_url( 'icons/' . $service . '.png', __FILE__ );
	}

	public static function get_service_png_path( $service ) {
		return dirname( __FILE__ ) . "/icons/{$service}.png";
	}
}


/**
 * Renders a social links block.
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
		// no support for size options yet.
		const sizeOptions = [
			{ name: __( 'Small' ), value: 'has-small-icon-size' },
			{ name: __( 'Normal' ), value: 'has-normal-icon-size' },
			{ name: __( 'Large' ), value: 'has-large-icon-size' },
			{ name: __( 'Huge' ), value: 'has-huge-icon-size' },
		];
*/
// source: https://github.com/WordPress/gutenberg/blob/406af5f2ccb4da99a983b32f6e191b9b8d907655/packages/block-library/src/social-links/edit.js#L39-L44

		$attrs = $parsed_block['attrs'] ?? array();

		$innerBlocks = $parsed_block['innerBlocks'] ?? array();

		$content = '';
		foreach ( $innerBlocks as $block ) {
			$content .= $this->generate_social_link_content( $block,  $attrs);
		}

		return str_replace(
			'{social_links_content}',
			$content,
			$this->get_block_wrapper( $attrs )
		);
	}

	private function generate_social_link_content( $block, $parent_block_attrs ) {
		$service_name = $block['attrs']['service'] ?? '';
		$service_url = $block['attrs']['url'] ?? '';
		$label = $block['attrs']['label'] ?? '';

		if ( empty( $service_name ) || empty( $service_url ) ) {
			return '';
		}

		$open_in_new_tab = $parent_block_attrs['openInNewTab'] ?? false;
		$show_labels = $parent_block_attrs['showLabels'] ?? false;

		$icon_color_value = $parent_block_attrs['iconColorValue'] ?? '';
		$icon_background_color_value = $parent_block_attrs['iconBackgroundColorValue'] ?? '';

		$service_icon_url = Social_Link::get_service_icon_url( $service_name );

		$label_html = '';
		if ( $show_labels ) {
			$text = ! empty( $label ) ? trim( $label ) : '';
			$text        = $text ? $text : block_core_social_link_get_name( $service_name );
			$label_html = sprintf( '<span class="wp-block-social-link-label">%s</span>', esc_html( $text ) );
		}

		$anchor_style = array(
			'color' => $icon_color_value,
			'background-color' => $icon_background_color_value,
			'text-decoration' => 'none',
			'text-transform' => 'none',
			'padding' => '10px',
		);
		$anchor_html = sprintf( ' style="%s" ', esc_attr( $this->compile_css( $anchor_style ) ) );
		if ( $open_in_new_tab ) {
			$anchor_html .= ' rel="noopener nofollow" target="_blank"';
		}

		$styles = array();

		if ( $icon_color_value ) {
			// $styles['color'] = $icon_color_value; // work-in-progress
		}

		if ( $icon_background_color_value ) {
			// $styles['background-color'] = $icon_background_color_value; // work-in-progress
		}

		$styles_css = $this->compile_css( $styles );

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
			$td_attributes, // td attributes
			$anchor_html, // a target and rel attributes
			esc_url( $service_url ), // a href link
			$service_icon_url, // img src
			$label_html	// label
		);
	}

	private function get_block_wrapper( $attrs ) {
		$align = $attrs['align'] ?? '';
		$class_name = $attrs['className'] ?? '';

		if ( !in_array( $align, array( 'left', 'center', 'right' ) ) ) {
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
			'100%', // width
			esc_attr( $align ),
			'{social_links_content}'
		);
	}
}
