<div class="wrap woocommerce">
    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=wc4jp-paygent-output') ?>" class="nav-tab <?php echo ($tab == 'setting') ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Setting', 'woocommerce-paygent-main2' )?></a><a href="<?php echo admin_url('admin.php?page=wc4jp-paygent-output&tab=info') ?>" class="nav-tab <?php echo ($tab == 'info') ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Infomations', 'woocommerce-paygent-main2' )?></a>
    </h2>
	<?php
		switch ($tab) {
			case "setting" :
				$this->admin_paygent_setting_page();
			break;
			default :
				$this->admin_paygent_info_page();
			break;
		}
	?>
</div>
