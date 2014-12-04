<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Paygent Payment Gateway
 *
 * Provides a Paygent Convenience Store Payment Gateway.
 *
 * @class 			WC_Paygent
 * @extends		WC_Gateway_Paygent_CS
 * @version		1.0.5
 * @package		WooCommerce/Classes/Payment
 * @author			Artisan Workshop
 */
class WC_Gateway_Paygent_CS extends WC_Payment_Gateway {


	/**
	 * Constructor for the gateway.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->id                = 'paygent_cs';
		$this->has_fields        = false;
		$this->order_button_text = __( 'Proceed to Paygent Convenience Store', 'woocommerce-paygent-main2' );
		$this->method_title      = __( 'Paygent Convenience Store', 'woocommerce-paygent-main2' );

		//Paygent Setting IDs
		$this->merchant_id = get_option('wc-paygent-mid');
		$this->connect_id = get_option('wc-paygent-cid');
		$this->connect_password = get_option('wc-paygent-cpass');
		$this->site_id = get_option('wc-paygent-sid');

        // Create plugin fields and settings
		$this->init_form_fields();
		$this->init_settings();
		$this->method_title       = __( 'Paygent Convenience Store Payment Gateway', 'woocommerce-paygent-main2' );
		$this->method_description = __( 'Allows payments by Paygent Convenience Store in Japan.', 'woocommerce-paygent-main2' );

		// Get setting values
		foreach ( $this->settings as $key => $val ) $this->$key = $val;

		// Actions
		add_action( 'woocommerce_receipt_paygent_cv',                              array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways',              array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	    	// Customer Emails
	    	add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
	}
      /**
       * Initialize Gateway Settings Form Fields.
       */
	    function init_form_fields() {

	      $this->form_fields = array(
	      'enabled'     => array(
	        'title'       => __( 'Enable/Disable', 'woocommerce-paygent-main2' ),
	        'label'       => __( 'Enable paygent Convenience Store Payment', 'woocommerce-paygent-main2' ),
	        'type'        => 'checkbox',
	        'description' => '',
	        'default'     => 'no'
	        ),
	      'title'       => array(
	        'title'       => __( 'Title', 'woocommerce-paygent-main2' ),
	        'type'        => 'text',
	        'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-paygent-main2' ),
	        'default'     => __( 'Convenience Store (Paygent)', 'woocommerce-paygent-main2' )
	        ),
	      'description' => array(
	        'title'       => __( 'Description', 'woocommerce-paygent-main2' ),
	        'type'        => 'textarea',
	        'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-paygent-main2' ),
	        'default'     => __( 'Pay at Convenience Store via Paygent.', 'woocommerce-paygent-main2' )
	        )
		);
		}

      /**
       * UI - Payment page fields for paygent Payment.
       */
			function payment_fields() {
          		// Description of payment method from settings
          		if ( $this->description ) { ?>
            		<p><?php echo $this->description; ?></p>
      		<?php } ?>
			<fieldset  style="padding-left: 40px;">
            <p><?php _e( 'Please select Convenience Store where you want to pay', 'woocommerce-paygent-main2' );?></p>
            <select name="cvs_company_id">
            <option value="00C001">セブンイレブン</option>
            <option value="00C002">ローソン</option>
            <option value="00C005">ファミリーマート</option>
            <option value="00C016">セイコーマート</option>
            <option value="00C004">ミニストップ</option>
            <option value="00C006">サンクス</option>
            <option value="00C007">サークルK</option>
            <option value="00C014">デイリーヤマザキ</option>
            </select>
			</fieldset>
<?php
    }

		/**
		 * Process the payment and return the result.
		 */
		function process_payment( $order_id ) {

			global $woocommerce;

			$order = new WC_Order( $order_id );
			$user = new WP_User( $order->user_id );
			if($order->user_id){
				$customer_id   = $order->user_id;
			}else{
				$customer_id   = $order->id.'-user';
			}
			
		// Send request and get response from server
		$order_process = new PaygentB2BModule();
		$order_process->init();
		$order_process->reqPut('merchant_id',$this->merchant_id);
		$order_process->reqPut('connect_id',$this->connect_id);
		$order_process->reqPut('connect_password',$this->connect_password);
		$order_process->reqPut('telegram_version','1.0');
		$order_process->reqPut('telegram_kind','030');//Card Payment Auto
		$order_process->reqPut('trading_id','wc_'.$order->id);
		$order_process->reqPut('payment_id','');

		$order_process->reqPut('payment_amount',$order->order_total);
		// Customer Name
		$order_process->reqPut('customer_family_name',$order->billing_last_name);
		$order_process->reqPut('customer_name',$order->billing_first_name);
		$order_process->reqPut('customer_tel',str_replace("-","",$order->billing_phone));

		$order_process->reqPut('cvs_company_id',$this->get_post( 'cvs_company_id' ));// Convenience Store Company ID
		$order_process->reqPut('sales_type',1);// Payment before shipping

		$result = $order_process->post();
		if($order_process->hasResNext()){
			$res_array = $order_process->resNext();
		}
		$response = array(
			"result" => $order_process->getResultStatus(),
			"responseCode" =>$order_process->getResponseCode(),
			"responseDetail"=> $order_process->getResponseDetail(),
			"result_array"=> $res_array
		);
		$this->result_array = $response['result_array'];

      // Check response
      if ( $response['result'] == 0 ) {
        // Success
        $order->add_order_note( __( 'Convenience store Payment completed. Transaction ID: ' , 'woocommerce-paygent-main2' ) . 'wc_'.$order->id . __('. Receipt Number : ', 'woocommerce-paygent-main2' ) .$this->result_array['receipt_number'] );

		// Mark as on-hold (we're awaiting the payment)
		$order->update_status( 'on-hold', __( 'Awaiting Convenience store payment', 'woocommerce-4jp' ) );

		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		WC()->cart->empty_cart();

//        $order->payment_complete();

        // Return thank you redirect
        return array (
          'result'   => 'success',
          'redirect' => $this->get_return_url( $order ),
        );

      } else if ( $response['result'] == 7 ) {//3DS

      } else if ( $response['result'] == 1 ) {//System Error
        // Other transaction error
        $order->add_order_note( __( 'paygent Payment failed. Sysmte Error: ', 'woocommerce-paygent-main2' ) . $response['responseCode'] .':'. mb_convert_encoding($response['responseDetail'],"UTF-8","SJIS" ).':'.'wc_'.$order->id );
        $woocommerce->add_error( __( 'Sorry, there was an error: ', 'woocommerce-paygent-main2' ) . $response['responseCode'] );
      } else {
        // No response or unexpected response
        $order->add_order_note( __( "paygent Payment failed. Some trouble happened.", 'woocommerce-paygent-main2' ). $response['result'] .':'.$response['responseCode'] .':'. mb_convert_encoding($response['responseDetail'],"UTF-8","SJIS").':'.'wc_'.$order->id );
        $woocommerce->add_error( __( 'No response from payment gateway server. Try again later or contact the site administrator.', 'woocommerce-paygent-main2' ). $response['responseCode'] );

      }
	}

		function receipt_page( $order ) {
			echo '<p>' . __( 'Thank you for your order.', 'woocommerce-paygent-main2' ) . '</p>';
		}

    /**
     * Include jQuery and our scripts
     */
    function add_paygent_cs_scripts() {

      if ( ! $this->user_has_stored_data( wp_get_current_user()->ID ) ) return;

      wp_enqueue_script( 'jquery' );
      wp_enqueue_script( 'edit_billing_details', PLUGIN_DIR . 'js/edit_billing_details.js', array( 'jquery' ), 1.0 );

      if ( $this->security_check == 'yes' ) wp_enqueue_script( 'check_cvv', PLUGIN_DIR . 'js/check_cvv.js', array( 'jquery' ), 1.0 );

    }

		/**
		 * Get post data if set
		 */
		private function get_post( $name ) {
			if ( isset( $_POST[ $name ] ) ) {
				return $_POST[ $name ];
			}
			return null;
		}
    /**
     * Add content to the WC emails For Convenient Infomation.
     *
     * @access public
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     * @return void
     */
    public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
    	if ( ! $sent_to_admin && 'paygent_cs' === $order->payment_method && 'on-hold' === $order->status ) {
			if ( $this->instructions ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}
			$this->paygent_cs_details( $order->id );
		}
    }

    /**
     * Get bank details and place into a list format
     */
    private function paygent_cs_details( $order_id = '' ) {
		$cvs_array = array(
			'00C001' => 'セブンイレブン',
            '00C002' => 'ローソン',
             '00C005' => 'ファミリーマート',
            '00C016' => 'セイコーマート',
            '00C004' => 'ミニストップ',
            '00C006' => 'サンクス',
            '00C007' => 'サークルK',
            '00C014' => 'デイリーヤマザキ',
		);
		if(strstr($this->result_array['usable_cvs_company_id'], '-')){
			$csv_companies = explode("-", $this->result_array['usable_cvs_company_id']);
			foreach($csv_companies as $csv_company){
				$usable_cvs_company .= $cvs_array[$csv_company].' ';
			}
		}else{
			$usable_cvs_company = $cvs_array[$this->result_array['usable_cvs_company_id']];
		}
		$payment_limit_date = substr($this->result_array['payment_limit_date'], 0, 4).'/'.substr($this->result_array['payment_limit_date'], 5, 2).'/'.substr($this->result_array['payment_limit_date'], 7, 2);
    	    	echo '<h3>' . __( 'Convenience store payment details', 'woocommerce-paygent-main2' ) . '</h3>' . PHP_EOL;
		echo '<p>'. __( 'Receipt Number : ', 'woocommerce-paygent-main2' ) .$this->result_array['receipt_number'].'<br />'. __( 'URL : ', 'woocommerce-paygent-main2' ).$this->result_array['receipt_print_url'].'<br />'. __( 'Convenience store : ', 'woocommerce-paygent-main2' ).$usable_cvs_company.'<br />'. __( 'limit Date : ', 'woocommerce-paygent-main2') .$payment_limit_date.'</p>';

    }

}
	/**
	 * Add the gateway to woocommerce
	 */
	function add_wc_paygent_cs_gateway( $methods ) {
		$methods[] = 'WC_Gateway_Paygent_CS';
		return $methods;
	}

	add_filter( 'woocommerce_payment_gateways', 'add_wc_paygent_cs_gateway' );

	/**
	 * Edit the available gateway to woocommerce
	 */
	function edit_available_gateways_cs( $methods ) {
		if ( ! $currency ) {
			$currency = get_woocommerce_currency();
		}
		if($currency !='JPY'){
		unset($methods['paygent_cs']);
		}
		return $methods;
	}

	add_filter( 'woocommerce_available_payment_gateways', 'edit_available_gateways_cs' );
