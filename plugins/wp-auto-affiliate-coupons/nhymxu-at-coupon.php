<?php
/*
Plugin Name: Auto Affiliate Coupons
Plugin URI: https://justinvo.com
Description: Hệ thống coupon đồng bộ tự động từ các networks
Version: 0.6.2
*/

defined('ABSPATH') || die;
define('NHYMXU_AT_COUPON_VER', "0.6.2");

date_default_timezone_set('Asia/Ho_Chi_Minh');

class nhymxu_at_coupon
{

	public function __construct()
	{
		add_filter('http_request_host_is_external', [$this, 'allow_external_update_host'], 10, 3);
		add_action('nhymxu_at_coupon_sync_event', [$this, 'do_this_twicedaily']);
		add_shortcode('atcoupon', [$this, 'shortcode_callback']);
		add_shortcode('coupon', [$this, 'shortcode_callback']);
		add_action('init', [$this, 'init_updater']);
		add_action('wp_ajax_nhymxu_coupons_ajax_forceupdate', [$this, 'ajax_force_update']);
		add_action('wp_ajax_nhymxu_coupons_ajax_clearexpired', [$this, 'ajax_clear_expired_coupon']);
	}

	public function do_this_twicedaily()
	{
		global $wpdb;
		$previous_time = get_option('codetech_at_coupon_sync_time', 0);
		$current_time = time();
		$option = get_option('nhymxu_at_coupon', ['tool_token' => '']);

		if ($option['tool_token'] == '') return null;

		$url = 'https://autoaffiliate.herokuapp.com/api/v1/coupons?from=' . date("Y-m-d H:m:s", $previous_time) . '&to=' . date("Y-m-d H:m:s", $current_time) . '&type=wp';
		// $url = 'http://localhost:3000/api/v1/coupons?from=' . date("Y-m-d H:m:s", $previous_time) . '&to=' . date("Y-m-d H:m:s", $current_time);

		$result = wp_remote_get($url, [
			'timeout' => '60',
			'headers' => array(
				'tool_token' => $option['tool_token'],
			)
		]);

		echo("<script>console.log('PHP: ".$result."');</script>");
		if (is_wp_error($result)) {
			$msg = [];
			$msg['previous_time'] = $previous_time;
			$msg['current_time'] = $current_time;
			$msg['error_msg'] = $result->get_error_message();
			$msg['action'] = 'get_remote_data';

			$this->insert_log($msg);
		} else {
			$result_json = json_decode($result['body'], true);
			$input = $result_json['data'];
			if (!empty($input) && isset($input[0]) && is_array($input[0])) {
				$wpdb->query("START TRANSACTION;");
				try {
					foreach ($input as $cp) {
						$this->insert_coupon($cp);
					}
					update_option('codetech_at_coupon_sync_time', $current_time);
					$wpdb->query("COMMIT;");
				} catch (Exception $e) {
					$msg = [];
					$msg['previous_time'] = $previous_time;
					$msg['current_time'] = $current_time;
					$msg['error_msg'] = $e->getMessage();
					$msg['action'] = 'insert_data';

					$this->insert_log($msg);

					$wpdb->query("ROLLBACK;");
				}
			}
		}
	}

	public function shortcode_callback($atts, $content = '')
	{
		$args = shortcode_atts([
			'type' => '',
			'cat'	=> '',
			'limit' => '',
			'coupon' => '',
		], $atts);

		if ('' == $args['type'])
			return '';

		$data = $this->get_coupons($args);
		$html = $this->build_html($data);

		return $html;
	}

	/*
	 * Get list coupon from database
	 */
	private function get_coupons($args)
	{
		global $wpdb;

		date_default_timezone_set('Asia/Ho_Chi_Minh');

		$vendor = $args['type'];
		$category = $args['cat'];
		$limit = $args['limit'];
		$has_coupon = $args['coupon'];

		$today = date('Y-m-d');

		$vendor = explode(',', $vendor);
		$vendor_slug = [];
		foreach ($vendor as $slug) {
			$vendor_slug[] = "'" . $slug . "'";
		}
		$vendor_slug = implode(',', $vendor_slug);

		$query_where = '';
		if ($has_coupon != '') {
			//$has_coupon = (int) $has_coupon;
			if ($has_coupon == '1') {
				$query_where = " AND coupons.code != ''";
			}
			if ($has_coupon == '0') {
				$query_where = " AND coupons.code == ''";
			}
		}

		$sql = "SELECT coupons.* FROM {$wpdb->prefix}coupons as coupons WHERE coupons.type IN ({$vendor_slug}) AND coupons.exp >= '{$today}' {$query_where} ORDER BY coupons.exp ASC";

		if ($category != '') {
			$cat_slug = explode(',', $category);
			$cat_slug_arr = [];
			foreach ($cat_slug as $cat) {
				$cat_slug_arr[] = "'" . trim($cat) . "'";
			}
			$cat_slug_arr = implode(',', $cat_slug_arr);

			$coupon_cats = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}coupon_categories WHERE slug IN ({$cat_slug_arr})");
			if (!$coupon_cats) {
				return false;
			}
			$cat_id = [];
			foreach ($coupon_cats as $row) {
				$cat_id[] = $row->id;
			}
			$cat_id = implode(',', $cat_id);

			$sql = "SELECT coupons.* FROM {$wpdb->prefix}coupons AS coupons
					LEFT JOIN {$wpdb->prefix}coupon_category_rel AS rel
						ON rel.coupon_id = coupons.id
					WHERE coupons.type IN ({$vendor_slug})
						AND rel.category_id IN ({$cat_id})
						AND coupons.exp >= '{$today}' 
						{$query_where} 
					ORDER BY coupons.exp ASC";
		}

		if ($limit != '' && $limit >= 0) {
			$sql .= ' LIMIT 0,' . $limit;
		}

		$results = $wpdb->get_results($sql, ARRAY_A);

		if ($results) {
			$coupon_id = [];
			$data = [];
			foreach ($results as $row) {
				$coupon_id[] = $row['id'];
				$data[$row['id']] = $row;
				$data[$row['id']]['categories'] = [];
				$data[$row['id']]['deeplink'] = $this->build_deeplink($row['url'], $row['network']);
			}
			$sql = "SELECT rel.*, cat.name
					FROM {$wpdb->prefix}coupon_category_rel rel
					LEFT JOIN {$wpdb->prefix}coupon_categories cat
						ON rel.category_id = cat.id
					WHERE rel.coupon_id IN (" . implode(',', $coupon_id) . ")";
			$cats = $wpdb->get_results($sql, ARRAY_A);
			foreach ($cats as $cat) {
				$data[$cat['coupon_id']]['categories'][] = $cat['name'];
			}

			return $data;
		}

		return false;
	}

	/*
	 * Build html template from coupon data
	 */
	private function build_html($at_coupons)
	{
		if (!$at_coupons) {
			return '';
		}

		ob_start();

		$template_dir_loader = plugin_dir_path(__FILE__);

		if (file_exists(get_template_directory() . '/accesstrade_coupon_template.php')) {
			$template_dir_loader = get_template_directory() . '/';
		}

		require $template_dir_loader . 'accesstrade_coupon_template.php';

		$html = ob_get_clean();
		return $html;
	}

	private function build_deeplink($url, $network)
	{
		$option = get_option('nhymxu_at_coupon', ['masoffer_id' => '', 'accesstrade_id' => '', 'ecomobi_id' => '', 'lazada_id' => '', 'utmsource' => '']);
		$utm_source = '';
		if ($option['utmsource'] != '') {
			$utm_source = '&utm_source=' . $option['utmsource'];
		}

		if ($option['masoffer_id'] || $option['accesstrade_id'] || $option['ecomobi_id'] || $option['lazada_id']) {
			switch ($network) {
				case 'masoffer':
					return 'http://go.masoffer.net/v0/' . $option['masoffer_id'] . '?url=' . rawurlencode($url) . $utm_source . '&at_source=auto-affiliate-coupons';
				case 'accesstrade':
					return 'https://go.isclix.com/deep_link/' . $option['accesstrade_id'] . '?url=' . rawurlencode($url) . $utm_source . '&at_source=auto-affiliate-coupons';
				case 'ecomobi':
					return 'https://go.ecotrackings.com/?token=' . $option['ecomobi_id'] . '&url=' . rawurlencode($url) . $utm_source . '&at_source=auto-affiliate-coupons';
				case 'lazada':
					return 'https://c.lazada.vn/t/' . $option['lazada_id'] . '?url=' . rawurlencode($url) . $utm_source . '&at_source=auto-affiliate-coupons';

				default:
					return $url;
			}
		}

		return $url;
	}

	/*
	 * Force update coupon from server
	 */
	public function ajax_force_update()
	{
		$this->do_this_twicedaily();
		echo 'running';
		wp_die();
	}

	public function allow_external_update_host($allow, $host, $url)
	{
		//if ( $host == 'sv.isvn.space' ) {$allow = true;}
		$allow = true;
		return $allow;
	}

	public function init_updater()
	{
		// if (is_admin()) {
		// 	if (!class_exists('nhymxu_AT_AutoUpdate')) {
		// 		require_once('nhymxu-updater.php');
		// 	}
		// 	// $plugin_remote_path = 'http://sv.isvn.space/wp-update/plugin-accesstrade-coupon.json';
		// 	$plugin_slug = plugin_basename(__FILE__);
		// 	$license_user = 'nhymxu';
		// 	$license_key = 'AccessTrade';
		// 	new nhymxu_AT_AutoUpdate(NHYMXU_AT_COUPON_VER, $plugin_remote_path, $plugin_slug, $license_user, $license_key);
		// }
	}

	public function insert_log($data)
	{
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'coupon_logs',
			[
				'created_at'	=> time(),
				'data'	=> json_encode($data)
			],
			['%d', '%s']
		);
	}

	public function clear_expired_coupon()
	{
		global $wpdb;

		$today = date('Y-m-d');
		$result = $wpdb->query("DELETE FROM {$wpdb->prefix}coupons WHERE exp < '{$today}'");

		return $result;
	}

	public function ajax_clear_expired_coupon()
	{
		$row_deleted = $this->clear_expired_coupon();

		if ($row_deleted === false) {
			echo 'failed';
			wp_die();
		}

		echo $row_deleted;
		wp_die();
	}

	private function insert_coupon($data)
	{
		global $wpdb;

		$result = $wpdb->insert(
			$wpdb->prefix . 'coupons',
			[
				'type'	=> $data['merchant_name'],
				'title' => trim($data['title']),
				'code'	=> ($data['code']) ? trim($data['code']) : '',
				'exp'	=> $data['exp'],
				'url'	=> ($data['link']) ? trim($data['link']) : '',
				'thumb' => $data['thumb'],
				'network' => $data['network'],
				'merchant_url' => $data['merchant_url'],
				'country' => $data['country'],
			],
			['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
		);

		if ($result) {
			$coupon_id = $wpdb->insert_id;
			if (isset($data['categories']) && !empty($data['categories'])) {
				$cat_ids = $this->get_coupon_category_id($data['categories']);
				foreach ($cat_ids as $row) {
					$wpdb->insert(
						$wpdb->prefix . 'coupon_category_rel',
						[
							'coupon_id' => $coupon_id,
							'category_id'	=> $row
						],
						['%d', '%d']
					);
				}
			}

			return 1;
		}

		$msg = [];
		$msg['previous_time'] = '';
		$msg['current_time'] = '';
		$msg['error_msg'] = json_encode($data);
		$msg['action'] = 'insert_coupon';

		$this->insert_log($msg);

		return 0;
	}

	private function get_coupon_category_id($input)
	{
		global $wpdb;

		$cat_id = [];

		foreach ($input as $row) {
			$slug = trim($row['slug']);
			$result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}coupon_categories WHERE slug = '{$slug}'");

			if ($result) {
				$cat_id[] = (int)$result->id;
			} else {
				$result = $wpdb->insert(
					$wpdb->prefix . 'coupon_categories',
					[
						'name'	=> trim($row['title']),
						'slug'	=> trim($row['slug'])
					],
					['%s', '%s']
				);
				$cat_id[] = (int)$wpdb->insert_id;
			}
		}

		return $cat_id;
	}
}

$nhymxu_at_coupon = new nhymxu_at_coupon();

if (is_admin()) {
	require_once __DIR__ . '/editor.php';
	new nhymxu_at_coupon_editor();

	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
	if (!is_plugin_active('nhymxu-at-coupon-pro/nhymxu-at-coupon-pro.php')) {
		require_once __DIR__ . '/admin.php';
		new nhymxu_at_coupon_admin();
	}
}

require_once __DIR__ . '/install.php';

register_activation_hook(__FILE__, ['nhymxu_at_coupon_install', 'plugin_install']);
register_deactivation_hook(__FILE__, ['nhymxu_at_coupon_install', 'plugin_deactive']);
register_uninstall_hook(__FILE__, ['nhymxu_at_coupon_install', 'plugin_uninstall']);
