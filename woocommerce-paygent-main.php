<?php
/**
 * Plugin Name: WooCommerce For Paygent Main 2 method
 * Plugin URI: http://wordpress.org/plugins/woocommerce-paygent-main2/
 * Description: Woocommerce Main 2 gateway payment 
 * Version: 0.9.0
 * Author: Artisan Workshop
 * Author URI: http://profiles.wordpress.org/shoheitanaka
 * Requires at least: 3.8
 * Tested up to: 3.9
 *
 * Text Domain: woocommerce-paygent-main2
 * Domain Path: /i18n/
 *
 * @package woocommerce-paygent-main2
 * @category Core
 * @author Artisan Workshop
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WooCommercePaygentMain2' ) ) :

/**
 * Load plugin functions.
 */
add_action( 'plugins_loaded', 'WooCommercePaygentMain2_plugin', 0 );

class WooCommercePaygentMain2{

	/**
	 * WooCommerce Constructor.
	 * @access public
	 * @return WooCommerce
	 */
	public function __construct() {
		// Include required files
		$this->includes();
		$this->init();
	}
	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		// Module
		define('CLIENT_FILE_PATH', WP_CONTENT_DIR.'/uploads/wc-paygent/client_cert.pem');
		define('CA_FILE_PATH', WP_CONTENT_DIR.'/uploads/wc-paygent/curl-ca-bundle.crt');
		define('WC_PAYGENT_PLUGIN_PATH',plugin_dir_path( __FILE__ ));
		define('PAYGENT_TIMEOUT_VALUE', 35);//Time out value
		define('PAYGENT_DEBUG_FALG', 0);//Debug Option
		define('PAYGENT_SELCET_MAX_CNT', 2000);//Maximum query Count upto 2000
		define('PAYGENT_TELEGRAM_KIND_REF', '027,090');//Telegram kind reffrence
		define('URL01', 'https://mdev2.paygent.co.jp/n/atm/request');//ATM決済URL
		define('URL02', 'https://mdev2.paygent.co.jp/n/card/request');//クレジットカード決済URL1
		define('URL11', 'https://mdev2.paygent.co.jp/n/card/request');//クレジットカード決済URL2
		define('URL18', 'https://mdev2.paygent.co.jp/n/card/request');//クレジットカード決済(多通貨)URL
		define('URL19', 'https://mdev2.paygent.co.jp/n/card/request');//クレジットカード決済(端末読取)URL
		define('URL03', 'https://mdev2.paygent.co.jp/n/conveni/request');//コンビニ番号方式決済URL
		define('URL04', 'https://mdev2.paygent.co.jp/n/conveni/request_print');//コンビニ帳票方式決済URL
		define('URL05', 'https://mdev2.paygent.co.jp/n/bank/request');//銀行ネット決済URL
		define('URL06', 'https://mdev2.paygent.co.jp/n/bank/requestasp');//銀行ネット決済ASPURL
		define('URL07', 'https://mdev2.paygent.co.jp/n/virtualaccount/request');//仮想口座決済URL
		define('URL09', 'https://mdev2.paygent.co.jp/n/ref/request');//決済情報照会URL
		define('URL091', 'https://mdev2.paygent.co.jp/n/ref/paynotice');//決済情報差分照会URL
		define('URL093', 'https://mdev2.paygent.co.jp/n/ref/runnotice');//キャリア継続課金差分照会URL
		define('URL094', 'https://mdev2.paygent.co.jp/n/ref/paymentref');//決済情報照会URL
		define('URL10', 'https://mdev2.paygent.co.jp/n/c/request');//携帯キャリア決済URL
		define('URL12', 'https://mdev2.paygent.co.jp/n/c/request');//携帯キャリア決済URL（継続課金用）
		define('URL20', 'https://mdev2.paygent.co.jp/n/o/requestdata');//ファイル決済URL
		define('URL15', 'https://mdev2.paygent.co.jp/n/emoney/request');//電子マネー決済URL
		define('URL13', 'https://mdev2.paygent.co.jp/n/paypal/request');//PayPal決済URL
		include_once('jp/co/ks/merchanttool/connectmodule/entity/ResponseDataFactory.php');
		include_once('jp/co/ks/merchanttool/connectmodule/system/PaygentB2BModule.php');

		include_once('jp/co/ks/merchanttool/connectmodule/exception/PaygentB2BModuleConnectException.php');
		include_once('jp/co/ks/merchanttool/connectmodule/exception/PaygentB2BModuleException.php');

		// 2 Main Payment Gateway
		include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/paygent/class-wc-gateway-paygent-cc.php' );	// Credit Card
		include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/paygent/class-wc-gateway-paygent-cs.php' );	// Convenience store
	}
	/**
	 * Init WooCommerce when WordPress Initialises.
	 */
	public function init() {
		// Set up localisation
		$this->load_plugin_textdomain();
	}

	/*
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-paygent-main2' );
		// Global + Frontend Locale
		load_plugin_textdomain( 'woocommerce-paygent-main2', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n" );
	}

}

endif;
function WooCommercePaygentMain2_plugin() {
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $wcPaygentMain2 = new WooCommercePaygentMain2();
    } else {
        add_action( 'admin_notices', 'wcPaygentMain2_fallback_notice' );
    }
}
