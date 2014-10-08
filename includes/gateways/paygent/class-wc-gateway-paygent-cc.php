<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Paygent Payment Gateway
 *
 * Provides a Paygent Credit Card Payment Gateway.
 *
 * @class 			WC_Paygent
 * @extends		WC_Gateway_Paygent_CC
 * @version		1.0.0
 * @package		WooCommerce/Classes/Payment
 * @author			Artisan Workshop
 */
class WC_Gateway_Paygent_CC extends WC_Payment_Gateway {


	/**
	 * Constructor for the gateway.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->id                = 'paygent_cc';
		$this->has_fields        = false;
		$this->order_button_text = __( 'Proceed to Paygent Credit Card', 'woocommerce-paygent-main2' );
		$this->method_title      = __( 'Paygent Credit Card', 'woocommerce-paygent-main2' );
		
		//Paygent Setting IDs
		$this->merchant_id = get_option('wc-paygent-mid');
		$this->connect_id = get_option('wc-paygent-cid');
		$this->connect_password = get_option('wc-paygent-cpass');
		$this->site_id = get_option('wc-paygent-sid');

        // Create plugin fields and settings
		$this->init_form_fields();
		$this->init_settings();
		$this->method_title       = __( 'Paygent Credit Card Payment Gateway', 'woocommerce-paygent-main2' );
		$this->method_description = __( 'Allows payments by Paygent Credit Card in Japan.', 'woocommerce-paygent-main2' );

		// Get setting values
		foreach ( $this->settings as $key => $val ) $this->$key = $val;

		// Load plugin checkout icon
//		$this->icon = WP_CONTENT_DIR . '/plugins/woocommerce-paygent-main/images/paygent-cards.png';
		$this->icon = plugins_url( 'images/paygent-cards.png' , __FILE__ );
		// Logs
		if ( 'yes' == $this->debug ) {
			$this->log = new WC_Logger();
		}

		// Actions
		add_action( 'woocommerce_receipt_paygent_cc',                              array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways',              array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'wp_enqueue_scripts',                                       array( $this, 'add_paygent_cc_scripts' ) );
	}

      /**
       * Initialize Gateway Settings Form Fields.
       */
	    function init_form_fields() {

	      $this->form_fields = array(
	      'enabled'     => array(
	        'title'       => __( 'Enable/Disable', 'woocommerce-paygent-main2' ),
	        'label'       => __( 'Enable paygent Credit Card Payment', 'woocommerce-paygent-main2' ),
	        'type'        => 'checkbox',
	        'description' => '',
	        'default'     => 'no'
	        ),
	      'title'       => array(
	        'title'       => __( 'Title', 'woocommerce-paygent-main2' ),
	        'type'        => 'text',
	        'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-paygent-main2' ),
	        'default'     => __( 'Credit Card (Paygent)', 'woocommerce-paygent-main2' )
	        ),
	      'description' => array(
	        'title'       => __( 'Description', 'woocommerce-paygent-main2' ),
	        'type'        => 'textarea',
	        'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-paygent-main2' ),
	        'default'     => __( 'Pay with your credit card via Paygent.', 'woocommerce-paygent-main2' )
	        ),
			'security_check' => array(
				'title'       => __( 'Security Check Code', 'woocommerce-paygent-main2' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable Security Check Code', 'woocommerce-paygent-main2' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Require customer to enter credit card CVV code (Security Check Code).', 'woocommerce-paygent-main2' )),
			),
			'store_card_info' => array(
				'title'       => __( 'Store Card Infomation', 'woocommerce-paygent-main2' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable Store Card Infomation', 'woocommerce-paygent-main2' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Store user Credit Card information in Paygent Server.(Option)', 'woocommerce-paygent-main2' )),
			),
			'testing' => array(
				'title'       => __( 'Gateway Testing', 'woocommerce-paygent-main2' ),
				'type'        => 'title',
				'description' => '',
			),
			'testmode' => array(
				'title'       => __( 'paygent Test mode', 'woocommerce-paygent-main2' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable paygent Test mode', 'woocommerce-paygent-main2' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Please check you want to use paygent Test mode.', 'woocommerce-paygent-main2' )),
			),
			'debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-paygent-main2' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-paygent-main2' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Log Paygent events, such as IPN requests, inside <code>woocommerce/logs/paygent-%s.txt</code>', 'woocommerce-paygent-main2' ), sanitize_file_name( wp_hash( 'paygent' ) ) ),
			)
		);
		}


      /**
       * UI - Admin Panel Options
       */
			function admin_options() { ?>
				<h3><?php _e( 'Paygent Credit Card','woocommerce-paygent-main2' ); ?></h3>
			    <p><?php _e( 'The Paygent Credit Card Payment Gateway is simple and powerful.  The plugin works by adding credit card fields on the checkout page, and then sending the details to paygent Payment for verification.  <a href="http://www.wp-pay.com/payment-agency/paygent/">Click here to read Paygent order</a>.', 'woocommerce-paygent-main2' ); ?></p>
			    <table class="form-table">
					<?php $this->generate_settings_html(); ?>
				</table>
			<?php }
      /**
       * UI - Payment page fields for paygent Payment.
       */
			function payment_fields() {
          		// Description of payment method from settings
          		if ( $this->description ) { ?>
            		<p><?php echo $this->description; ?></p>
      		<?php } ?>
			<fieldset  style="padding-left: 40px;">
		        <?php
		          $user = wp_get_current_user();
				  $customer_check = $this->user_has_stored_data( $user->ID );
		          if ( $customer_check['result']==1 and $customer_check['responseCode']!="P026") { ?>
						<fieldset>
							<input type="radio" name="paygent-use-stored-payment-info" id="paygent-use-stored-payment-info-yes" value="yes" checked="checked" onclick="document.getElementById('paygent-new-info').style.display='none'; document.getElementById('paygent-stored-info').style.display='block'"; /><label for="paygent-use-stored-payment-info-yes" style="display: inline;"><?php _e( 'Use a stored credit card from Paygent', 'woocommerce-paygent-main2' ) ?></label>
								<div id="paygent-stored-info" style="padding: 10px 0 0 40px; clear: both;">
				                    <p><?php if($customer_check['result']==1):?>
                                    <?php $card_qty = count($customer_check['result_array'])-1; for($i=0; $i <= $card_qty; $i++) { ?>
                                    		<input type="radio" name="stored-info-<?php echo $i;?>" value="<?php echo $i;?>" id="stored-info">
										<?php _e( 'credit card last 4 numbers: ', 'woocommerce-paygent-main2' ) ?><?php echo substr($customer_check[$i]['card_number'],-4); ?> (<?php echo $customer_check[$i]['card_brand']; ?>)
                                    <?php }?><?php endif;?>
				                    <?php print_r($customer_check);?></p>
						</fieldset>
						<fieldset>
							<p>
								<input type="radio" name="paygent-use-stored-payment-info" id="paygent-use-stored-payment-info-no" value="no" onclick="document.getElementById('paygent-stored-info').style.display='none'; document.getElementById('paygent-new-info').style.display='block'"; />
		                  		<label for="paygent-use-stored-payment-info-no"  style="display: inline;"><?php _e( 'Use a new payment method', 'woocommerce-paygent-main2' ) ?></label>
		                	</p>
		                	<div id="paygent-new-info" style="display:none">
						</fieldset>
				<?php } elseif($customer_check['responseCode'] and $customer_check['responseCode']!="P026") { ?>
              			<fieldset>
                        <div id="error"><?php echo __( 'Error! ', 'woocommerce-paygent-main2' ).$customer_check['responseCode'].":".mb_convert_encoding($customer_check['responseDetail'],"UTF-8","SJIS");?></div>
              				<!-- Show input boxes for new data -->
              				<div id="paygent-new-info">
				<?php } else { ?>
              			<fieldset>
              				<!-- Show input boxes for new data -->
              				<div id="paygent-new-info">
              	<?php } ?>
								<!-- Credit card number -->
                    			<p class="form-row form-row-first">
									<label for="ccnum"><?php echo __( 'Credit Card number', 'woocommerce-paygent-main2' ) ?> <span class="required">*</span></label>
									<input type="text" class="input-text" id="card_number" name="card_number" maxlength="16" />
                    			</p>
								<!-- Credit card type -->
								<div class="clear"></div>
								<!-- Credit card expiration -->
                    			<p class="form-row form-row-first">
                      				<label for="cc-expire-month"><?php echo __( 'Expiration date', 'woocommerce-paygent-main2') ?> <span class="required">*</span></label>
                      				<select name="expire_m" id="expire_m" class="woocommerce-select woocommerce-cc-month">
                        				<option value=""><?php _e( 'Month', 'woocommerce-paygent-main2' ) ?></option><?php
				                        $months = array();
				                        for ( $i = 1; $i <= 12; $i ++ ) {
				                          $timestamp = mktime( 0, 0, 0, $i, 1 );
				                          $months[ date( 'n', $timestamp ) ] = date( 'n', $timestamp );
				                        }
				                        foreach ( $months as $num => $name ) {
				                          printf( '<option value="%u">%s</option>', $num, $name );
				                        } ?>
                      				</select>
                      				<select name="expire_y" id="expire_y" class="woocommerce-select woocommerce-cc-year">
                        				<option value=""><?php _e( 'Year', 'woocommerce-paygent-main2' ) ?></option><?php
				                        $years = array();
				                        for ( $i = date( 'y' ); $i <= date( 'y' ) + 15; $i ++ ) {
				                          printf( '<option value="20%u">20%u</option>', $i, $i );
				                        } ?>
                      				</select>
                    			</p>
								<?php

				                    // Credit card security code
				                    if ( $this->security_check == 'yes' ) { ?>
				                      <p class="form-row form-row-last">
				                        <label for="cvv"><?php _e( 'Card security code', 'woocommerce-paygent-main2' ) ?> <span class="required">*</span></label>
				                        <input oninput="validate_cvv(this.value)" type="text" class="input-text" id="cvv" name="security_code" maxlength="4" style="width:45px" />
				                        <span class="help"><?php _e( '3 or 4 digits usually found on the signature strip.', 'woocommerce-paygent-main2' ) ?></span>
				                      </p><?php
				                    }

			                    // Option to store credit card data
			                    if ( $this->saveinfo == 'yes' && ! ( class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() ) ) { ?>
			                      	<div style="clear: both;"></div>
										<p>
			                        		<label for="saveinfo"><?php _e( 'Save this billing method?', 'woocommerce-paygent-main2' ) ?></label>
			                        		<input type="checkbox" class="input-checkbox" id="saveinfo" name="saveinfo" />
			                        		<span class="help"><?php _e( 'Select to store your billing information for future use.', 'woocommerce-paygent-main2' ) ?></span>
			                      		</p>
									<?php  } ?>
            			</fieldset>
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
		$order_process->reqPut('telegram_kind','020');//Card Payment Auto
		$order_process->reqPut('trading_id','wc_'.$order->id);
		$order_process->reqPut('payment_id','');
		
		$order_process->reqPut('payment_amount',$order->order_total);
		
      // Create server request using stored or new payment details
		if ( $this->get_post( 'paygent-use-stored-payment-info' ) == 'yes' ) {
			$order_process->reqPut('stock_card_mode',1);
		} else {
		//Credit Card Infomation
			$order_process->reqPut('card_number',$this->get_post( 'card_number' ));
			if($this->get_post( 'expire_m' ) < 10){
				$expire_m = '0'.$this->get_post( 'expire_m' );
			}else{
				$expire_m = $this->get_post( 'expire_m' );
			}
			$card_valid_term = $expire_m.substr($this->get_post( 'expire_y' ),0,-2);
			$order_process->reqPut('card_valid_term',$card_valid_term);
        // Using Security Check
        if ( $this->security_check == 'yes' ) {
			$order_process->reqPut('card_conf_number',$this->get_post( 'security_code' ));
		}
      }
	  //Payment times
		$order_process->reqPut('payment_class',10);//One time payment
		
		//3D Secure Setting
		$order_process->reqPut('3dsecure_ryaku',1);
		$order_process->reqPut('http_access',$_SERVER['HTTP_ACCESS']);
		$order_process->reqPut('http_user_agent',$_SERVER['HTTP_USER_AGENT']);

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

      // Check response
      if ( $response['result'] == 0 ) {
        // Success
        $order->add_order_note( __( 'paygent Payment completed. Transaction ID: ' , 'woocommerce-paygent-main2' ) . 'wc_'.$order->id );
        $order->payment_complete();

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

		/**
		 * Process a payment for an ongoing subscription.
		 */
    function process_scheduled_subscription_payment( $amount_to_charge, $order, $product_id ) {

      $user = new WP_User( $order->user_id );
      $customer_vault_ids = get_user_meta( $user->ID, 'customer_vault_ids', true );
      $payment_method_number = get_post_meta( $order->id, 'payment_method_number', true );

      $inspire_request = array (
				'username' 		      => $this->username,
				'password' 	      	=> $this->password,
				'amount' 		      	=> $amount_to_charge,
				'type' 			        => $this->salemethod,
				'billing_method'    => 'recurring',
        );

      $id = $customer_vault_ids[ $payment_method_number ];
      if( substr( $id, 0, 1 ) !== '_' ) $inspire_request['customer_vault_id'] = $id;
      else {
        $inspire_request['customer_vault_id'] = $user->user_login;
        $inspire_request['billing_id']        = substr( $id , 1 );
        $inspire_request['ver']               = 2;
      }

      $response = $this->post_and_get_response( $inspire_request );

      if ( $response['response'] == 1 ) {
        // Success
        $order->add_order_note( __( 'paygent Payment scheduled subscription payment completed. Transaction ID: ' , 'woocommerce-paygent-main2' ) . $response['transactionid'] );
        WC_Subscriptions_Manager::process_subscription_payments_on_order( $order );

			} else if ( $response['response'] == 2 ) {
        // Decline
        $order->add_order_note( __( 'paygent Payment scheduled subscription payment failed. Payment declined.', 'woocommerce-paygent-main2') );
        WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order );

      } else if ( $response['response'] == 3 ) {
        // Other transaction error
        $order->add_order_note( __( 'paygent Payment scheduled subscription payment failed. Error: ', 'woocommerce-paygent-main2') . $response['responsetext'] );
        WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order );

      } else {
        // No response or unexpected response
        $order->add_order_note( __('paygent Payment scheduled subscription payment failed. Couldn\'t connect to gateway server.', 'woocommerce-paygent-main2') );

      }
    }

    /**
     * Check if the user has any billing records in the Customer Vault
     */
    function user_has_stored_data( $user_id ) {
		$user_check = new PaygentB2BModule();
		$user_check->init();
		$user_check->reqPut('merchant_id',$this->merchant_id);
		$user_check->reqPut('connect_id',$this->connect_id);
		$user_check->reqPut('connect_password',$this->connect_password);
		$user_check->reqPut('telegram_version','1.0');
		$user_check->reqPut('telegram_kind','027');//Check Store User infomation
		$user_check->reqPut('trading_id','');
		$user_check->reqPut('customer_id',$user_id);
		$user_check->reqPut('customer_card_id','');
		if($this->site_id)$user_check->reqPut('site_id',$site_id);

		$result = $user_check->post();

		while($user_check->hasResNext()){
			$res_array[] = $user_check->resNext();
		}

		$result_data = array(
			"result" => $user_check->getResultStatus(),
			"responseCode" =>$user_check->getResponseCode(),
			"responseDetail"=> $user_check->getResponseDetail(),
			"result_array"=> $res_array
		);

      return $result_data;
    }

    function add_stored_user_data( $user_id, $card_number, $card_valid_term ) {
		$user_stored = new PaygentB2BModule();
		$user_stored->init();
		$user_stored->reqPut('merchant_id',$this->merchant_id);
		$user_stored->reqPut('connect_id',$this->connect_id);
		$user_stored->reqPut('connect_password',$this->connect_password);
		$user_stored->reqPut('telegram_kind','025');//Add Store User infomation
		$user_stored->reqPut('telegram_version','1.0');
		$user_stored->reqPut('trading_id','');
		$user_stored->reqPut('custmer_id',$user_id);
		$user_stored->reqPut('card_number',$card_number);
		$user_stored->reqPut('card_valid_term',$card_valid_term);

		$result = $user_stored->post();
		if($user_stored->hasResNext()){
			$res_array = $user_stored->resNext();
		}
		$data = array(
			"result"=>$result,
			"responseCode" => $user_stored->getResponseCode(),
			"responseDetail" => $user_stored->getResponseDetail(),
			"customer_card_id" => $res_array['customer_card_id']
		);
		return $data;
	}
    /**
     * Check payment details for valid format
     */
		function validate_fields() {

      if ( $this->get_post( 'paygent-use-stored-payment-info' ) == 'yes' ) return true;

			global $woocommerce;

			// Check for saving payment info without having or creating an account
			if ( $this->get_post( 'saveinfo' )  && ! is_user_logged_in() && ! $this->get_post( 'createaccount' ) ) {
        $woocommerce->add_error( __( 'Sorry, you need to create an account in order for us to save your payment information.', 'woocommerce-paygent-main2') );
        return false;
      }

			$cardNumber          = $this->get_post( 'card_number' );
			$cardCSC             = $this->get_post( 'security_code' );
			$cardExpirationMonth = $this->get_post( 'expire_m' );
			$cardExpirationYear  = $this->get_post( 'expire_y' );

			// Check card number
			if ( empty( $cardNumber ) || ! ctype_digit( $cardNumber ) ) {
				$woocommerce->add_error( __( 'Card number is invalid.', 'woocommerce-paygent-main2' ) );
				return false;
			}

			if ( $this->security_check == 'yes' ){
				// Check security code
				if ( ! ctype_digit( $cardCSC ) ) {
					$woocommerce->add_error( __( 'Card security code is invalid (only digits are allowed).', 'woocommerce-paygent-main2' ) );
					return false;
				}
				if ( ( strlen( $cardCSC ) >4 ) ) {
					$woocommerce->add_error( __( 'Card security code is invalid (wrong length).', 'woocommerce-paygent-main2' ) );
					return false;
				}
			}

			// Check expiration data
			$currentYear = date( 'Y' );

			if ( ! ctype_digit( $cardExpirationMonth ) || ! ctype_digit( $cardExpirationYear ) ||
				 $cardExpirationMonth > 12 ||
				 $cardExpirationMonth < 1 ||
				 $cardExpirationYear < $currentYear ||
				 $cardExpirationYear > $currentYear + 20
			) {
				$woocommerce->add_error( __( 'Card expiration date is invalid', 'woocommerce-paygent-main2' ) );
				return false;
			}

			// Strip spaces and dashes
			$cardNumber = str_replace( array( ' ', '-' ), '', $cardNumber );

			return true;

		}

		/**
     * Send the payment data to the gateway server and return the response.
     */
    private function post_and_get_response( $request ) {

		if($this->testmode=='no'){
		$direct_card_url = "https://secure.paygent.jp/cgi-bin/order/direct_card_payment.cgi";
		}else{
		$direct_card_url = "https://beta.paygent.jp/cgi-bin/order/direct_card_payment.cgi";
		}

		// make new cURL resource
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
		curl_setopt($ch, CURLOPT_URL, $direct_card_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
		$output = curl_exec($ch);
		curl_close($ch);

		$array = explode("\n", $output);
		foreach($array as $value){
			$title = substr($value,10,5);
			switch($title){
				case 'acsur':
				$result['acsurl'] = substr(substr($value,18),0,-4);
				break;
				case 'err_c':
				$result['err_code'] = substr(substr($value,20),0,-4);
				break;
				case 'err_d':
				$result['err_detail'] = substr(substr($value,22),0,-4);
				break;
				case 'pareq':
				$result['pareq'] = substr(substr($value,17),0,-4);
				break;
				case 'resul':
				if(substr($value,10,7)!="result>"){
					$result['result'] = substr(substr($value,18),0,-4);
				}
				break;
				case 'trans':
				$result['trans_code'] = substr(substr($value,22),0,-4);
				break;
				case 'kari_':
				$result['kari_flag'] = substr(substr($value,21),0,-4);
				break;
			}
		}

      // Return response array
      return $result;
    }


		function receipt_page( $order ) {
			echo '<p>' . __( 'Thank you for your order.', 'woocommerce-paygent-main2' ) . '</p>';
		}

    /**
     * Include jQuery and our scripts
     */
    function add_paygent_cc_scripts() {

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
     * Check whether an order is a subscription
     */
		private function is_subscription( $order ) {
      return class_exists( 'WC_Subscriptions_Order' ) && WC_Subscriptions_Order::order_contains_subscription( $order );
		}

}
	/**
	 * Add the gateway to woocommerce
	 */
	function add_wc_paygent_cc_gateway( $methods ) {
		$methods[] = 'WC_Gateway_Paygent_CC';
		return $methods;
	}

	add_filter( 'woocommerce_payment_gateways', 'add_wc_paygent_cc_gateway' );

