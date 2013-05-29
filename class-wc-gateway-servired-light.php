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
		$this->method_title     = __( 'Servired Light', 'woocommerce' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title 			= $this->get_option( 'title' );
		$this->description      = $this->get_option( 'description' );

		$this->commerce 		= $this->get_option( 'commerce' );
		$this->terminal 		= $this->get_option( 'terminal' );
		$this->key 				= $this->get_option( 'key' );

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
						'title' => __( 'Activar/Desactivar', 'woocommerce' ),
						'type' => 'checkbox',
						'label' => __( 'Activar Servired Light', 'woocommerce' ),
						'default' => 'yes'
				),
				'title' => array(
						'title' => __( 'Título', 'woocommerce' ),
						'type' => 'text',
						'description' => __( 'Título que se muestra al usuario durante el proceso de pago.', 'woocommerce' ),
						'default' => __( 'Servired Light', 'woocommerce' ),
						'desc_tip'      => true,
				),
				'description' => array(
						'title' => __( 'Descripción', 'woocommerce' ),
						'type' => 'textarea',
						'description' => __( 'Descripción del método de pago. Úselo para indicar al usuario que se trata de un sistema seguro, mediante entidad bancaria.', 'woocommerce' ),
						'default' => __( 'Pago seguro mediante tarjeta de crédito. Se le redireccionará a la web segura de la entidad bancaria.', 'woocommerce' )
				),
				'titular' => array(
						'title' => __( 'Titular', 'woocommerce' ),
						'type' => 'text',
						'default' => ''
				),
				'merchantName' => array(
						'title' => __( 'Nombre de comercio', 'woocommerce' ),
						'type' => 'text',
						'default' => ''
				),
				'commerce' => array(
						'title' => __( 'Nº comercio', 'woocommerce' ),
						'type' => 'text',
						'description' => __( 'Número de comercio.', 'woocommerce' ),
						'default' => ''
				),
				'terminal' => array(
						'title' => __( 'Nº Terminal', 'woocommerce' ),
						'type' => 'text',
						'description' => __( 'Número de Terminal.', 'woocommerce' ),
						'default' => '1'
				),
				'key' => array(
						'title' => __( 'Clave secreta', 'woocommerce' ),
						'type' => 'text',
						'description' => 'Clave secreta de encriptación.',
						'default' => ''
				),
				'signature' => array(
						'title' => __( 'Sort Code', 'woocommerce' ),
						'type'	=> 'select',
						'options' => array(
								'completa' => __( 'Completa', 'woocommerce' ),
								'ampliada' => __( 'SHA1 - Completa Extendida', 'woocommerce' )
						),
						'description' => '',
						'default' => 'ampliada'
				),
				'test' => array(
						'title' => __( 'Modo Test', 'woocommerce' ),
						'type' => 'checkbox',
						'label' => __( 'Activar Servired en modo test.', 'woocommerce' ),
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
	<?php _e( 'Pago por Servired', 'woocommerce' ); ?>
</h3>
<p>
	<?php _e('Permite el pago por tarjetas Servired.', 'woocommerce' ); ?>
</p>
<table class="form-table">
	<?php
	// Generate the HTML For the settings form.
	$this->generate_settings_html();
	?>
</table>
<!--/.form-table-->
<?php
	}

	/**
	 * Output for the order received page.
	 *
	 * @access public
	 * @return void
	 */
	function receipt_page( $order ) {

		echo '<p>'.__( 'Gracias por su pedido, haga click en el botón para pagar por Servired.', 'woocommerce' ).'</p>';

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

		if ( $this->test == 'yes' ):
		$servired_adr = 'https://sis-t.sermepa.es:25443/sis/realizarPago';
		else :
		$servired_adr = 'https://sis.sermepa.es/sis/realizarPago';
		endif;

		$servired_args = $this->get_servired_light_args( $order );

		$servired_args_array = array();

		foreach ($servired_args as $key => $value) {
			$servired_args_array[] = '<input type="hidden" name="'.esc_attr( $key ).'" value="'.esc_attr( $value ).'" />';
		}

		$woocommerce->add_inline_js( '
				jQuery("body").block({
				message: "<img src=\"' . esc_url( apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/ajax-loader.gif' ) ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />'.__( 'Thank you for your order. We are now redirecting you to servired to make payment.', 'woocommerce' ).'",
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

		return '<form action="'.esc_url( $servired_adr ).'" method="post" id="servired_light_payment_form" target="_top">
								' . implode( '', $servired_args_array) . '
								<input type="submit" class="button-alt" id="submit_servired_light_payment_form" value="'.__( 'Pay via Servired', 'woocommerce' ).'" /> <a class="button cancel" href="'.esc_url( $order->get_cancel_order_url() ).'">'.__( 'Cancel order &amp; restore cart', 'woocommerce' ).'</a>
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

		/*
		-Ds_Merchant_MerchantCode: 999008881
		-Ds_Merchant_Terminal : 9
		-Ds_Merchant_ProductDescription: Alfombrilla para raton
		-Ds_Merchant_Order: 070803113316
		-Ds_Merchant_Titular: Sermepa
		-Ds_Merchant_Currency: 978
		-Ds_Merchant_MerchantURL: https://sis-t.sermepa.es:25443/sis/pruebaCom.jsp
		-Ds_Merchant_MerchantName: Comercio Pruebas
		-Ds_Merchant_MerchantSiganture:	ca2bd747d365b4f0a87c670b270cc390b79670ce
		-Ds_Merchant_Amount: 825
		-Ds_Merchant_TransactionType: 0
		*/

		$message =  $order->get_total()*100 .
					str_pad($order->id, 12, "0", STR_PAD_LEFT) .
					$this->commerce . 
					"978" . 
					$this->key;
		
		
		//$amount.$order.$code.$currency.$transactionType.$urlMerchant.$clave;
		//$message = $importe.$order.$code.$currency.$clave;
		$signature = sha1($message);
		
		$args = array (
				'Ds_Merchant_MerchantCode'			=> "" . $this->commerce,
				'Ds_Merchant_Terminal'				=> $this->terminal,
				'Ds_Merchant_Currency'				=> 978,
				'Ds_Merchant_MerchantURL'			=> str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_Servired_Light', home_url( '/' ) ) ),
				'Ds_Merchant_TransactionType'		=> 0,
				'Ds_Merchant_MerchantSignature'		=> $signature,
				
				'Ds_Merchant_UrlOK'					=> get_permalink(woocommerce_get_page_id('thanks')),
				'Ds_Merchant_UrlKO'					=> get_permalink(woocommerce_get_page_id('checkout')),
				
				'Ds_Merchant_Titular'				=> $this->titular,
				'Ds_Merchant_MerchantName'			=> $this->merchantName,
				
				'Ds_Merchant_Amount'				=> $order->get_total()*100,
				'Ds_Merchant_ProductDescription'	=> sprintf( __( 'Pedido %s' , 'woocommerce'), $order->get_order_number() ),
				
				'Ds_Merchant_Order'					=> str_pad($order->id, 12, "0", STR_PAD_LEFT),
			
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

}
?>