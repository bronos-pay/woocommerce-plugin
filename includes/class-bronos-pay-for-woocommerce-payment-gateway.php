<?php
/**
 * The functionality of the bronos payment gateway.
 *
 * @link       https://payments.bronos.org
 * @since      1.0.0
 *
 * @package    BronosPay_For_Woocommerce
 * @subpackage BronosPay_For_Woocommerce/includes
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
	return;
}

use BronosPay\Exception\ApiErrorException;
use BronosPay\Client;

/**
 * The functionality of the bronos payment gateway.
 *
 * @since      1.0.0
 * @package    BronosPay_For_Woocommerce
 * @subpackage BronosPay_For_Woocommerce/includes
 * @author     BronosPay <support@bronos.org>
 */
class BronosPay_For_Woocommerce_Payment_Gateway extends WC_Payment_Gateway {

	public const ORDER_TOKEN_META_KEY = 'bronos_pay_order_token';

	public const SETTINGS_KEY = 'woocommerce_bronos_pay_settings';

	/**
	 * Bronos_Payment_Gateway constructor.
	 */
	public function __construct() {
		$this->id = 'bronos_pay';
		$this->has_fields = false;
		$this->method_title = 'Bronos Pay';
		$this->icon = apply_filters( 'woocommerce_bronos_pay_icon', BRONOS_PAY_FOR_WOOCOMMERCE_PLUGIN_URL . 'assets/bitcoin.png' );

		$this->init_form_fields();
		$this->init_settings();

		$this->title = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		// $this->api_secret = $this->get_option( 'api_secret' );
		$this->api_auth_token = $this->get_option( 'api_auth_token' );  // ( empty( $this->get_option( 'api_auth_token' ) ) ? $this->get_option( 'api_secret' ) : $this->get_option( 'api_auth_token' ) );
		// $this->receive_currency = $this->get_option( 'receive_currency' );
		$this->order_statuses = $this->get_option( 'order_statuses' );
		$this->test = ( 'yes' === $this->get_option( 'test', 'no' ) );

		add_action( 'woocommerce_update_options_payment_gateways_bronos_pay', array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_bronos_pay', array( $this, 'save_order_statuses' ) );
		add_action( 'woocommerce_thankyou_bronos_pay', array( $this, 'thankyou' ) );
		add_action( 'woocommerce_api_wc_gateway_bronos_pay', array( $this, 'payment_callback' ) );
	}

	/**
	 * Output the gateway settings screen.
	 */
	public function admin_options() {
		?>
		<h3>
			<?php
			esc_html_e( 'Bronos Pay', 'bronos-pay' );
			?>
		</h3>
		<p>
			<?php
			esc_html_e(
				'Accept Bitcoin through the Bronos Pay and receive payments in euros and US dollars.',
				'bronos-pay'
			);
			?>
			<br>
			<a href="https://developer.bronos.org/docs/issues" target="_blank">
			<?php
			esc_html_e( 'Not working? Common issues' );
			?>
			</a> &middot;
			<a href="mailto:support@bronos.org">support@bronos.org</a>
		</p>
		<table class="form-table">
			<?php
				$this->generate_settings_html();
			?>
		</table>
		<?php
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'       => __( 'Enable Bronos Pay', 'bronos-pay' ),
				'label'       => __( 'Enable Cryptocurrency payments via Bronos Pay', 'bronos-pay' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'description' => array(
				'title'       => __( 'Description', 'bronos-pay' ),
				'type'        => 'textarea',
				'description' => __( 'The payment method description which a user sees at the checkout of your store.', 'bronos-pay' ),
				'default'     => __( 'Pay with BTC, LTC, ETH, XMR, XRP, BCH and other cryptocurrencies. Powered by BronosPay.', 'bronos-pay' ),
			),
			'title' => array(
				'title'       => __( 'Title', 'bronos-pay' ),
				'type'        => 'text',
				'description' => __( 'The payment method title which a customer sees at the checkout of your store.', 'bronos-pay' ),
				'default'     => __( 'Cryptocurrencies via BronosPay (more than 50 supported)', 'bronos-pay' ),
			),
			'api_auth_token' => array(
				'title'       => __( 'API Auth Token', 'bronos-pay' ),
				'type'        => 'text',
				'description' => __( 'BronosPay API Auth Token', 'bronos-pay' ),
				'default'     => (''), // ( empty( $this->get_option( 'api_secret' ) ) ? '' : $this->get_option( 'api_secret' ) ),
			),
			// 'receive_currency' => array(
			// 	'title' => __( 'Payout Currency', 'bronos-pay' ),
			// 	'type' => 'select',
			// 	'options' => array(
			// 		'BTC'            => __( 'Bitcoin (฿)', 'bronos-pay' ),
			// 		'USDT'           => __( 'USDT', 'bronos-pay' ),
			// 		'EUR'            => __( 'Euros (€)', 'bronos-pay' ),
			// 		'USD'            => __( 'U.S. Dollars ($)', 'bronos-pay' ),
			// 		'DO_NOT_CONVERT' => __( 'Do not convert', 'bronos-pay' ),
			// 	),
			// 	'description' => __( 'Choose the currency in which your payouts will be made (BTC, EUR or USD). For real-time EUR or USD settlements, you must verify as a merchant on Bronos Pay. Do not forget to add your Bitcoin address or bank details for payouts on <a href="https://merchant.bronos.org" target="_blank">your Bronos Pay account</a>.', 'bronos-pay' ),
			// 	'default' => 'BTC',
			// ),
			'order_statuses' => array(
				'type' => 'order_statuses',
			),
			// 'purchaser_email_status' => array(
			// 	'title'       => __( 'Pre-fill Bronos Pay invoice email', 'bronos-pay' ),
			// 	'type'        => 'checkbox',
			// 	'label'       => __( 'Pre-fill Bronos Pay invoice email', 'bronos-pay' ),
			// 	'default'     => 'yes',
			// 	'description' => __(
			// 		'When this feature is enabled, customer email will be passed to Bronos Pay checkout form automatically. <br>
            //         Email will be used to contact customers by the Bronos Pay team if any payment issues occur.',
			// 		'bronos-pay'
			// 	),
			// ),
			'test' => array(
				'title'       => __( 'Test (Sandbox)', 'bronos-pay' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable Test Mode (Sandbox)', 'bronos-pay' ),
				'default'     => 'no',
				'description' => __(
					'To test on <a href="https://sandbox.bronos.org" target="_blank">Bronos Pay Sandbox</a>, turn Test Mode "On".
					Please note, for Test Mode you must create a separate account on <a href="https://sandbox.bronos.org" target="_blank">sandbox.bronos.org</a> and generate API credentials there.
					API credentials generated on <a href="https://merchant.bronos.org" target="_blank">merchant.bronos.org</a> are "Live" credentials and will not work for "Test" mode.',
					'bronos-pay'
				),
			),
		);
	}

	/**
	 * Thank you page.
	 */
	public function thankyou() {
		$description = $this->get_description();
		if ( $description ) {
			echo '<p>' . esc_html( $description ) . '</p>';
		}
	}

	/**
	 * Validate api_auth_token field.
	 *
	 * @param string $key Field key.
	 * @param string $value Field value.
	 * @return string Returns field value.
	 */
	public function validate_api_auth_token_field( $key, $value ) {
		$post_data = $this->get_post_data();
		$mode = $post_data['woocommerce_bronos_pay_test'];

		if ( ! empty( $value ) ) {
			$client = new Client();
			$client::setAppInfo( 'Bronos Pay For Woocommerce', BRONOS_PAY_FOR_WOOCOMMERCE_VERSION );
			$result = $client::testConnection( $value, (bool) $mode );

			if ( $result ) {
				return $value;
			}
		}

		WC_Admin_Settings::add_error( esc_html__( 'API Auth Token is invalid. Your changes have not been saved. '. $value . $result, 'bronos-pay' ) );

		return '';
	}

	/**
	 * Payment process.
	 *
	 * @param int $order_id The order ID.
	 * @return string[]
	 *
	 * @throws Exception Unknown exception type.
	 */
	public function process_payment( $order_id ) {
		global $woocommerce, $page, $paged;
		$order = wc_get_order( $order_id );

		$client = $this->init_bronos_pay();

		$description = array();
		foreach ( $order->get_items() as $item ) {
			$description[] = $item['qty'] . ' × ' . $item['name'];
		}

		$params = array(
			'reference_id'         => $order->get_id(),
			'amount'     => $order->get_total(),
			'currency'   => $order->get_currency(),
			// 'receive_currency' => $this->receive_currency,
			'callback_url'     => trailingslashit( get_bloginfo( 'wpurl' ) ) . '?wc-api=wc_gateway_bronos_pay',
			'cancel_url'       => $this->get_cancel_order_url( $order ),
			'success_url'      => add_query_arg( 'order-received', $order->get_id(), add_query_arg( 'key', $order->get_order_key(), $this->get_return_url( $order ) ) ),
			'title'            => get_bloginfo( 'name', 'raw' ) . ' Order #' . $order->get_id(),
			'description'      => implode( ', ', $description ),
		);

		// if ( 'yes' === $this->get_option( 'purchaser_email_status' ) ) {
		// 	$params['purchaser_email'] = $order->get_billing_email();
		// }

		$response = array( 'result' => 'fail' );

		try {
			$gateway_order = $client->order->create( $params );
			if ( $gateway_order ) {
				update_post_meta( $order->get_id(), static::ORDER_TOKEN_META_KEY, $gateway_order->_id );
				$response['result'] = 'success';
				$response['redirect'] = $gateway_order->payment_url;
			}
		} catch ( ApiErrorException $exception ) {
			error_log( $exception );
		}

		return $response;
	}

	/**
	 * Payment callback.
	 *
	 * @throws Exception Unknown exception type.
	 */
	public function payment_callback() {
		$request = $_POST;
		$order = wc_get_order( sanitize_text_field( $request['reference_id'] ) );

		if ( ! $this->is_token_valid( $order, sanitize_text_field( $request['_id'] ) ) ) {
			throw new Exception( 'Bronos Pay callback token does not match' );
		}

		if ( ! $order || ! $order->get_id() ) {
			throw new Exception( 'Order #' . $order->get_id() . ' does not exists' );
		}

		if ( $order->get_payment_method() !== $this->id ) {
			throw new Exception( 'Order #' . $order->get_id() . ' payment method is not ' . $this->method_title );
		}

		// Get payment data from request due to security reason.
		$client = $this->init_bronos_pay();
		$bp_order = $client->order->get( (int) sanitize_key( $request['_id'] ) );
		if ( ! $bp_order || $order->get_id() !== (int) $bp_order->reference_id ) {
			throw new Exception( 'Bronos Pay Order #' . $order->get_id() . ' does not exists.' );
		}

		$callback_order_status = sanitize_text_field( $bp_order->status );

		$order_statuses = $this->get_option( 'order_statuses' );
		$wc_order_status = isset( $order_statuses[ $callback_order_status ] ) ? $order_statuses[ $callback_order_status ] : null;
		if ( ! $wc_order_status ) {
			return;
		}

		switch ( $callback_order_status ) {
			case 'paid':
				if ( ! $this->is_order_paid_status_valid( $order, $bp_order->amount ) ) {
					throw new Exception( 'Bronos Pay Order #' . $order->get_id() . ' amounts do not match' );
				}

				$status_was = 'wc-' . $order->get_status();

				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Payment is confirmed on the network, and has been credited to the merchant. Purchased goods/services can be securely delivered to the buyer.', 'bronos-pay' ) );
				$order->payment_complete();

				$wc_expired_status = $order_statuses['expired'];
				$wc_canceled_status = $order_statuses['cancelled'];

				if ( 'processing' === $order->status && ( $status_was === $wc_expired_status || $status_was === $wc_canceled_status ) ) {
					WC()->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger( $order->get_id() );
				}
				if ( ( 'processing' === $order->status || 'completed' === $order->status ) && ( $status_was === $wc_expired_status || $status_was === $wc_canceled_status ) ) {
					WC()->mailer()->emails['WC_Email_New_Order']->trigger( $order->get_id() );
				}
				break;
			case 'confirmed':
				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Shopper transferred the payment for the invoice. Awaiting blockchain network confirmation.', 'bronos-pay' ) );
				break;
			case 'invalid':
				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Payment rejected by the network or did not confirm within 7 days.', 'bronos-pay' ) );
				break;
			case 'expired':
				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Buyer did not pay within the required time and the invoice expired.', 'bronos-pay' ) );
				break;
			case 'cancelled':
				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Buyer canceled the invoice.', 'bronos-pay' ) );
				break;
			case 'refunded':
				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Payment was refunded to the buyer.', 'bronos-pay' ) );
				break;
		}
	}

	/**
	 * Generates a URL so that a customer can cancel their (unpaid - pending) order.
	 *
	 * @param WC_Order $order    Order.
	 * @param string   $redirect Redirect URL.
	 * @return string
	 */
	public function get_cancel_order_url( $order, $redirect = '' ) {
		return apply_filters(
			'woocommerce_get_cancel_order_url',
			wp_nonce_url(
				add_query_arg(
					array(
						'order'    => $order->get_order_key(),
						'order_id' => $order->get_id(),
						'redirect' => $redirect,
					),
					$order->get_cancel_endpoint()
				),
				'woocommerce-cancel_order'
			)
		);
	}

	/**
	 * Generate order statuses.
	 *
	 * @return false|string
	 */
	public function generate_order_statuses_html() {
		ob_start();

		$bp_statuses = $this->bronos_pay_order_statuses();
		$default_status['ignore'] = __( 'Do nothing', 'bronos-pay' );
		$wc_statuses = array_merge( $default_status, wc_get_order_statuses() );

		$default_statuses = array(
			'paid'       => 'wc-processing',
			'confirmed' => 'ignore',
			'invalid'    => 'ignore',
			'expired'    => 'ignore',
			'cancelled'   => 'ignore',
			'refunded'   => 'ignore',
		);

		?>
		<tr valign="top">
			<th scope="row" class="titledesc"> <?php esc_html_e( 'Order Statuses:', 'bronos-pay' ); ?></th>
			<td class="forminp" id="bronos_pay_order_statuses">
				<table cellspacing="0">
					<?php
					foreach ( $bp_statuses as $bp_status_name => $bp_status_title ) {
						?>
						<tr>
							<th><?php echo esc_html( $bp_status_title ); ?></th>
							<td>
								<select name="woocommerce_bronos_pay_order_statuses[<?php echo esc_html( $bp_status_name ); ?>]">
									<?php
									$bp_settings = get_option( static::SETTINGS_KEY );
									$order_statuses = $bp_settings['order_statuses'];

									foreach ( $wc_statuses as $wc_status_name => $wc_status_title ) {
										$current_status = isset( $order_statuses[ $bp_status_name ] ) ? $order_statuses[ $bp_status_name ] : null;

										if ( empty( $current_status ) ) {
											$current_status = $default_statuses[ $bp_status_name ];
										}

										if ( $current_status === $wc_status_name ) {
											echo '<option value="' . esc_attr( $wc_status_name ) . '" selected>' . esc_html( $wc_status_title ) . '</option>';
										} else {
											echo '<option value="' . esc_attr( $wc_status_name ) . '">' . esc_html( $wc_status_title ) . '</option>';
										}
									}
									?>
								</select>
							</td>
						</tr>
						<?php
					}
					?>
				</table>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Validate order statuses field.
	 *
	 * @return mixed|string
	 */
	public function validate_order_statuses_field() {
		$order_statuses = $this->get_option( 'order_statuses' );

		if ( isset( $_POST[ $this->plugin_id . $this->id . '_order_statuses' ] ) ) {
			return array_map(
				'sanitize_text_field',
				wp_unslash( $_POST[ $this->plugin_id . $this->id . '_order_statuses' ] )
			);
		}

		return $order_statuses;
	}

	/**
	 * Save order statuses.
	 */
	public function save_order_statuses() {
		$bronos_pay_order_statuses = $this->bronos_pay_order_statuses();
		$wc_statuses = wc_get_order_statuses();

		if ( isset( $_POST['woocommerce_bronos_pay_order_statuses'] ) ) {
			$bp_settings = get_option( static::SETTINGS_KEY );
			$order_statuses = $bp_settings['order_statuses'];

			foreach ( $bronos_pay_order_statuses as $bp_status_name => $bp_status_title ) {
				if ( ! isset( $_POST['woocommerce_bronos_pay_order_statuses'][ $bp_status_name ] ) ) {
					continue;
				}

				$wc_status_name = sanitize_text_field( wp_unslash( $_POST['woocommerce_bronos_pay_order_statuses'][ $bp_status_name ] ) );

				if ( array_key_exists( $wc_status_name, $wc_statuses ) ) {
					$order_statuses[ $bp_status_name ] = $wc_status_name;
				}
			}

			$bp_settings['order_statuses'] = $order_statuses;
			update_option( static::SETTINGS_KEY, $bp_settings );
		}
	}

	/**
	 * Handle order status.
	 *
	 * @param WC_Order $order  The order.
	 * @param string   $status Order status.
	 */
	protected function handle_order_status( WC_Order $order, string $status ) {
		if ( 'ignore' !== $status ) {
			$order->update_status( $status );
		}
	}

	/**
	 * List of bronos pay order statuses.
	 *
	 * @return string[]
	 */
	private function bronos_pay_order_statuses() {
		return array(
			'paid'       => 'Paid',
			'confirmed' => 'Confirming',
			'invalid'    => 'Invalid',
			'expired'    => 'Expired',
			'cancelled'   => 'Canceled',
			'refunded'   => 'Refunded',
		);
	}

	/**
	 * Initial client.
	 *
	 * @return Client
	 */
	private function init_bronos_pay() {
		$auth_token = $this->api_auth_token; // ( empty( $this->api_auth_token ) ? $this->api_secret : $this->api_auth_token );
		$client = new Client( $auth_token, $this->test );
		$client::setAppInfo( 'Bronos Pay For Woocommerce', BRONOS_PAY_FOR_WOOCOMMERCE_VERSION );

		return $client;
	}

	/**
	 * Check if order status is valid.
	 *
	 * @param WC_Order $order The order.
	 * @param mixed    $price Price.
	 * @return bool
	 */
	private function is_order_paid_status_valid( WC_Order $order, $price ) {
		return $order->get_total() >= (float) $price;
	}

	/**
	 * Check token match.
	 *
	 * @param WC_Order $order The order.
	 * @param string   $token Token.
	 * @return bool
	 */
	private function is_token_valid( WC_Order $order, string $token ) {
		$order_token = $order->get_meta( static::ORDER_TOKEN_META_KEY );

		return ! empty( $order_token ) && $token === $order_token;
	}

}
