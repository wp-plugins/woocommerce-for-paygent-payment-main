<?php
if( ! defined ('WP_UNINSTALL_PLUGIN') )
exit();
function wc_paygent_delete_plugin(){
	global $wpdb;
	
	//delete option settings
	delete_option('woocommerce_paygent_cc');
	delete_option('woocommerce_paygent_cc_setting');
	delete_option('woocommerce_paygent_cc_settings');
	delete_option('woocommerce_paygent_cs');
	delete_option('woocommerce_paygent_cs_setting');
	delete_option('woocommerce_paygent_cs_settings');
	delete_option('woocommerce_paygent_mccc');
	delete_option('woocommerce_paygent_mccc_setting');
	delete_option('woocommerce_paygent_mccc_settings');
	delete_option('wc-paygent-cc');
	delete_option('wc-paygent-cid');
	delete_option('wc-paygent-cpass');
	delete_option('wc-paygent-cs');
	delete_option('wc-paygent-mccc');
	delete_option('wc-paygent-mid');
	delete_option('wc-paygent-sid');

//delete paygent files and directory
	unlink(WP_CONTENT_DIR.'/uploads/wc-paygent/connectmodule.log');
	unlink(WP_CONTENT_DIR.'/uploads/wc-paygent/client_cert.pem');
	unlink(WP_CONTENT_DIR.'/uploads/wc-paygent/curl-ca-bundle.crt');
	rmdir(WP_CONTENT_DIR.'/uploads/wc-paygent');
}

wc_paygent_delete_plugin();
?>