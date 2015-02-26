<?php
/**
 * Entity Class
 *
 * @package YT_SCASE_COM
 * @version 1.1.1
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Emd_Video Class
 * @since WPAS 4.0
 */
class Emd_Video extends Emd_Entity {
	protected $post_type = 'emd_video';
	protected $textdomain = 'yt-scase-com';
	protected $sing_label;
	protected $plural_label;
	private $boxes = Array();
	/**
	 * Initialize entity class
	 *
	 * @since WPAS 4.0
	 *
	 */
	public function __construct() {
		add_action('init', array(
			$this,
			'set_filters'
		));
		add_filter('post_updated_messages', array(
			$this,
			'updated_messages'
		));
		add_action('manage_emd_video_posts_custom_column', array(
			$this,
			'custom_columns'
		) , 10, 2);
		add_filter('manage_emd_video_posts_columns', array(
			$this,
			'column_headers'
		));
	}
	/**
	 * Get column header list in admin list pages
	 * @since WPAS 4.0
	 *
	 * @param array $columns
	 *
	 * @return array $columns
	 */
	public function column_headers($columns) {
		foreach ($this->boxes as $mybox) {
			foreach ($mybox['fields'] as $fkey => $mybox_field) {
				if (!in_array($fkey, Array(
					'wpas_form_name',
					'wpas_form_submitted_by',
					'wpas_form_submitted_ip'
				)) && !in_array($mybox_field['type'], Array(
					'textarea',
					'wysiwyg'
				))) {
					$columns[$fkey] = $mybox_field['name'];
				}
			}
		}
		$args = array(
			'_builtin' => false,
			'object_type' => Array(
				$this->post_type
			)
		);
		$taxonomies = get_taxonomies($args, 'objects');
		if (!empty($taxonomies)) {
			foreach ($taxonomies as $taxonomy) {
				$columns[$taxonomy->name] = $taxonomy->label;
			}
		}
		return $columns;
	}
	/**
	 * Get custom column values in admin list pages
	 * @since WPAS 4.0
	 *
	 * @param int $column_id
	 * @param int $post_id
	 *
	 * @return string $value
	 */
	public function custom_columns($column_id, $post_id) {
		if (taxonomy_exists($column_id) == true) {
			$terms = get_the_terms($post_id, $column_id);
			$ret = array();
			if (!empty($terms)) {
				foreach ($terms as $term) {
					$url = add_query_arg(array(
						'post_type' => $this->post_type,
						'term' => $term->slug,
						'taxonomy' => $column_id
					) , admin_url('edit.php'));
					$ret[] = sprintf('<a href="%s">%s</a>', $url, $term->name);
				}
			}
			echo implode(', ', $ret);
			return;
		}
		$value = get_post_meta($post_id, $column_id, true);
		$type = "";
		foreach ($this->boxes as $mybox) {
			foreach ($mybox['fields'] as $fkey => $mybox_field) {
				if ($fkey == $column_id) {
					$type = $mybox_field['type'];
					break;
				}
			}
		}
		switch ($type) {
			case 'plupload_image':
			case 'image':
			case 'thickbox_image':
				$image_list = emd_mb_meta($column_id, 'type=image');
				if (!empty($image_list)) {
					$value = "";
					foreach ($image_list as $myimage) {
						$value.= "<img src='" . $myimage['url'] . "' >";
					}
				}
			break;
			case 'user':
			case 'user-adv':
				$user_id = emd_mb_meta($column_id);
				if (!empty($user_id)) {
					$user_info = get_userdata($user_id);
					$value = $user_info->display_name;
				}
			break;
			case 'file':
				$file_list = emd_mb_meta($column_id, 'type=file');
				if (!empty($file_list)) {
					$value = "";
					foreach ($file_list as $myfile) {
						$fsrc = wp_mime_type_icon($myfile['ID']);
						$value.= "<a href='" . $myfile['url'] . "' target='_blank'><img src='" . $fsrc . "' title='" . $myfile['name'] . "' width='20' /></a>";
					}
				}
			break;
			case 'checkbox_list':
				$checkbox_list = emd_mb_meta($column_id, 'type=checkbox_list');
				if (!empty($checkbox_list)) {
					$value = implode(', ', $checkbox_list);
				}
			break;
			case 'select':
			case 'select_advanced':
				$select_list = get_post_meta($post_id, $column_id, false);
				if (!empty($select_list)) {
					$value = implode(', ', $select_list);
				}
			break;
		}
		echo $value;
	}
	/**
	 * Register post type and taxonomies and set initial values for taxs
	 *
	 * @since WPAS 4.0
	 *
	 */
	public static function register() {
		$labels = array(
			'name' => __('Videos', 'yt-scase-com') ,
			'singular_name' => __('Video', 'yt-scase-com') ,
			'add_new' => __('Add New', 'yt-scase-com') ,
			'add_new_item' => __('Add New Video', 'yt-scase-com') ,
			'edit_item' => __('Edit Video', 'yt-scase-com') ,
			'new_item' => __('New Video', 'yt-scase-com') ,
			'all_items' => __('All Videos', 'yt-scase-com') ,
			'view_item' => __('View Video', 'yt-scase-com') ,
			'search_items' => __('Search Videos', 'yt-scase-com') ,
			'not_found' => __('No Videos Found', 'yt-scase-com') ,
			'not_found_in_trash' => __('No Videos Found In Trash', 'yt-scase-com') ,
			'menu_name' => __('Videos', 'yt-scase-com') ,
		);
		register_post_type('emd_video', array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'description' => __('Videos are YouTube videos identified by Video ID.', 'yt-scase-com') ,
			'show_in_menu' => true,
			'menu_position' => 6,
			'has_archive' => true,
			'exclude_from_search' => false,
			'rewrite' => array(
				'slug' => 'videos'
			) ,
			'can_export' => true,
			'hierarchical' => false,
			'menu_icon' => 'dashicons-format-video',
			'map_meta_cap' => 'false',
			'taxonomies' => array(
				'category',
				'post_tag'
			) ,
			'capability_type' => 'post',
			'supports' => array(
				'title',
				'editor',
				'excerpt',
				'comments'
			)
		));
	}
	/**
	 * Set metabox fields,labels,filters, comments, relationships if exists
	 *
	 * @since WPAS 4.0
	 *
	 */
	public function set_filters() {
		$search_args = array();
		$filter_args = array();
		$this->sing_label = __('Video', 'yt-scase-com');
		$this->plural_label = __('Videos', 'yt-scase-com');
		$this->boxes[] = array(
			'id' => 'emd_video_info_emd_video_0',
			'title' => __('Video Info', 'yt-scase-com') ,
			'pages' => array(
				'emd_video'
			) ,
			'context' => 'normal',
			'fields' => array(
				'emd_video_key' => array(
					'name' => __('Video Key', 'yt-scase-com') ,
					'id' => 'emd_video_key',
					'type' => 'text',
					'multiple' => false,
					'desc' => __('<p>The unique 11 digit alphanumeric video key found on the YouTube video. For example; in https://www.youtube.com/watch?v=uVgWZd7oGOk. uVgWZd7oGOk is the video id.</p>', 'yt-scase-com') ,
					'class' => 'emd_video_key',
				) ,
				'emd_video_featured' => array(
					'name' => __('Featured', 'yt-scase-com') ,
					'id' => 'emd_video_featured',
					'type' => 'checkbox',
					'multiple' => false,
					'desc' => __('Adds the video to featured video list.', 'yt-scase-com') ,
					'class' => 'emd_video_featured',
				) ,
			) ,
			'validation' => array(
				'onfocusout' => false,
				'onkeyup' => false,
				'onclick' => false,
				'rules' => array(
					'emd_video_key' => array(
						'required' => true,
						'minlength' => 11,
						'maxlength' => 11,
						'uniqueAttr' => true,
					) ,
					'emd_video_featured' => array(
						'required' => false,
					) ,
				) ,
			)
		);
		if (!post_type_exists($this->post_type) || in_array($this->post_type, Array(
			'post',
			'page'
		))) {
			self::register();
		}
		global $pagenow;
		if ('post-new.php' === $pagenow || 'post.php' === $pagenow) {
			if (class_exists('EMD_Meta_Box') && is_array($this->boxes)) {
				foreach ($this->boxes as $meta_box) {
					new EMD_Meta_Box($meta_box);
				}
			}
		}
	}
	/**
	 * Change content for created frontend views
	 * @since WPAS 4.0
	 * @param string $content
	 *
	 * @return string $content
	 */
	public function change_content($content) {
		global $post;
		$layout = "";
		if (get_post_type() == $this->post_type && is_single()) {
			ob_start();
			emd_get_template_part($this->textdomain, 'single', 'emd-video');
			$layout = ob_get_clean();
		}
		if ($layout != "") {
			$content = $layout;
		}
		return $content;
	}
}
new Emd_Video;
