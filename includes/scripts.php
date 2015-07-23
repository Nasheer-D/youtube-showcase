<?php
/**
 * Enqueue Scripts Functions
 *
 * @package YT_SCASE_COM
 * @version 1.4.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
add_action('admin_enqueue_scripts', 'yt_scase_com_load_admin_enq');
/**
 * Enqueue style and js for each admin entity pages and settings
 *
 * @since WPAS 4.0
 * @param string $hook
 *
 */
function yt_scase_com_load_admin_enq($hook) {
	global $typenow;
	if ($hook == 'edit-tags.php') {
		return;
	}
	if ($hook == 'toplevel_page_yt_scase_com' || $hook == 'video-settings_page_yt_scase_com_notify' || $hook == 'video-settings_page_yt_scase_com_settings') {
		wp_enqueue_script('accordion');
		return;
	} else if (in_array($hook, Array(
		'video-settings_page_yt_scase_com_store',
		'video-settings_page_yt_scase_com_designs',
		'video-settings_page_yt_scase_com_support'
	))) {
		wp_enqueue_style('admin-tabs', YT_SCASE_COM_PLUGIN_URL . 'assets/css/admin-store.css');
		return;
	}
	if (in_array($typenow, Array(
		'emd_video'
	))) {
		$theme_changer_enq = 1;
		$datetime_enq = 0;
		$date_enq = 0;
		$sing_enq = 0;
		$tab_enq = 0;
		if ($hook == 'post.php' || $hook == 'post-new.php') {
			$unique_vars['msg'] = __('Please enter a unique value.', 'yt-scase-com');
			$unique_vars['reqtxt'] = __('required', 'yt-scase-com');
			$unique_vars['app_name'] = 'yt_scase_com';
			$ent_list = get_option('yt_scase_com_ent_list');
			if (!empty($ent_list[$typenow])) {
				$unique_vars['keys'] = $ent_list[$typenow]['unique_keys'];
				if (!empty($ent_list[$typenow]['req_blt'])) {
					$unique_vars['req_blt_tax'] = $ent_list[$typenow]['req_blt'];
				}
			}
			$tax_list = get_option('yt_scase_com_tax_list');
			if (!empty($tax_list[$typenow])) {
				foreach ($tax_list[$typenow] as $txn_name => $txn_val) {
					if ($txn_val['required'] == 1) {
						$unique_vars['req_blt_tax'][$txn_name] = Array(
							'hier' => $txn_val['hier'],
							'type' => $txn_val['type'],
							'label' => $txn_val['label'] . ' ' . __('Taxonomy', 'yt-scase-com')
						);
					}
				}
			}
			wp_enqueue_script('unique_validate-js', YT_SCASE_COM_PLUGIN_URL . 'assets/js/unique_validate.js', array(
				'jquery',
				'jquery-validate'
			) , YT_SCASE_COM_VERSION, true);
			wp_localize_script("unique_validate-js", 'unique_vars', $unique_vars);
		}
		if ($datetime_enq == 1) {
			wp_enqueue_script("jquery-ui-timepicker", YT_SCASE_COM_PLUGIN_URL . 'assets/ext/emd-meta-box/js/jqueryui/jquery-ui-timepicker-addon.js', array(
				'jquery-ui-datepicker',
				'jquery-ui-slider'
			) , YT_SCASE_COM_VERSION, true);
			$tab_enq = 1;
		} elseif ($date_enq == 1) {
			wp_enqueue_script("jquery-ui-datepicker");
			$tab_enq = 1;
		}
	}
}
add_action('wp_enqueue_scripts', 'yt_scase_com_frontend_scripts');
/**
 * Enqueue style and js for each frontend entity pages and components
 *
 * @since WPAS 4.0
 *
 */
function yt_scase_com_frontend_scripts() {
	$dir_url = YT_SCASE_COM_PLUGIN_URL;
	$grid_vars = Array();
	$local_vars['ajax_url'] = admin_url('admin-ajax.php');
	$wpas_shc_list = get_option('yt_scase_com_shc_list');
	wp_register_style('wpas-boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
	wp_register_script('wpas-boot-js', $dir_url . 'assets/ext/wpas/bootstrap.min.js');
	wp_register_style('allview-css', $dir_url . '/assets/css/allview.css');
	if (is_single() && get_post_type() == 'emd_video') {
		wp_enqueue_script('jquery');
		wp_enqueue_style('wpas-boot');
		wp_enqueue_script('wpas-boot-js');
		wp_enqueue_style('single-video-cdn');
		wp_enqueue_script('single-video-cdn');
		wp_enqueue_style('allview-css');
		return;
	}
}
