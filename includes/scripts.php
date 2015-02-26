<?php
/**
 * Enqueue Scripts Functions
 *
 * @package YT_SCASE_COM
 * @version 1.1
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
	if ($hook == 'toplevel_page_yt_scase_com' || $hook == 'video-settings_page_yt_scase_com_notify') {
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
	global $post;
	if (isset($post) && in_array($post->post_type, Array(
		'emd_video'
	))) {
		$theme_changer_enq = 1;
		$datetime_enq = 0;
		$date_enq = 0;
		$sing_enq = 0;
		if ($hook == 'post.php' || $hook == 'post-new.php') {
			$unique_vars['msg'] = __('Please enter a unique value.', 'emd-plugins');
			wp_enqueue_script('unique_validate-js', YT_SCASE_COM_PLUGIN_URL . 'assets/js/unique_validate.js', array(
				'jquery',
				'jquery-validate'
			) , YT_SCASE_COM_VERSION, true);
			wp_localize_script("unique_validate-js", 'unique_vars', $unique_vars);
		}
		wp_enqueue_style("jq-css", "//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css");
		if ($datetime_enq == 1) {
			wp_enqueue_script("jquery-ui-timepicker", YT_SCASE_COM_PLUGIN_URL . 'assets/ext/emd-meta-box/js/jqueryui/jquery-ui-timepicker-addon.js', array(
				'jquery-ui-datepicker',
				'jquery-ui-slider'
			) , YT_SCASE_COM_VERSION, true);
		} elseif ($date_enq == 1) {
			wp_enqueue_script("jquery-ui-datepicker");
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
	if (is_page()) {
		$grid_vars = Array();
		$local_vars['ajax_url'] = admin_url('admin-ajax.php');
		$wpas_shc_list = get_option('yt_scase_com_shc_list');
		$check_content = "";
		if (!is_author() && !is_tax()) {
			$check_content = get_post(get_the_ID())->post_content;
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_items')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', '//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
			wp_enqueue_style('allview-css', YT_SCASE_COM_PLUGIN_URL . '/assets/css/allview.css');
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_indicators')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', '//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
			wp_enqueue_style('allview-css', YT_SCASE_COM_PLUGIN_URL . '/assets/css/allview.css');
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_grid')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', '//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
			wp_enqueue_style('allview-css', YT_SCASE_COM_PLUGIN_URL . '/assets/css/allview.css');
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_gallery')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', '//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
			if (!empty($wpas_shc_list['integrations']['video_gallery']['datagrids'])) {
				$datagrids = $wpas_shc_list['integrations']['video_gallery']['datagrids'];
				foreach ($datagrids as $myint_dgrid) {
					$grid_vars[] = Emd_Datagrid::emd_get_gridvars('yt_scase_com', $myint_dgrid);
				}
			}
			wp_enqueue_style('allview-css', $dir_url . '/assets/css/allview.css');
		}
		return;
	}
	if (is_single() && get_post_type() == 'emd_video') {
		wp_enqueue_script('jquery');
		wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
		wp_enqueue_script('boot-js', '//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
		wp_enqueue_style('allview-css', $dir_url . '/assets/css/allview.css');
		return;
	}
}
