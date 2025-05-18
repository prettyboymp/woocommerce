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
	 *
	 * @param string              $block_content Block content.
	 * @param array               $parsed_block Parsed block.
	 * @param Settings_Controller $settings_controller Settings controller.
	 * @return string
	 */
	protected function render_content( $block_content, array $parsed_block, Settings_Controller $settings_controller ): string {
		// Not using this for now.
		return $block_content;
	}

	/**
	 * Gets the service icon URL.
	 *
	 * @param string $service The service name.
	 * @param string $image_type The image type. e.g 'white', 'brand', 'svg'.
	 * @return string The service icon URL.
	 */
	public static function get_service_icon_url( $service, $image_type = '' ) {
		$services = block_core_social_link_services();

		if ( ! isset( $services[ $service ] ) ) {
			// not in the list of core services.
			return '';
		}

		// Get URL to icons/service.png.
		$service_icon_url = self::get_service_png_url( $service, $image_type );

		if ( $service_icon_url && ! file_exists( self::get_service_png_path( $service, $image_type ) ) ) {
			// Create image file from SVG.
			// We previously created the images from SVGs using create_image_from_svg.
			return '';
		}

		return $service_icon_url;
	}

	/**
	 * Creates an image from SVG.
	 *
	 * @param string $service The service name.
	 * @param array  $service_data The service data.
	 * @return string The service icon URL.
	 */
	public static function create_image_from_svg( $service, $service_data ) {
		// We can use PHP-SVG library to create an image from the SVG.
		// https://github.com/meyfa/php-svg.
		$svg = $service_data['icon'];

		$image = SVG::fromString( $svg );
		// The background will be transparent by default.
		// TODO: Image quality is not good. Need to fix.
		/**
		 * Converts SVG image into a rasterized GD resource of the given size
		 *
		 * @var \GdImage $raster_image - The raster image.
		 */
		$raster_image = $image->toRasterImage( 24, 24 );
		imagepng( $raster_image, self::get_service_png_path( $service ) );

		return self::get_service_png_url( $service );
	}

	/**
	 * Gets the service PNG URL.
	 *
	 * @param string $service The service name.
	 * @param string $image_type The image type. e.g 'white', 'brand', 'svg'.
	 * @return string The service PNG URL.
	 */
	public static function get_service_png_url( $service, $image_type = 'white' ) {
		$file_extension = 'svg' === $image_type ? 'svg' : 'png';
		$file_name      = "/icons/{$service}/{$service}-{$image_type}.{$file_extension}";
		return plugins_url( $file_name, __FILE__ );
	}

	/**
	 * Gets the service PNG path.
	 *
	 * @param string $service The service name.
	 * @param string $image_type The image type. e.g 'white', 'brand', 'svg'.
	 * @return string The service PNG path.
	 */
	public static function get_service_png_path( $service, $image_type = 'white' ) {
		$file_extension = 'svg' === $image_type ? 'svg' : 'png';
		$file_name      = "/icons/{$service}/{$service}-{$image_type}.{$file_extension}";
		return __DIR__ . $file_name;
	}
}
