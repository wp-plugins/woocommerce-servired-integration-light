<?php
/**
 * class-wc-gateway-servired-light.php
 *
 * Copyright (c) Antonio Blanco www.plugintpv.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author PluginTPV
 * @package woocommerce-servired-light
 * @since woocommerce 2.0.0
 */

class WC_Gateway_Servired_Light extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		$this->id				= 'servired_light';
		$this->icon 			= apply_filters('woocommerce_servired_light_icon', WOOCOMMERCE_SERVIRED_LIGHT_URL . '/logo.gif');
		$this->has_fields 		= false;
		$this->method_title     = __( 'Servired Light', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title 			= apply_filters( 'wooservired_light_title', $this->get_option( 'title' ) );
		$this->description      = apply_filters( 'wooservired_light_description', $this->get_option( 'description' ) );
		
		$this->commerce 		= $this->get_option( 'commerce' );
		$this->terminal 		= $this->get_option( 'terminal' );
		$this->key 				= $this->get_option( 'key' );

		$this->url				= $this->get_option( 'url' );
		
		$this->signature 		= $this->get_option( 'signature' );
		$this->test 			= $this->get_option( 'test' );

		$this->merchantName 	= $this->get_option( 'merchantName' );
		$this->titular 			= $this->get_option( 'titular' );

		// Actions
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		
		add_action( 'woocommerce_receipt_servired_light', array( $this, 'receipt_page' ) );

	}


	
	
	/**
	 * Initialise Gateway Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields() {

		$this->form_fields = array(
				'enabled' => array(
						'title' => __( 'Enable/Disable', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'type' => 'checkbox',
						'label' => __( 'Enable Servired Light', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'default' => 'yes'
				),
				'title' => array(
						'title' => __( 'Title', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'type' => 'text',
						'description' => __( 'This  title is showed in checkout process.', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'default' => __( 'Servired Light', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'desc_tip'      => true,
				),
				'description' => array(
						'title' => __( 'Description', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'type' => 'textarea',
						'description' => __( 'Description of the method of payment. Use it to tell the user that it is a secure system through bank.', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'default' => __( 'Secure payment by credit card. You will be redirected to the secure website of the bank.', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN )
				),
				'titular' => array(
						'title' => __( 'Titular', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'type' => 'text',
						'default' => ''
				),
				'merchantName' => array(
						'title' => __( 'Trade name', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'type' => 'text',
						'default' => ''
				),
				'commerce' => array(
						'title' => __( 'Trade number', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'type' => 'text',
						'default' => ''
				),
				'terminal' => array(
						'title' => __( 'Terminal number', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'type' => 'text',
						'default' => '1'
				),
				'key' => array(
						'title' => __( 'Secret key', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'type' => 'text',
						'description' => __('Encryptation Secret Key.', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'default' => ''
				),
				'signature' => array(
						'title' => __( 'Sort Code', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'type'	=> 'select',
						'options' => array(
								'completa' => __( 'Complet', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
								'ampliada' => __( 'SHA1 - Complet Extended', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN )
						),
						'description' => '',
						'default' => 'ampliada'
				),
				'url' => array(
						'title' => __( 'Url', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'type'	=> 'select',
						'options' => array(
								'sermepa' => __( 'Sermepa', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
								'redsys' => __( 'RedSys', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN )
						),
						'description' => '',
						'default' => 'sermepa'
				),
				'test' => array(
						'title' => __( 'Test Mode', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'type' => 'checkbox',
						'label' => __( 'Enable Servired test mode.', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ),
						'default' => 'yes'
				),
		);

	}


	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @access public
	 * @return void
	 */
	public function admin_options() {
		?>
<h3>
	<?php _e( 'Servired Payment', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ); ?>
</h3>
<p>
	<?php _e('Allows Servired card payments.', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ); ?>
</p>
<table class="form-table">
	<?php
	// Generate the HTML For the settings form.
	$this->generate_settings_html();
	?>
</table>
<!--/.form-table-->
<?php
	// footer
	$this->printFooter();
	}

	/**
	 * Output for the order received page.
	 *
	 * @access public
	 * @return void
	 */
	function receipt_page( $order ) {

		echo '<p>'.__( 'Thank you for your order, click on the button to pay for Servired.', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ).'</p>';

		echo $this->generate_servired_light_form( $order );

	}

	/**
	 * Generate the paypal button link
	 *
	 * @access public
	 * @param mixed $order_id
	 * @return string
	 */
	function generate_servired_light_form( $order_id ) {
		global $woocommerce;

		$order = new WC_Order( $order_id );

		if ( $this->test == 'yes' ) {
			$servired_adr = 'https://sis-t.sermepa.es:25443/sis/realizarPago';
			if ( $this->url == "redsys" ) {
				$servired_adr = 'https://sis-t.redsys.es:25443/sis/realizarPago';
			}
		} else {
			$servired_adr = 'https://sis.sermepa.es/sis/realizarPago';
			if ( $this->url == "redsys" ) {
				$servired_adr = 'https://sis.redsys.es/sis/realizarPago';
			}
		}

		$servired_args = $this->get_servired_light_args( $order );

		$servired_args_array = array();

		foreach ($servired_args as $key => $value) {
			$servired_args_array[] = '<input type="hidden" name="'.esc_attr( $key ).'" value="'.esc_attr( $value ).'" />';
		}

		if ( method_exists( $woocommerce, 'add_inline_js' ) ) {
			$woocommerce->add_inline_js( '
				jQuery("body").block({
					message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/ajax-loader.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />'.__( 'Thank you for your order. We are now redirecting you to servired to make payment.', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ).'",
					overlayCSS:
					{
						background: "#fff",
						opacity: 0.6
					},
					css: {
						padding:        20,
						textAlign:      "center",
						color:          "#555",
						border:         "3px solid #aaa",
						backgroundColor:"#fff",
						cursor:         "wait",
						lineHeight:		"32px"
					}
				});
				jQuery("#submit_servired_light_payment_form").click();
			' );
		} else {
			wc_enqueue_js( '
				jQuery("body").block({
					message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/ajax-loader.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />'.__( 'Thank you for your order. We are now redirecting you to servired to make payment.', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ).'",
					overlayCSS:
					{
						background: "#fff",
						opacity: 0.6
					},
					css: {
						padding:        20,
						textAlign:      "center",
						color:          "#555",
						border:         "3px solid #aaa",
						backgroundColor:"#fff",
						cursor:         "wait",
						lineHeight:		"32px"
					}
				});
				jQuery("#submit_servired_light_payment_form").click();
			' );
	 	}

		return '<form action="'.esc_url( $servired_adr ).'" method="post" id="servired_light_payment_form" target="_top">
			' . implode( '', $servired_args_array) . '
			<input type="submit" class="button-alt" id="submit_servired_light_payment_form" value="'.__( 'Pay via Servired', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ).'" /> <a class="button cancel" href="'.esc_url( $order->get_cancel_order_url() ).'">'.__( 'Cancel order &amp; restore cart', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ).'</a>
			</form>';

	}

	/**
	 * Get Servired Args for passing to PP
	 *
	 * @access public
	 * @param mixed $order
	 * @return array
	 */
	function get_servired_light_args( $order ) {
		global $woocommerce;

		$order_id = $order->id;
		$ds_order = str_pad($order->id, 8, "0", STR_PAD_LEFT) . date('is');
		
		if ($this->signature == "completa") {
			//$message = $importe.$order.$code.$currency.$clave;
			$message =  $order->get_total()*100 .
			$ds_order .
			$this->commerce .
			"978" .
			$this->key;
				
			$signature = strtoupper(sha1($message));
		} else {
			// Ampliado
			//$amount.$order.$code.$currency.$transactionType.$urlMerchant.$clave;
		
			$message =  $order->get_total()*100 .
			$ds_order .
			$this->commerce .
			"978" .
			"0" .
			add_query_arg( 'wc-api', 'WC_Gateway_Servired_Light', home_url( '/' ) ) .
			$this->key;
				
			$signature = strtoupper(sha1($message));
		}
		
		$args = array (
				'Ds_Merchant_MerchantCode'			=> $this->commerce,
				'Ds_Merchant_Terminal'				=> $this->terminal,
				'Ds_Merchant_Currency'				=> 978,
				'Ds_Merchant_MerchantURL'			=> add_query_arg( 'wc-api', 'WC_Gateway_Servired_Light', home_url( '/' ) ),
				'Ds_Merchant_TransactionType'		=> 0,
				'Ds_Merchant_MerchantSignature'		=> $signature,
				
				'Ds_Merchant_UrlKO'					=> apply_filters( 'wooservired_light_param_urlKO', get_permalink( woocommerce_get_page_id( 'checkout' ) ) ),
				'Ds_Merchant_UrlOK'					=> apply_filters( 'wooservired_light_param_urlOK', $this->get_return_url( $order ) ),

				'Ds_Merchant_Titular'				=> $this->titular,
				'Ds_Merchant_MerchantName'			=> $this->merchantName,
				
				'Ds_Merchant_Amount'				=> round($order->get_total()*100),
				'Ds_Merchant_ProductDescription'	=> sprintf( __( 'Order %s' , WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ), $order->get_order_number() ),
				
				'Ds_Merchant_Order'					=> $ds_order,
			
		);
	
			
		return $args;
		
	}

	
    
    /**
     * Process the payment and return the result
     *
     * @access public
     * @param int $order_id
     * @return array
     */
    function process_payment( $order_id ) {

    	$order = new WC_Order( $order_id );

    	return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay' ))))
		);
    }
    
    function printFooter() {
		$output = '<hr>';
		$output .= '<div style="background-color:#ccc; padding: 20px 10px;">';
		$output .= '<p>Actualice los estados de los pedidos automáticamente y consiga soporte premium, usando <a href="http://plugintpv.com/plugins/servired-integracion-woocommerce/" target="_blank">Woocommerce Servired PRO</a>';
		$output .= '</div>';
		
		echo $output;
	}

}
?>