<?php
defined('ABSPATH') or die("No script kiddies please!");
?>

<h2><?php echo __( 'Shop Metrics for WP - Test API Connection', 'shopmetrics-for-wp' ); ?></h2>
<p style="max-width: 600px;"><?php echo __( 'The testresults are listed below. Please make sure you have filled in your webshop salt and pepper.', 'shopmetrics-for-wp' ); ?></p>

<div class="postbox" style="max-width: 600px; padding: 10px;">

	<table width="100%">
		<thead style="line-height: 30px;">
		<th width="70%" align="left"><?php echo __( 'Action', 'shopmetrics-for-wp' ); ?>:</th>
		<th align="left"><?php echo __( 'Result', 'shopmetrics-for-wp' ); ?>:</th>
		</thead>
		<tbody>
		<tr style="line-height: 30px;">
			<td width="70%"><?php echo __( 'Connection to API', 'shopmetrics-for-wp' ); ?>:</td>
			<td align="left"><strong><?php if ( $result['basic'] ) {
					echo '<span style="color: green;">Ok!</span>';
				} else {
					echo '<span style="color: red;">Fail</span>';
				} ?></strong></td>
		</tr>
		<tr style="line-height: 30px;">
			<td width="70%"><?php echo __( 'Test salt and pepper', 'shopmetrics-for-wp' ); ?>:</td>
			<td align="left"><strong><?php if ( $result['api'] ) {
					echo '<span style="color: green;">Ok!</span>';
				} else {
					echo '<span style="color: red;">Fail</span> <a href="admin.php?page=smr_for_wp_settings" class="button">Settings</a>';
				} ?></strong></td>
		</tr>
		<?php if(isset($result['domain'])): ?>
		<tr style="line-height: 30px;">
			<td width="70%"><?php echo __( 'Result domain', 'shopmetrics-for-wp' ); ?>:</td>
			<td align="left"><strong><?php echo $result['domain']; ?></strong><br />
			<a href="https://dashboard.shopmetrics.report/?utm_source=wordpress_backend&utm_medium=test_dashboard_link&utm_campaign=wordpress_backend" class="button" target="_blank"><?php _e('View your dashboard', 'shopmetrics-for-wp'); ?></a></td>
		</tr>
		<?php endif; ?>
		</tbody>
	</table>
</div>