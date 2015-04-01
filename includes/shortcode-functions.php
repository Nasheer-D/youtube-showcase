<?php
/**
 * Shortcode Functions
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       1.0
 */
// Exit if accessed directly
if (!defined('ABSPATH')) exit;
/**
 * Gets shortcode layout
 *
 * @since WPAS 4.0
 * @param array $atts shortcode attributes
 * @param array $args query args
 * @param array $args_default default query args
 * @param array $fields
 * @return string layout html
 */
function emd_shc_get_layout_list($atts, $args, $args_default, $fields) {
	global $wp_rewrite;
	//fields -- app , class, shc , form, has_pages , pageno, theme
	if ($fields['has_pages'] && empty($args)) {
		if (is_front_page() && get_query_var('pagename') == get_post(get_query_var('page_id'))->post_name) {
			$fields['pageno'] = get_query_var('paged');
		}
		elseif($wp_rewrite->permalink_structure == '/%postname%/' || $wp_rewrite->permalink_structure == ''){
			if (get_query_var('pageno')) $fields['pageno'] = get_query_var('pageno');
		}
		else {
			$fields['pageno'] = get_query_var('paged');
		}
		if($fields['pageno'] == 0){
			$fields['pageno'] = 1;	
		}	
	}
	if (empty($args)) {
		if (is_array($atts) && !empty($atts['filter'])) {
			$emd_query = new Emd_Query($fields['class'], $fields['app']);
			$emd_query->args_filter($atts['filter']);
			$args = $emd_query->args;
		}
		$args['post_type'] = $fields['class'];
	}
	if ($fields['has_pages']) {
		$args_default['paged'] = $fields['pageno'];
	} else {
		$args_default['no_found_rows'] = true;
	}
	$args = array_merge($args, $args_default);
	if ($fields['form'] != '') {
		$_SESSION[$fields['form'] . '_args'] = $args;
	}
	$has_limit_by = get_option($fields['app'] . "_has_limitby_cap");
	if (isset($has_limit_by) && $has_limit_by == 1) {
		$pids = apply_filters('emd_limit_by', Array() , $fields['app'], $args['post_type']);
		if(!empty($pids)){
			$args['post__in'] = $pids;
		}
	}
	$myshc_query = new WP_Query($args);
	if ($myshc_query->have_posts()) {
		ob_start();
		if (empty($fields['form']) && $fields['theme'] == 'bs') {
			emd_get_template_part($fields['app'], 'shc', "menu");
		} ?>
		<div id='<?php echo esc_attr($fields['shc']) . "_" . esc_attr($fields['class']) . "-view"; ?>' class='emd-view-results'>
		<input type='hidden' id='emd_entity' name='emd_entity' value='<?php echo esc_attr($fields['class']); ?>'>
		<input type='hidden' id='emd_view' name='emd_view' value='<?php echo esc_attr($fields['shc']); ?>'>
		<input type='hidden' id='emd_app' name='emd_app' value='<?php echo esc_attr($fields['app']); ?>'>
		<?php
		emd_get_template_part($fields['app'], 'shc', str_replace('_', '-', $fields['shc']) . "-header");
		$res_posts = Array();
		$count_var = $fields['shc'] . "_count";
		global $$count_var;
		$$count_var = 0;
		while ($myshc_query->have_posts()) {
			$myshc_query->the_post();
			$in_post_id = get_the_ID();
			if (!in_array($in_post_id, $res_posts)) {
				$res_posts[] = $in_post_id;
				emd_get_template_part($fields['app'], 'shc', str_replace('_', '-', $fields['shc']) . "-content");
				$$count_var++;
			}
		}
		wp_reset_postdata();
		emd_get_template_part($fields['app'], 'shc', str_replace('_', '-', $fields['shc']) . "-footer");
		if ($fields['has_pages'] && $myshc_query->max_num_pages > 1) {
			global $wp_rewrite;
			if ($wp_rewrite->using_permalinks()) {
				if (is_front_page()) {
					$base = '/' . get_post(get_query_var('page_id'))->post_name . '/page/%#%/';
				}
				elseif($wp_rewrite->permalink_structure == '/%postname%/'){
					$base = '/' . get_query_var('pagename') . '/pageno/%#%/';
				}
				else {
					$base = '/' . get_query_var('pagename') . '/page/%#%/';
				}
			} else {
				$base = '/?page_id=' . get_query_var('page_id') . '&pageno=%#%';
			}
			$paging = paginate_links(array(
				'total' => $myshc_query->max_num_pages,
				'current' => $fields['pageno'],
				'base' => $base,
				'format' => '%#%',
				'type' => 'array',
				'add_args' => true,
			));
			$paging_html = emd_shc_get_pagination($fields['theme'], $paging, $fields['pageno'], $fields['pgn_class']); ?>
			<div class='pagination-bar'>
			<?php echo $paging_html; ?>
			</div>
		<?php
		} ?>
		</div>
		<?php
		$layout = ob_get_clean();
		return $layout;
	}
	return '';
}
/**
 * Creates pagination html
 *
 * @since WPAS 4.0
 * @param string $type theme type
 * @param array $paging
 * @param int $pageno
 * @return string paging html
 */
function emd_shc_get_pagination($type, $paging, $pageno, $pgn_class) {
	$paging_html = "";
	if ($type == 'bs') {
		$paging_html = "<ul class='pagination " . $pgn_class . "'>";
		foreach ($paging as $key_paging => $my_paging) {
			$paging_html.= "<li";
			if(preg_match('/current/',$my_paging)){
				$paging_html.= " class='active'";
			}
			$paging_html.= ">" . $my_paging . "</li>";
		}
		$paging_html.= "</ul>";
	} elseif ($type == 'jui' || $type == 'na') {
		$paging_html = "<div class='nav-pages " . $pgn_class . "'>";
		foreach ($paging as $key_paging => $my_paging) {
			$paging_html.= "<div class='nav-item ui-state-default ui-corner-all";
			if(preg_match('/current/',$my_paging)){
				$paging_html.= " ui-state-highlight";
			}
			$paging_html.= "'>" . $my_paging . "</div>";
		}
		$paging_html.= "</div>";
	}
	return $paging_html;
}
/**
 * Add query var pageno
 *
 * @since WPAS 4.0
 * @param array $vars query vars
 * @return array $vars query vars
 */
function emd_query_vars($vars) {
	$vars[] = "pageno";
	return $vars;
}
/**
 * Create rewrite rules for pageno
 *
 * @since WPAS 4.0
 * @return wp_rewrite rules
 */
function emd_create_rewrite_rules() {
	global $wp_rewrite;
	$rewrite_tag = '%pageno%';
	$wp_rewrite->add_rewrite_tag($rewrite_tag, '(.+?)', 'pageno=');
	$rewrite_keywords_structure = $wp_rewrite->root . "%pagename%/%pageno%/$rewrite_tag/";
	$new_rule = $wp_rewrite->generate_rewrite_rules($rewrite_keywords_structure);
	$wp_rewrite->rules = $wp_rewrite->rules + $new_rule;
	return $wp_rewrite->rules;
}
