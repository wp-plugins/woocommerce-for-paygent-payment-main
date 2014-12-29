<?php global $woocommerce; ?>
<form id="wc-paygent-setting-form" method="post" action=""  enctype="multipart/form-data">
<?php wp_nonce_field( 'my-nonce-key','wc-paygent-setting');?>
<h3><?php echo __( 'Paygent Initial Setting', 'woocommerce-paygent-main2' );?></h3>
<p style="border:1px solid #666; width:50%; padding:10px;"><b><?php echo __( 'IP Address : ', 'woocommerce-paygent-main2' );?></b><?php echo $_SERVER['SERVER_ADDR'];?><br />
<b><?php echo __( 'libcurl Version : ', 'woocommerce-paygent-main2' );?></b><?php $version = curl_version(); echo $version["version"];?><br />
<?php echo __( '※In the case of PHP 5.0.0 or later, you need libcurl 7.10.5 or later.', 'woocommerce-paygent-main2' );?>
</p>
<table class="form-table">
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_paygent_mid"><?php echo __( 'Merchant ID', 'woocommerce-paygent-main2' );?></label>
    </th>
    <td class="forminp"><input type="text" name="paygent_mid" value="<?php echo get_option('wc-paygent-mid');?>" >
    <p class="description"><?php echo __( 'Please input Merchant ID from Paygent documents', 'woocommerce-paygent-main2' );?></p></td>
</tr>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_company"><?php echo __( 'Connect ID', 'woocommerce-paygent-main2' );?></label>
    </th>
    <td class="forminp"><input type="text" name="paygent_cid" value="<?php echo get_option('wc-paygent-cid');?>" >
    <p class="description"><?php echo __( 'Please input Connect ID from Paygent documents', 'woocommerce-paygent-main2' );?></p></td>
</tr>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_company"><?php echo __( 'Connect Password', 'woocommerce-paygent-main2' );?></label>
    </th>
    <td class="forminp"><input type="text" name="paygent_cpass" value="<?php echo get_option('wc-paygent-cpass');?>" >
    <p class="description"><?php echo __( 'Please input Connect Password from Paygent documents', 'woocommerce-paygent-main2' );?></p></td>
</tr>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_company"><?php echo __( 'Site ID', 'woocommerce-paygent-main2' );?></label>
    </th>
    <td class="forminp"><input type="text" name="paygent_sid" value="<?php echo get_option('wc-paygent-sid');?>" >
    <p class="description"><?php echo __( 'Please input Site ID from Paygent documents', 'woocommerce-paygent-main2' );?></p></td>
</tr>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_paygent_pcf"><?php echo __( 'Client Cert File', 'woocommerce-paygent-main2' );?></label>
    </th>
    <td class="forminp"><input type="file" name="clientc_file" size="30" >
    <p class="description"><?php if(!file_exists(CLIENT_FILE_PATH)){ echo __( 'Please select Client Cert File from local.', 'woocommerce-paygent-main2' );}else{echo __( 'If you want to change Client Cert File, please select New Client Cert File from local.', 'woocommerce-paygent-main2' );}?></p></td>
</tr>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_paygent_ccf"><?php echo __( 'CA Cert File', 'woocommerce-paygent-main2' );?></label>
    </th>
    <td class="forminp"><input type="file" name="cac_file" size="30" >
    <p class="description"><?php if(!file_exists(CA_FILE_PATH)){echo __( 'Please select CA Cert File from local.', 'woocommerce-paygent-main2' );}else{echo __( 'If you want to change CA Cert File, please select New CA Cert File from local.', 'woocommerce-paygent-main2' );}?></p></td>
</tr>
</table>
<h3><?php echo __( 'Paygent Payment Method', 'woocommerce-paygent-main2' );?></h3>
<table class="form-table">
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_cc"><?php echo __( 'Credit Card', 'woocommerce-paygent-main2' );?></label>
    </th>
    <td class="forminp"><input type="checkbox" name="paygent_cc" value="1" <?php $options['wc-paygent-cc'] =get_option('wc-paygent-cc') ;checked( $options['wc-paygent-cc'], 1 ); ?>><?php echo __( 'Credit Card', 'woocommerce-paygent-main2' );?>
    <p class="description"><?php echo __( 'Please check it if you want to use the payment method of Credit Card', 'woocommerce-paygent-main2' );?></p></td>
</tr>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_cs"><?php echo __( 'Convenience store', 'woocommerce-paygent-main2' );?></label>
    </th>
    <td class="forminp"><input type="checkbox" name="paygent_cs" value="1" <?php $options['wc-paygent-cs'] =get_option('wc-paygent-cs') ;checked( $options['wc-paygent-cs'], 1 ); ?>><?php echo __( 'Convenience store', 'woocommerce-paygent-main2' );?>
    <p class="description"><?php echo __( 'Please check it if you want to use the payment method of Convenience store', 'woocommerce-paygent-main2' );?></p></td>
</tr>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_mccc"><?php echo __( 'Multi-currency Credit Card', 'woocommerce-paygent-main2' );?></label>
    </th>
    <td class="forminp"><input type="checkbox" name="paygent_mccc" value="1" <?php $options['wc-paygent-mccc'] =get_option('wc-paygent-mccc') ;checked( $options['wc-paygent-mccc'], 1 ); ?>><?php echo __( 'Multi-currency Credit Card', 'woocommerce-paygent-main2' );?>
    <p class="description"><?php echo __( 'Please check it if you want to use the payment method of Multi-currency Credit Card', 'woocommerce-paygent-main2' );?></p></td>
</tr>
</table>

<h3><?php echo __( 'Set Credit Card', 'woocommerce-paygent-main2' );?></h3>
<table class="form-table">
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_setcard"><?php echo __( 'Set able to Use Credit Card', 'woocommerce-paygent-main2' );?></label>
    </th>
    <td class="forminp"><input type="checkbox" name="paygent_cc_vm" value="1" <?php $options['wc-paygent-cc-vm'] =get_option('wc-paygent-cc-vm') ;checked( $options['wc-paygent-cc-vm'], 1 ); ?>><?php echo __( 'Visa & Master', 'woocommerce-paygent-main2' );?>
    <input type="checkbox" name="paygent_cc_d" value="1" <?php $options['wc-paygent-cc-d'] =get_option('wc-paygent-cc-d') ;checked( $options['wc-paygent-cc-d'], 1 ); ?>><?php echo __( 'Dinners', 'woocommerce-paygent-main2' );?>
    <input type="checkbox" name="paygent_cc_aj" value="1" <?php $options['wc-paygent-cc-aj'] =get_option('wc-paygent-cc-aj') ;checked( $options['wc-paygent-cc-aj'], 1 ); ?>><?php echo __( 'AMEX & JCB', 'woocommerce-paygent-main2' );?>
    <p class="description"><?php echo __( 'Please check them you are able to use Credit Card', 'woocommerce-paygent-main2' );?></p></td>
</tr>
</table>

<h3><?php echo __( 'Test Mode', 'woocommerce-paygent-main2' );?></h3>
<table class="form-table">
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_testmode"><?php echo __( 'Test Mode', 'woocommerce-paygent-main2' );?></label>
    </th>
    <td class="forminp"><input type="checkbox" name="paygent_testmode" value="1" <?php $options['wc-paygent-testmode'] =get_option('wc-paygent-testmode') ;checked( $options['wc-paygent-testmode'], 1 ); ?>><?php echo __( 'Test Mode', 'woocommerce-paygent-main2' );?>
    <p class="description"><?php echo __( 'Please check it if you want to use Testmode', 'woocommerce-paygent-main2' );?></p></td>
</tr>
</table>
<p class="submit">
   <input name="save" class="button-primary" type="submit" value="<?php echo __( 'Save changes', 'woocommerce-paygent-main2' );?>">
</p>
</form>
