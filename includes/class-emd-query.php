<?php
/**
 * Emd Query
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       1.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Emd_Query Class
 *
 * Setup meta , tax and relationship query args for wp query
 *
 * @since WPAS 4.0
 */
class Emd_Query {
	var $args = Array();
	var $rel_args = Array();
	var $rel_posts = Array();
	var $entity = "";
	var $app = "";
	var $has_rel = 0;
	/**
	 * Instantiate emd query class
	 * Set entity and app names
	 * @since WPAS 4.0
	 *
	 * @param string $entity
	 * @param string $myapp
	 *
	 */
	public function __construct($entity, $myapp) {
		$this->entity = $entity;
		$this->app = $myapp;
	}
	/**
	 * Get filter list and create wp query args
	 * @since WPAS 4.0
	 *
	 * @param string $filter
	 *
	 */
	public function args_filter($filter) {
		$filter_list = explode(";", $filter);
		foreach ($filter_list as $myfilter) {
			if (!empty($myfilter)) {
				$field_list = explode("::", $myfilter);
				if (count($field_list) == 4) {
					switch ($field_list[0]) {
						case 'attr':
							$this->args['meta_query'][] = $this->get_meta_query($field_list);
						break;
						case 'tax':
							$this->args['tax_query'][] = $this->get_tax_query($field_list);
						break;
						case 'rel':
							$this->has_rel = 1;
							$this->get_rel_query($field_list);
						break;
					}
				}
			}
		}
		if (isset($this->args['meta_query']) && count($this->args['meta_query']) > 1) {
			$this->args['meta_query']['relation'] = 'AND';
		}
		if (isset($this->args['tax_query']) && count($this->args['tax_query']) > 1) {
			$this->args['tax_query']['relation'] = 'AND';
		}
		if ($this->has_rel == 1) {
			if (empty($this->args['post__in'])) {
				$this->args['post__in'] = Array(
					0
				);
			}
		}
	}
	/**
	 * Get fields list and create meta query
	 * @since WPAS 4.0
	 *
	 * @param array $field_list
	 *
	 * @return array $meta_query
	 */
	public function get_meta_query($field_list) {
		$ent_attrs = get_option($this->app . '_attr_list');
		$type = "char";
		$value = $field_list[3];
		if (isset($ent_attrs[$this->entity][$field_list[1]]['type'])) {
			$type = $ent_attrs[$this->entity][$field_list[1]]['type'];
		}
		if (in_array($type, Array(
			'date',
			'time',
			'datetime'
		))) {
			$value = emd_translate_date_format($ent_attrs[$this->entity][$field_list[1]], $field_list[3]);
		}
		$compare = emd_get_meta_operator($field_list[2]);
		if (is_array($value)) {
			$compare = "IN";
		}
		$meta_query = array(
			'key' => $field_list[1],
			'value' => $value,
			'compare' => $compare,
			'type' => $type,
		);
		return $meta_query;
	}
	/**
	 * Get fields list and create tax query
	 * @since WPAS 4.0
	 *
	 * @param array $field_list
	 *
	 * @return array $tax_query
	 */
	public function get_tax_query($field_list) {
		$tax_query = array(
			'taxonomy' => $field_list[1],
			'field' => 'slug',
			'terms' => explode(',', $field_list[3]) ,
			'operator' => 'IN',
		);
		return $tax_query;
	}
	/**
	 * Get fields list and create post_in arg for query
	 * @since WPAS 4.0
	 *
	 * @param array $field_list
	 *
	 * @return array $args['post__in']
	 */
	public function get_rel_query($field_list) {
		if (!isset($this->args['post__in']) || (isset($this->args['post__in']) && !empty($this->args['post__in']))) {
			$rel_query = "";
			$this->rel_args['connected_type'] = $field_list[1];
			$this->rel_args['connected_items'] = explode(',', $field_list[3]);
			if (!empty($this->rel_posts)) {
				$this->rel_args['post__in'] = $this->rel_posts;
			}
			$this->rel_args['post_type'] = $this->entity;
			$this->rel_args['posts_per_page'] = '-1';
			$rel_query = new WP_Query($this->rel_args);
			$this->rel_posts = Array();
			if ($rel_query->have_posts()) {
				while ($rel_query->have_posts()) {
					$rel_query->the_post();
					$in_post_id = get_the_ID();
					if (!in_array($in_post_id, $this->rel_posts)) {
						$this->rel_posts[] = get_the_ID();
					}
				}
			}
			wp_reset_query();
			$this->args['post__in'] = $this->rel_posts;
		}
	}
}
