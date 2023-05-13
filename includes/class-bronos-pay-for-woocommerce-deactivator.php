<?php
/**
 * Fired during plugin deactivation.
 *
 * @link       https://payments.bronos.org
 * @since      1.0.0
 *
 * @package    BronosPay_For_Woocommerce
 * @subpackage BronosPay_For_Woocommerce/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    BronosPay_For_Woocommerce
 * @subpackage BronosPay_For_Woocommerce/includes
 * @author     BronosPay <support@bronos.org>
 */
class BronosPay_For_Woocommerce_Deactivator {

	/**
	 * Delete plugin settings.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		delete_option( 'woocommerce_bronos_pay_settings' );
	}

}
