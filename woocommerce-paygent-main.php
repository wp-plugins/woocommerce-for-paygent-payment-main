<?php
/**
 * Plugin Name: WooCommerce For Paygent Main 2 method
 * Plugin URI: http://wordpress.org/plugins/woocommerce-paygent-main2/
 * Description: Woocommerce Main 2 gateway payment 
 * Version: 1.0.0
 * Author: Artisan Workshop
 * Author URI: http://wc.artws.info/
 * Requires at least: 3.8
 * Tested up to: 4.0
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
register_activation_hook( __FILE__, array( 'WooCommercePaygentMain2', 'plugin_activation' ) );

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
		define('PAYGENT_DEBUG_FLG', 0);//Debug Option
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
		if(get_option('wc-paygent-cc')) include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/paygent/class-wc-gateway-paygent-cc.php' );	// Credit Card
		if(get_option('wc-paygent-cs')) include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/paygent/class-wc-gateway-paygent-cs.php' );	// Convenience store
		if(get_option('wc-paygent-mccc')) include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/paygent/class-wc-gateway-paygent-mccc.php' );	// Multi-currency Credit Card

		// Admin Setting Screen 
		include_once( plugin_dir_path( __FILE__ ).'/includes/class-wc-admin-screen-paygent.php' );
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
	public function plugin_activation(){
		$wc_paybent_dir = WP_CONTENT_DIR.'/uploads/wc-paygent';
		if( !is_dir( $wc_paybent_dir ) ){
		mkdir($wc_paybent_dir, 0755);
		}
	}
}

endif;
//If WooCommerce Plugins is not activate notice
function wcPaygentMain2_fallback_notice(){
	?>
    <div class="error">
        <ul>
            <li><?php echo __( 'WooCommerce for Paygent Main 2 method is enabled but not effective. It requires WooCommerce in order to work.', 'woocommerce-paygent-main2' );?></li>
        </ul>
    </div>
    <?php
}
function WooCommercePaygentMain2_plugin() {
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $wcPaygentMain2 = new WooCommercePaygentMain2();
    } else {
        add_action( 'admin_notices', 'wcPaygentMain2_fallback_notice' );
    }
}
