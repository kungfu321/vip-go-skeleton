<?php

class nhymxu_at_coupon_admin {
	public function __construct() {
		add_action( 'admin_menu', [$this,'admin_page'] );
	}

	public function admin_page() {
		add_menu_page( 'Cài đặt Coupon', 'Auto Affiliate Coupons', 'manage_options', 'accesstrade_coupon', [$this, 'admin_page_callback_settings'], 'dashicons-tickets', 6 );
		add_submenu_page( 'accesstrade_coupon', 'Cài đặt Coupon', 'Cài đặt', 'manage_options', 'auto_affiliate_coupon_settings', [$this, 'admin_page_callback_settings'] );
	}

	/*
	 * Admin page setting
	 */
	public function admin_page_callback_settings() {
		global $wpdb;
		if( isset( $_POST, $_POST['nhymxu_hidden'] ) && $_POST['nhymxu_hidden'] == 'coupon' ) {
			$input = [
				'masoffer_id'	=> sanitize_text_field($_REQUEST['codetech_masoffer']),
				'ecomobi_id'	=> sanitize_text_field($_REQUEST['codetech_ecomobi']),
				'accesstrade_id'	=> sanitize_text_field($_REQUEST['codetech_coupon_accesstrade']),
				'lazada_id'	=> sanitize_text_field($_REQUEST['codetech_lazada']),
				'utmsource'	=> sanitize_text_field($_REQUEST['codetech_coupon_utmsource']),
				'tool_token'	=> sanitize_text_field($_REQUEST['codetech_tool_token']),
			];

			update_option('nhymxu_at_coupon', $input);
			echo '<h1>Cập nhật thành công</h1><br>';
		}
		$option = get_option('nhymxu_at_coupon', ['masoffer_id' => '', 'ecomobi_id' => '', 'accesstrade_id' => '', 'lazada_id' => '', 'utmsource' => '', 'tool_token' => '']);
		$masoffer_id = (isset($option['masoffer_id'])) ? $option['masoffer_id'] : '';
		$accesstrade_id = (isset($option['accesstrade_id'])) ? $option['accesstrade_id'] : '';
		$ecomobi_id = (isset($option['ecomobi_id'])) ? $option['ecomobi_id'] : '';
		$lazada_id = (isset($option['lazada_id'])) ? $option['lazada_id'] : '';
		$tool_token = (isset($option['tool_token'])) ? $option['tool_token'] : '';
		?>
		<script type="text/javascript">
		function nhymxu_force_update_coupons() {
			var is_run = jQuery('#nhymxu_force_update').data('run');
			if( is_run !== 0 ) {
				console.log('Đã chạy rồi');
				return false;
			}
			jQuery('#nhymxu_force_update').attr('disabled', 'disabled');
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: { action: 'nhymxu_coupons_ajax_forceupdate' },
				success: function(response) {
					alert('Khởi chạy thành công. Vui lòng đợi vài phút để dữ liệu được cập nhật.');
				}
			});
		}
		function nhymxu_clear_expired_coupon() {
			var is_run = jQuery('#nhymxu_clear_expired').data('run');
			if( is_run !== 0 ) {
				console.log('Đã chạy rồi');
				return false;
			}
			jQuery('#nhymxu_clear_expired').attr('disabled', 'disabled');
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: { action: 'nhymxu_coupons_ajax_clearexpired' },
				success: function(response) {
					if( response === 'failed' ) {
						alert('Dọn dẹp thất bại, vui lòng thử lại sau');
						return false;
					}
					alert('Đã xoá ' + response + ' coupon hết hạn.');
					return true;
				}
			});
		}
		</script>
		<div>
			<h2>Cài đặt Auto Affiliate Coupons</h2>
			<form action="<?=admin_url( 'admin.php?page=auto_affiliate_coupon_settings' );?>" method="post">
				<input type="hidden" name="nhymxu_hidden" value="coupon">
				<table>
					<tr>
						<td>MASOFFER ID*:</td>
						<td><input type="text" name="codetech_masoffer" value="<?=$masoffer_id;?>"></td>
					</tr>
					<tr>
						<td>ECOMOBI TOKEN*:</td>
						<td><input type="text" name="codetech_ecomobi" value="<?=$ecomobi_id;?>"></td>
					</tr>
					<tr>
						<td>ACCESSTRADE ID*:</td>
						<td><input type="text" name="codetech_coupon_accesstrade" value="<?=$accesstrade_id;?>"></td>
					</tr>
					<tr>
						<td>LAZADA ID*:</td>
						<td><input type="text" name="codetech_lazada" value="<?=$lazada_id;?>"></td>
					</tr>
					<tr>
						<td></td>
						<td>Hướng dẫn lấy ID tại <a href="https://justinvo.com" target="_blank">đây</a></td>
					</tr>
					<tr>
						<td>UTM Source:</td>
						<td><input type="text" name="codetech_coupon_utmsource" value="<?=(isset($option['utmsource'])) ? $option['utmsource'] : '';?>"></td>
					</tr>
					<tr>
						<td>Tool Token:</td>
						<td><input type="text" name="codetech_tool_token" value="<?=$tool_token;?>"></td>
					</tr>
				</table>
				<input name="Submit" type="submit" value="Lưu">
			</form>
		</div>
		<hr>
		<div>
			<h3>Thông tin coupon</h3>
			<?php
			$total_coupon = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}coupons" );
			$today = date('Y-m-d');
			$total_expired_coupon = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}coupons WHERE exp < '{$today}'" );
			?>
			<p>Tổng số coupon trong hệ thống: <strong><?=$total_coupon;?></strong></p>
			<p>
				Tổng số coupon hết hạn: <strong><?=$total_expired_coupon;?></strong>&nbsp;
				<?php if( $total_expired_coupon > 0 ): ?>
				- <button id="nhymxu_clear_expired" data-run="0" onclick="nhymxu_clear_expired_coupon();">Dọn dẹp ngay</button>
				<?php endif; ?>
			</p>
			<?php $last_run = (int) get_option('codetech_at_coupon_sync_time', 0); $now = time(); ?>

			<p>
				Lần đồng bộ cuối: <strong><?=( $last_run == 0 ) ? 'chưa rõ' : date("Y-m-d H:i:s", $last_run);?></strong>
				<?php if( $last_run == 0 || ( ($now - $last_run) >= 1800 ) ): ?>
				- <button id="nhymxu_force_update" data-run="0" onclick="nhymxu_force_update_coupons();">Cập nhật ngay</button>
				<?php endif; ?>
			</p>
		</div>
		<?php
	}
}
