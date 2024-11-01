<?php
defined('ABSPATH') or die("No script kiddies please!");
?>

<h2><?php echo __( 'Shop Metrics Report - Settings', 'shopmetrics-for-wp' ); ?></h2>
<p style="max-width: 600px;"><?php printf( __( 'Manage your settings here of the Shop Metrics for WooCommerce plugin. Once you have configured your webshop on <a href="%s" target="_blank">ShopMetrics.report</a> and you have generated the Salt and Pepper for your webshop, you can enable this plugin. The plugin should push your orders to Shop Metrics to analyze them and generate stunning charts.', 'shopmetrics-for-wp'), 'https://shopmetrics.report/?utm_source=wordpress_backend&utm_medium=settings_link&utm_campaign=wordpress_backend' ); ?></p>

<form action="<?php echo admin_url(); ?>admin.php?page=smr_for_wp_settings" method="post">
	<?php wp_nonce_field( 'shopmetrics-for-wp-settings' ); ?>
	<table class="form-table"><tr>
		<th scope="row"><?php _e('Use data from this plugin', 'shopmetrics-for-wp'); ?>:</th>
			<td><fieldset><legend class="screen-reader-text"><span><?php _e('Track plugins', 'shopmetrics-for-wp'); ?></plugins></span></legend>
					<label for="plugins-none">
						<input name="plugin" type="radio" id="plugins-none" value="none" <?php if(!isset($settings['plugin']) || $settings['plugin']=='none'): echo "checked='checked'"; endif; ?> />
						<?php _e('None', 'shopmetrics-for-wp'); ?></label><br />
					<label for="plugins-woocommerce">
						<input name="plugin" type="radio" id="plugins-woocommerce" value="WC" <?php if(isset($settings['plugin']) && $settings['plugin']=='WC'): echo "checked='checked'"; endif; ?> />
						<?php _e('WooCommerce (WC)', 'shopmetrics-for-wp'); ?> <?php if( isset($installed['woocommerce']) ): echo ' - <font color="green">' . __('Installed!', 'shopmetrics-for-wp') . '</font>'; endif; ?></label><br />
					<!--<label for="plugins-edd">
						<input name="plugin" type="radio" id="plugins-edd" value="EDD" <?php if(isset($settings['plugin']) && $settings['plugin']=='EDD'): echo "checked='checked'"; endif; ?> />
						<?php _e('Easy Digital Downloads (EDD)', 'shopmetrics-for-wp'); ?> <?php if( isset($installed['edd']) ): echo ' - <font color="green">' . __('Installed!', 'shopmetrics-for-wp') . '</font>'; endif; ?></label><br />-->
					<a href="https://shopmetrics.report/support/plugin-not-listed-wp" target="_blank"><i><?php _e( 'Help! The plugin I want to use isn\'t listed here..', 'shopmetrics-for-wp' ); ?></i></a>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="webshop-salt"><?php _e('Webshop API Salt', 'shopmetrics-for-wp'); ?>:</label></th>
			<td><input name="webshop_salt" type="text" id="webshop-salt" value="<?php echo $settings['webshop_salt']; ?>" class="regular-text ltr" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="webshop-pepper"><?php _e('Webshop API Pepper', 'shopmetrics-for-wp'); ?>:</label></th>
			<td>
				<input name="webshop_pepper" type="text" id="webshop-pepper" value="<?php echo $settings['webshop_pepper']; ?>" class="regular-text ltr" />
			</td>
		</tr>
		<tr>
			<th>
				<a href="https://dashboard.shopmetrics.report/?utm_source=wordpress_backend&utm_medium=settings_dashboard_link&utm_campaign=wordpress_backend" class="button" target="_blank"><?php _e('View your dashboard', 'shopmetrics-for-wp'); ?></a>
			</th>
			<td>
				<input type="submit" name="submit" class="button calc_totals button-primary" value="<?php echo __('Save settings', 'shopmetrics-for-wp'); ?>">
			</td>
		</tr>
	</table>
</form>