<?php
/**
 *  Uninstall Youtube Showcase
 *
 * Uninstalling deletes notifications and terms initializations
 *
 * @package YT_SCASE_COM
 * @version 1.4.0
 * @since WPAS 4.0
 */
if (!defined('WP_UNINSTALL_PLUGIN')) exit;
if (!current_user_can('activate_plugins')) return;
function yt_scase_com_uninstall() {
	//delete options
	$options_to_delete = Array(
		'yt_scase_com_notify_list',
		'yt_scase_com_ent_list',
		'yt_scase_com_attr_list',
		'yt_scase_com_shc_list',
		'yt_scase_com_tax_list',
		'yt_scase_com_rel_list',
		'yt_scase_com_license_key',
		'yt_scase_com_license_status',
		'yt_scase_com_comment_list',
		'yt_scase_com_access_views',
		'yt_scase_com_limitby_auth_caps',
		'yt_scase_com_limitby_caps',
		'yt_scase_com_has_limitby_cap',
		'yt_scase_com_setup_pages'
	);
	if (!empty($options_to_delete)) {
		foreach ($options_to_delete as $option) {
			delete_option($option);
		}
	}
	$emd_activated_plugins = get_option('emd_activated_plugins');
	if (!empty($emd_activated_plugins)) {
		$emd_activated_plugins = array_diff($emd_activated_plugins, Array(
			'yt-scase-com'
		));
		update_option('emd_activated_plugins', $emd_activated_plugins);
	}
}
if (is_multisite()) {
	global $wpdb;
	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
	if ($blogs) {
		foreach ($blogs as $blog) {
			switch_to_blog($blog['blog_id']);
			yt_scase_com_uninstall();
		}
		restore_current_blog();
	}
} else {
	yt_scase_com_uninstall();
}
