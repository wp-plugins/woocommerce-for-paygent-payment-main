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
	exit;
}

class WC_Admin_Screen_Paygent {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wc_admin_paygent_menu' ) );
		add_action( 'admin_notices', array( $this, 'paygent_file_check' ) );
		add_action( 'admin_notices', array( $this, 'paygent_ssl_check' ) );
		add_action( 'admin_init', array( $this, 'wc_setting_paygent_init') );
	}
	/**
	 * Admin Menu
	 */
	public function wc_admin_paygent_menu() {
		$page = add_submenu_page( 'woocommerce', __( 'Paygent Setting', 'woocommerce-paygent-main2' ), __( 'Paygent Setting', 'woocommerce-paygent-main2' ), 'manage_woocommerce', 'wc4jp-paygent-output', array( $this, 'wc_paygent_output' ) );
	}

	/**
	 * Admin Screen output
	 */
	public function wc_paygent_output() {
		$tab = ! empty( $_GET['tab'] ) && $_GET['tab'] == 'info' ? 'info' : 'setting';
		include( 'views/html-admin-screen.php' );
	}

	/**
	 * Admin page for Setting
	 */
	public function admin_paygent_setting_page() {
		include( 'views/html-admin-setting-screen.php' );
	}

	/**
	 * Admin page for infomation
	 */
	public function admin_paygent_info_page() {
		include( 'views/html-admin-info-screen.php' );
	}
	
      /**
       * Check require files set in this site and notify the user.
       */
	public function paygent_file_check(){
       // * Check if Client Cert file and CA Cert file and notify the user.
		if (!file_exists(CLIENT_FILE_PATH) or !file_exists(CA_FILE_PATH)){
			if(!file_exists(CLIENT_FILE_PATH)) $cilent_msg = __('Client Cert File do not exist. ', 'woocommerce-paygent-main2' );
			if(!file_exists(CA_FILE_PATH)) $ca_msg = __('CA Cert File do not exist. ', 'woocommerce-paygent-main2' );
			echo '<div class="error"><ul><li>' . __('Paygent Cert File do not exist. Please put Cert files.', 'woocommerce-paygent-main2' ) .$cilent_msg.$ca_msg. '</li></ul></div>';
		}
       // * Check if Client Cert file and CA Cert file uploaded files is fault.
		if ($this->pem_error_message or $this->crt_error_message){
			if($this->pem_error_message) $cilent_msg = $this->pem_error_message;
			if($this->crt_error_message) $ca_msg = $this->crt_error_message;
			echo '<div class="error"><ul><li>' . __('Mistake your uploaded file.', 'woocommerce-paygent-main2' ) .$cilent_msg.$ca_msg. '</li></ul></div>';
		}
	}

      /**
       * Check if SSL is enabled and notify the user.
       */
      function paygent_ssl_check() {
        if ( get_option( 'woocommerce_force_ssl_checkout' ) == 'no' && $this->enabled == 'yes' ) {
            echo '<div class="error"><p>' . sprintf( __('Paygent Commerce is enabled and the <a href="%s">force SSL option</a> is disabled; your checkout is not secure! Please enable SSL and ensure your server has a valid SSL certificate.', 'woocommerce-paygent-main2' ), admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) . '</p></div>';
            }
	  }

	function wc_setting_paygent_init(){
		if( isset( $_POST['wc-paygent-setting'] ) && $_POST['wc-paygent-setting'] ){
			if( check_admin_referer( 'my-nonce-key', 'wc-paygent-setting')){
				//Merchant ID Setting
				if(isset($_POST['paygent_mid']) && $_POST['paygent_mid']){
					update_option( 'wc-paygent-mid', $_POST['paygent_mid']);
				}
				//Connect ID Setting
				if(isset($_POST['paygent_cid']) && $_POST['paygent_cid']){
					update_option( 'wc-paygent-cid', $_POST['paygent_cid']);
				}
				//Connect Password Setting
				if(isset($_POST['paygent_cpass']) && $_POST['paygent_cpass']){
					update_option( 'wc-paygent-cpass', $_POST['paygent_cpass']);
				}
				//Site ID Setting
				if(isset($_POST['paygent_sid']) && $_POST['paygent_sid']){
					update_option( 'wc-paygent-sid', $_POST['paygent_sid']);
				}
				//Client Cert File upload
				if(substr($_FILES["clientc_file"]["name"], strrpos($_FILES["clientc_file"]["name"], '.') + 1)=='pem'){
					if (move_uploaded_file($_FILES["clientc_file"]["tmp_name"], WP_CONTENT_DIR.'/uploads/wc-paygent/client_cert.pem')) {
					    chmod(WP_CONTENT_DIR.'/uploads/wc-paygent/client_cert.pem' , 0644);
					} else {
						//error_log
					}
				}else{
					if($_FILES["clientc_file"]["name"]){
					//error_message
						$this->pem_error_message = __('Uploaded flie is not Client Cert File. Please check .pem file.', 'woocommerce-paygent-main2' );
					}
				}
				//CA Cert File upload
				if(substr($_FILES["cac_file"]["name"], strrpos($_FILES["cac_file"]["name"], '.') + 1)=='crt'){
					if (move_uploaded_file($_FILES["cac_file"]["tmp_name"], WP_CONTENT_DIR.'/uploads/wc-paygent/curl-ca-bundle.crt')) {
					    chmod(WP_CONTENT_DIR.'/uploads/wc-paygent/curl-ca-bundle.crt', 0644);
					} else {
						//error_log
					}
				}else{
					if($_FILES["cac_file"]["name"]){
					//error_message
						$this->crt_error_message = __('Uploaded flie is not CA Cert File. Please check .crt file.', 'woocommerce-paygent-main2' );
					}
				}
				//Credit Card payment method
				$woocommerce_paygent_cc = get_option('woocommerce_paygent_cc_settings');
				if(isset($_POST['paygent_cc']) && $_POST['paygent_cc']){
					update_option( 'wc-paygent-cc', $_POST['paygent_cc']);
					if(isset($woocommerce_paygent_cc)){
						$woocommerce_paygent_cc['enabled'] = 'yes';
						update_option( 'woocommerce_paygent_cc_settings', $woocommerce_paygent_cc);
					}
				}else{
					update_option( 'wc-paygent-cc', '');
					if(isset($woocommerce_paygent_cc)){
						$woocommerce_paygent_cc['enabled'] = 'no';
						update_option( 'woocommerce_paygent_cc_settings', $woocommerce_paygent_cc);
					}
				}
				//Convenience store payment method
					$woocommerce_paygent_cs = get_option('woocommerce_paygent_cs_settings');
				if(isset($_POST['paygent_cs']) && $_POST['paygent_cs']){
					update_option( 'wc-paygent-cs', $_POST['paygent_cs']);
					if(isset($woocommerce_paygent_cs)){
						$woocommerce_paygent_cs['enabled'] = 'yes';
						update_option( 'woocommerce_paygent_cs_settings', $woocommerce_paygent_cs);
					}
				}else{
					update_option( 'wc-paygent-cs', '');
					if(isset($woocommerce_paygent_cs)){
						$woocommerce_paygent_cs['enabled'] = 'no';
						update_option( 'woocommerce_paygent_cs_settings', $woocommerce_paygent_cs);
					}
				}
				//Multi-currency Credit Card payment method
					$woocommerce_paygent_mccc = get_option('woocommerce_paygent_mccc_settings');
				if(isset($_POST['paygent_mccc']) && $_POST['paygent_mccc']){
					update_option( 'wc-paygent-mccc', $_POST['paygent_mccc']);
					if(isset($woocommerce_paygent_mccc)){
						$woocommerce_paygent_mccc['enabled'] = 'yes';
						update_option( 'woocommerce_paygent_mccc_settings', $woocommerce_paygent_mccc);
					}
				}else{
					update_option( 'wc-paygent-mccc', '');
					if(isset($woocommerce_paygent_mccc)){
						$woocommerce_paygent_mccc['enabled'] = 'no';
						update_option( 'woocommerce_paygent_mccc_settings', $woocommerce_paygent_mccc);
					}
				}
				//Test Mode Setting
				if(isset($_POST['paygent_testmode']) && $_POST['paygent_testmode']){
					update_option( 'wc-paygent-testmode', $_POST['paygent_testmode']);
				}else{
					update_option( 'wc-paygent-testmode', '');
				}
			}
		}
	}
}

new WC_Admin_Screen_Paygent();