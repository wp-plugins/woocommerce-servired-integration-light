<?php
/**
 * woocommerce-servired-light.php
 *
 * Copyright (c) 2013 Antonio Blanco www.plugintpv.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * Parts of this code are released under the GNU General Public License.
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
 *
 * Plugin Name: Woocommerce Servired Light
 * Plugin URI: http://www.plugintpv.com
 * Description: Pago por tarjeta servired para Woocommerce. Versión Light.
 * Author: PluginTPV
 * Author URI: http://www.plugintpv.com
 * Version: 1.5
 */

if ( !defined( 'WOOCOMMERCE_SERVIRED_LIGHT_URL' ) ) {
	define( 'WOOCOMMERCE_SERVIRED_LIGHT_URL', WP_PLUGIN_URL . '/woocommerce-servired-integration-light' );
}

define( 'WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN', 'wooserviredlight' );

add_action( 'plugins_loaded', 'woocommerce_gateway_servired_light_init' );

add_action( 'init', 'miFuncion' );

function miFuncion() {
	load_plugin_textdomain( WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN, null, 'woocommerce-servired-integration-light/languages' );
}

function woocommerce_gateway_servired_light_init() {
	
	if ( !class_exists( 'WC_Payment_Gateway' ) ) return;

	
	include_once ('class-wc-gateway-servired-light.php');

	
	add_filter('woocommerce_payment_gateways', 'woocommerce_add_gateway_servired_light_gateway' );
	
	add_action( 'woocommerce_api_wc_gateway_servired_light', 'servired_light_ipn_response' );
}

function servired_light_ipn_response () {
		
	$datos = $_POST;
	
	if ($datos['Ds_Response']=='0000') {  // Operacion correcta
	
		$ds_order = ( $datos['Ds_Order'] );
		$order_id = substr($ds_order,0,8);
		
		$order = new WC_Order( $order_id );
		
		// Check order not already completed
		if ( $order->status == 'completed' ) {
			exit;
		}
		
		$order->add_order_note( sprintf( __( 'Servired order completed, code %s', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ), $datos['Ds_AuthorisationCode'] ) );
	
	} else {
		// Order failed
		$order_id = ( $datos['Ds_Order'] );
		$order = new WC_Order( $order_id );
		
		$order->add_order_note( sprintf( __( 'Servired Payment ERROR, code %s', WOOCOMMERCE_SERVIRED_LIGHT_DOMAIN ), $datos['Ds_ErrorCode'] ) );
		
	}
	
}


function woocommerce_add_gateway_servired_light_gateway($methods) {
	$methods[] = 'WC_Gateway_Servired_Light';
	return $methods;
}
