<?php
/**
 * Integration Shortcode Functions
 *
 * @package UTUBE_SCASE
 * @version 1.0.1
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
add_shortcode('video_gallery', 'utube_scase_get_integ_video_gallery');
/**
 * Display integration shortcode or no access msg
 * @since WPAS 4.0
 *
 * @return string $layout or $no_access_msg
 */
function utube_scase_get_integ_video_gallery() {
	$no_access_msg = __('You are not allowed to access to this area. Please contact the site administrator.', 'utube-scase');
	$access_views = get_option('utube_scase_access_views', Array());
	if (!current_user_can('view_video_gallery') && !empty($access_views['integration']) && in_array('video_gallery', $access_views['integration'])) {
		return $no_access_msg;
	} else {
		ob_start();
		emd_get_template_part('utube-scase', 'integration', 'video-gallery');
		$layout = ob_get_clean();
		return $layout;
	}
}
