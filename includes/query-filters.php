<?php
/**
 * Query Filter Functions
 *
 * @package YT_SCASE_COM
 * @version 1.3.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Change query parameters before wp_query is processed
 *
 * @since WPAS 4.0
 * @param object $query
 *
 * @return object $query
 */
function yt_scase_com_query_filters($query) {
	$has_limitby = get_option("yt_scase_com_has_limitby_cap");
	if (!is_admin() && $query->is_main_query()) {
		if ($query->is_category && empty($query->query_vars['post_type'])) {
			$query->query_vars['post_type'] = Array(
				"post",
				"emd_video"
			);
		}
		if ($query->is_tag && empty($query->query_vars['post_type'])) {
			$query->query_vars['post_type'] = Array(
				"post",
				"emd_video"
			);
		}
		if ($query->is_author || $query->is_search) {
			$query = emd_limit_author_search('yt_scase_com', $query, $has_limitby);
		}
	}
	return $query;
}
add_action('pre_get_posts', 'yt_scase_com_query_filters');
