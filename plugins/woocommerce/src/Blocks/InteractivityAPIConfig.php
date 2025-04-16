<?php

declare(strict_types=1);

namespace Automattic\WooCommerce\Blocks;

/**
 * Manages the registration of interactivity settings for WooCommerce blocks.
 * Initialization only happens on the first call to init(). Intended to be used as a singleton.
 */
class InteractivityAPIConfig {

	/**
	 * The namespace for the settings.
	 *
	 * @var string
	 */
	private $settings_namespace = 'woocommerce';

	/**
	 * Whether the core settings have been registered.
	 *
	 * @var boolean
	 */
	private $core_settings_registered = false;

	/**
	 * Initialize the core settings.
	 */
	private function initialize() {
		if ( self::$core_settings_registered ) {
			return;
		}

		$this->core_settings_registered = true;

		$this->add( $this->get_currency_data() );
		$this->add( $this->get_locale_data() );
		$this->add( $this->get_core_data() );
	}

	/**
	 * Get core data to include in settings.
	 *
	 * @return array
	 */
	protected function get_core_data() {
		return [
			'isBlockTheme' => wp_is_block_theme(),
		];
	}

	/**
	 * Get currency data to include in settings.
	 *
	 * @return array
	 */
	protected function get_currency_data() {
		$currency = get_woocommerce_currency();

		return [
			'currency' => [
				'code'              => $currency,
				'precision'         => wc_get_price_decimals(),
				'symbol'            => html_entity_decode( get_woocommerce_currency_symbol( $currency ) ),
				'symbolPosition'    => get_option( 'woocommerce_currency_pos' ),
				'decimalSeparator'  => wc_get_price_decimal_separator(),
				'thousandSeparator' => wc_get_price_thousand_separator(),
				'priceFormat'       => html_entity_decode( get_woocommerce_price_format() ),
			],
		];
	}

	/**
	 * Get locale data to include in settings.
	 *
	 * @return array
	 */
	protected function get_locale_data() {
		global $wp_locale;

		return [
			'locale' => [
				'siteLocale'    => get_locale(),
				'userLocale'    => get_user_locale(),
				'weekdaysShort' => array_values( $wp_locale->weekday_abbrev ),
			],
		];
	}

	/**
	 * Interface for adding block settings to wp_interactivity_config. New settings will be merged with existing settings.
	 *
	 * @param array $data The data to add to the settings.
	 */
	public function add( $data ) {
		if ( ! $this->core_settings_registered ) {
			$this->initialize();
		}

		wp_interactivity_config( self::$settings_namespace, $data );
	}
}
