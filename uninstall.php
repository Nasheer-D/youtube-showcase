<?php
/**
 *  Uninstall Youtube Showcase
 *
 * Uninstalling deletes notifications and terms initializations
 *
 * @package UTUBE_SCASE
 * @version 1.0.1
 * @since WPAS 4.0
 */
if (!defined('WP_UNINSTALL_PLUGIN')) exit;
if (!current_user_can('activate_plugins')) return;
function utube_scase_uninstall() {
	//delete options
	$options_to_delete = Array(
		'utube_scase_notify_list',
		'utube_scase_ent_list',
		'utube_scase_attr_list',
		'utube_scase_shc_list',
		'utube_scase_tax_list',
		'utube_scase_rel_list',
		'utube_scase_license_key',
		'utube_scase_license_status',
		'utube_scase_comment_list',
		'utube_scase_access_views',
		'utube_scase_limitby_auth_caps',
		'utube_scase_limitby_caps',
		'utube_scase_has_limitby_cap',
		'utube_scase_setup_pages'
	);
	if (!empty($options_to_delete)) {
		foreach ($options_to_delete as $option) {
			delete_option($option);
		}
	}
	$emd_activated_plugins = get_option('emd_activated_plugins');
	if (!empty($emd_activated_plugins)) {
		$emd_activated_plugins = array_diff($emd_activated_plugins, Array(
			'utube-scase'
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
			utube_scase_uninstall();
		}
		restore_current_blog();
	}
} else {
	utube_scase_uninstall();
}
