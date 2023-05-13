<?php
/**
 * Define the internationalization functionality.
 *
 * @link       https://payments.bronos.org
 * @since      1.0.0
 *
 * @package    BronosPay_For_Woocommerce
 * @subpackage BronosPay_For_Woocommerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    BronosPay_For_Woocommerce
 * @subpackage BronosPay_For_Woocommerce/includes
 * @author     BronosPay <support@bronos.org>
 */
class BronosPay_For_Woocommerce_I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'bronos-pay-for-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
