<?php
/**
 * Entity Widget Classes
 *
 * @package UTUBE_SCASE
 * @version 1.0.1
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Entity widget class extends Emd_Widget class
 *
 * @since WPAS 4.0
 */
class utube_scase_recent_videos_widget extends Emd_Widget {
	public $title;
	public $text_domain = 'utube-scase';
	public $class_label;
	public $class = 'emd_video';
	public $type = 'entity';
	public $has_pages = false;
	public $css_label = 'recent-videos';
	public $id = 'utube_scase_recent_videos_widget';
	public $query_args = array(
		'post_type' => 'emd_video',
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC'
	);
	public $filter = '';
	/**
	 * Instantiate entity widget class with params
	 *
	 * @since WPAS 4.0
	 */
	function utube_scase_recent_videos_widget() {
		$this->Emd_Widget(__('Recent Videos', 'utube-scase') , __('Videos', 'utube-scase') , __('The most recent videos', 'utube-scase'));
	}
	/**
	 * Returns widget layout
	 *
	 * @since WPAS 4.0
	 */
	public static function layout() {
		$layout = "<div class=\"thumbnail\"><a class=\"thumbnail-link\" href=\"" . get_permalink() . "\"><img src=\"https://img.youtube.com/vi/" . esc_html(rwmb_meta('emd_video_key')) . "/maxresdefault.jpg\" alt=\"" . get_the_title() . "\">" . get_the_title() . "</a></div>";
		return $layout;
	}
}
/**
 * Entity widget class extends Emd_Widget class
 *
 * @since WPAS 4.0
 */
class utube_scase_featured_videos_widget extends Emd_Widget {
	public $title;
	public $text_domain = 'utube-scase';
	public $class_label;
	public $class = 'emd_video';
	public $type = 'entity';
	public $has_pages = false;
	public $css_label = 'featured-videos';
	public $id = 'utube_scase_featured_videos_widget';
	public $query_args = array(
		'post_type' => 'emd_video',
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC'
	);
	public $filter = 'attr::emd_video_featured::is::1';
	/**
	 * Instantiate entity widget class with params
	 *
	 * @since WPAS 4.0
	 */
	function utube_scase_featured_videos_widget() {
		$this->Emd_Widget(__('Featured Videos', 'utube-scase') , __('Videos', 'utube-scase') , __('The most recent videos', 'utube-scase'));
	}
	/**
	 * Returns widget layout
	 *
	 * @since WPAS 4.0
	 */
	public static function layout() {
		$layout = "<div class=\"thumbnail\"><a class=\"thumbnail-link\" href=\"" . get_permalink() . "\"><img src=\"https://img.youtube.com/vi/" . esc_html(rwmb_meta('emd_video_key')) . "/maxresdefault.jpg\" alt=\"" . get_the_title() . "\">" . get_the_title() . "</a></div>";
		return $layout;
	}
}
$access_views = get_option('utube_scase_access_views', Array());
if (empty($access_views['widgets']) || (!empty($access_views['widgets']) && in_array('recent_videos', $access_views['widgets']) && current_user_can('view_recent_videos'))) {
	register_widget('utube_scase_recent_videos_widget');
}
if (empty($access_views['widgets']) || (!empty($access_views['widgets']) && in_array('featured_videos', $access_views['widgets']) && current_user_can('view_featured_videos'))) {
	register_widget('utube_scase_featured_videos_widget');
}
