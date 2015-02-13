<?php
/**
 * Query Filter Functions
 *
 * @package UTUBE_SCASE
 * @version 1.0.1
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
function utube_scase_query_filters($query) {
	$has_limitby = get_option("utube_scase_has_limitby_cap");
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
			$query = emd_limit_author_search('utube_scase', $query, $has_limitby);
		}
	}
	return $query;
}
add_action('pre_get_posts', 'utube_scase_query_filters');
