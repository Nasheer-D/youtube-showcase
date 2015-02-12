<?php
/**
 * Enqueue Scripts Functions
 *
 * @package UTUBE_SCASE
 * @version 1.0.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
add_action('admin_enqueue_scripts', 'utube_scase_load_admin_enq');
/**
 * Enqueue style and js for each admin entity pages and settings
 *
 * @since WPAS 4.0
 * @param string $hook
 *
 */
function utube_scase_load_admin_enq($hook) {
	if ($hook == 'toplevel_page_utube_scase' || $hook == 'video-settings_page_utube_scase_notify') {
		wp_enqueue_script('accordion');
		return;
	} else if ($hook == 'video-settings_page_utube_scase_store') {
		wp_enqueue_style('admin-tabs', UTUBE_SCASE_PLUGIN_URL . 'assets/css/admin-store.css');
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
			wp_enqueue_script('unique_validate-js', UTUBE_SCASE_PLUGIN_URL . 'assets/js/unique_validate.js', array(
				'jquery',
				'jquery-validate'
			) , UTUBE_SCASE_VERSION, true);
			wp_localize_script("unique_validate-js", 'unique_vars', $unique_vars);
		}
		wp_enqueue_style("jq-css", "//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css");
		if ($datetime_enq == 1) {
			wp_enqueue_script("jquery-ui-timepicker", UTUBE_SCASE_PLUGIN_URL . 'assets/ext/meta-box/js/jqueryui/jquery-ui-timepicker-addon.js', array(
				'jquery-ui-datepicker',
				'jquery-ui-slider'
			) , UTUBE_SCASE_VERSION, true);
		} elseif ($date_enq == 1) {
			wp_enqueue_script("jquery-ui-datepicker");
		}
	}
}
add_action('wp_enqueue_scripts', 'utube_scase_frontend_scripts');
/**
 * Enqueue style and js for each frontend entity pages and components
 *
 * @since WPAS 4.0
 *
 */
function utube_scase_frontend_scripts() {
	$dir_url = UTUBE_SCASE_PLUGIN_URL;
	if (is_page()) {
		$grid_vars = Array();
		$local_vars['ajax_url'] = admin_url('admin-ajax.php');
		$wpas_shc_list = get_option('utube_scase_shc_list');
		$check_content = "";
		if (!is_author() && !is_tax()) {
			$check_content = get_post(get_the_ID())->post_content;
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_items')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', '//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
			wp_enqueue_style('allview-css', UTUBE_SCASE_PLUGIN_URL . '/assets/css/allview.css');
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_indicators')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', '//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
			wp_enqueue_style('allview-css', UTUBE_SCASE_PLUGIN_URL . '/assets/css/allview.css');
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_grid')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', '//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
			wp_enqueue_style('allview-css', UTUBE_SCASE_PLUGIN_URL . '/assets/css/allview.css');
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_gallery')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', '//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js');
			if (!empty($wpas_shc_list['integrations']['video_gallery']['datagrids'])) {
				$datagrids = $wpas_shc_list['integrations']['video_gallery']['datagrids'];
				foreach ($datagrids as $myint_dgrid) {
					$grid_vars[] = Emd_Datagrid::emd_get_gridvars('utube_scase', $myint_dgrid);
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
