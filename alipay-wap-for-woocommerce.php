<?php
/*
 * Plugin Name: Alipay Wap For WooCommerce
 * Plugin URI: http://www.chenwg.com
 * Description: WooCommerce支持支付宝付款，支付宝是中国应用最广泛的支付方式之一。
 * Version: 1.3.4
 * Author: 陈文光
 * Author URI: http://www.chenwg.com
 * Requires at least: 3.9
 * Tested up to: 4.0
 *
 * Text Domain: alipay
 * Domain Path: /lang/
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wc_alipay_wap_gateway_init() {

    if( !class_exists('WC_Payment_Gateway') )  return;

    load_plugin_textdomain( 'alipaywap', false, dirname( plugin_basename( __FILE__ ) ) . '/language/'  );

    require_once( plugin_basename( 'class-wc-alipay-wap.php' ) );

    add_filter('woocommerce_payment_gateways', 'woocommerce_alipay_wap_add_gateway' );

    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_alipay_wap_plugin_edit_link' );

}
add_action( 'plugins_loaded', 'wc_alipay_wap_gateway_init' );

/**
 * Add the gateway to WooCommerce
 *
 * @access  public
 * @param   array $methods
 * @package WooCommerce/Classes/Payment
 * @return  array
 */
function woocommerce_alipay_wap_add_gateway( $methods ) {

    $methods[] = 'WC_Alipay_Wap';
    return $methods;
}

/**
 * Display Alipay Trade No. for customer
 * 
 *
 * The function is put here because the alipay class 
 * is not called on order-received page
 *
 * @param array $total_rows
 * @param mixed $order
 * @return array
 */
function wc_alipay_wap_display_order_meta_for_customer( $total_rows, $order ){
    $trade_no = get_post_meta( $order->id, 'Alipay Trade No.', true );
    
    if( !empty( $trade_no ) ){
        $new_row['alipay_trade_no'] = array(
            'label' => __( 'Alipay Trade No.:', 'alipay' ),
            'value' => $trade_no
        );
        // Insert $new_row after shipping field
        $total_rows = array_merge( array_splice( $total_rows,0,2), $new_row, $total_rows );
    }
    return $total_rows;
}
add_filter( 'woocommerce_get_order_item_totals', 'wc_alipay_wap_display_order_meta_for_customer', 10, 2 );

function wc_alipay_wap_plugin_edit_link( $links ){
    return array_merge(
        array(
            'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_alipay_wap') . '">'.__( 'Settings', 'alipay' ).'</a>'
        ),
        $links
    );
}
?>