<?php

declare(strict_types=1);

namespace Automattic\WooCommerce\Blocks;

/**
 * Manages the registration of interactivity settings for WooCommerce blocks.
 * Initialization only happens on the first call to initialize_shared_config.
 * Intended to be used as a singleton.
 */
class InteractivityAPIConfig {

	/**
	 * The namespace for the config.
	 *
	 * @var string
	 */
	private $settings_namespace = 'woocommerce';

	/**
	 * Whether the core config has been registered.
	 *
	 * @var boolean
	 */
	private $core_config_registered = false;

	/**
	 * Initialize the shared core config.
	 */
	public function initialize_shared_config() {
		if ( $this->core_config_registered ) {
			return;
		}

		$this->core_config_registered = true;

		$this->add_shared_config( $this->get_currency_data() );
		$this->add_shared_config( $this->get_locale_data() );
		$this->add_shared_config( $this->get_core_data() );
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
	public function add_shared_config( $data ) {
		if ( ! $this->core_config_registered ) {
			$this->initialize_shared_config();
		}

		wp_interactivity_config( $this->settings_namespace, $data );
	}
}
