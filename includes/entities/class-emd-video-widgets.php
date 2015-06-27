<?php
/**
 * Entity Widget Classes
 *
 * @package YT_SCASE_COM
 * @version 1.3.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Entity widget class extends Emd_Widget class
 *
 * @since WPAS 4.0
 */
class yt_scase_com_recent_videos_widget extends Emd_Widget {
	public $title;
	public $text_domain = 'yt-scase-com';
	public $class_label;
	public $class = 'emd_video';
	public $type = 'entity';
	public $has_pages = false;
	public $css_label = 'recent-videos';
	public $id = 'yt_scase_com_recent_videos_widget';
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
	function yt_scase_com_recent_videos_widget() {
		$this->Emd_Widget(__('Recent Videos', 'yt-scase-com') , __('Videos', 'yt-scase-com') , __('The most recent videos', 'yt-scase-com'));
	}
	/**
	 * Returns widget layout
	 *
	 * @since WPAS 4.0
	 */
	public static function layout() {
		global $post;
		$layout = "<a href=\"" . get_permalink() . "\" title=\"" . get_the_title() . "\"><img style=\"width:320px;height:180px;padding:5px\" src=\"https://img.youtube.com/vi/" . esc_html(emd_mb_meta('emd_video_key')) . "/" . esc_html(emd_mb_meta('emd_video_thumbnail_resolution')) . "default.jpg\" alt=\"" . get_the_title() . "\"></a>";
		return $layout;
	}
}
/**
 * Entity widget class extends Emd_Widget class
 *
 * @since WPAS 4.0
 */
class yt_scase_com_featured_videos_widget extends Emd_Widget {
	public $title;
	public $text_domain = 'yt-scase-com';
	public $class_label;
	public $class = 'emd_video';
	public $type = 'entity';
	public $has_pages = false;
	public $css_label = 'featured-videos';
	public $id = 'yt_scase_com_featured_videos_widget';
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
	function yt_scase_com_featured_videos_widget() {
		$this->Emd_Widget(__('Featured Videos', 'yt-scase-com') , __('Videos', 'yt-scase-com') , __('The most recent videos', 'yt-scase-com'));
	}
	/**
	 * Returns widget layout
	 *
	 * @since WPAS 4.0
	 */
	public static function layout() {
		global $post;
		$layout = "<a title=\"" . get_the_title() . "\" href=\"" . get_permalink() . "\"><img style=\"width:320px;height:180px;padding:5px\" src=\"https://img.youtube.com/vi/" . esc_html(emd_mb_meta('emd_video_key')) . "/" . esc_html(emd_mb_meta('emd_video_thumbnail_resolution')) . "default.jpg\" alt=\"" . get_the_title() . "\"></a>";
		return $layout;
	}
}
$access_views = get_option('yt_scase_com_access_views', Array());
if (empty($access_views['widgets']) || (!empty($access_views['widgets']) && in_array('recent_videos', $access_views['widgets']) && current_user_can('view_recent_videos'))) {
	register_widget('yt_scase_com_recent_videos_widget');
}
if (empty($access_views['widgets']) || (!empty($access_views['widgets']) && in_array('featured_videos', $access_views['widgets']) && current_user_can('view_featured_videos'))) {
	register_widget('yt_scase_com_featured_videos_widget');
}
