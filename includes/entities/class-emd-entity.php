<?php
/**
 * Emd Entity
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Emd_Entity Class
 *
 * Base class for entities
 *
 * @since WPAS 4.0
 */
class Emd_Entity {
	protected $post_type;
	protected $textdomain;
	/**
	 * Check to show tabs/accordions in admin entity add/edit pages
	 * @since WPAS 4.0
	 *
	 * @return bool
	 *
	 */
	private function maybe_show_tabs() {
		$desired_screen = 'edit-' . $this->post_type;
		// Exit early on autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return false;
		}
		// Inline save?
		if (defined('DOING_AJAX') && DOING_AJAX && isset($_POST['screen']) && $desired_screen === $_POST['screen']) {
			return true;
		}
		if (!$screen = get_current_screen()) {
			global $pagenow;
			if ('post-new.php' === $pagenow || 'post.php' === $pagenow) {
				if (isset($_GET['post_type']) && $this->post_type === $_GET['post_type']) {
					return true;
				} elseif (isset($_GET['post']) && get_post_type($_GET['post']) === $this->post_type) {
					return true;
				} else if ('post' === $this->post_type) {
					return true;
				}
				return false;
			}
		}
		if (is_object($screen) && isset($screen->id)) {
			return $desired_screen === $screen->id;
		} else {
			return false;
		}
	}
	/**
	 * Include file for tabs/accordions in admin entity add/edit pages
	 * @since WPAS 4.0
	 *
	 * @return include file
	 *
	 */
	public function include_tabs_acc() {
		if (defined('DOING_AJAX') && DOING_AJAX) return;
		if ($this->maybe_show_tabs()) {
			$fname = str_replace("_", "-", $this->post_type);
			$inc_file = constant(strtoupper(str_replace("-", "_", $this->textdomain)) . "_PLUGIN_DIR") . '/includes/entities/' . $fname . '-tabs.php';
			if (file_exists($inc_file)) {
				require_once $inc_file;
			}
		}
	}
	/**
	 * Change title to post id or concat of unique keys
	 * @since WPAS 4.0
	 *
	 * @param int $post_id
	 * @param object $post
	 *
	 */
	public function change_title($post_id, $post) {
		if ($post->post_type == $this->post_type) {
			if (in_array($post->post_title, Array(
				'',
				'Auto Draft'
			))) {
				remove_action('save_post', array(
					$this,
					'change_title'
				) , 99, 2);
				wp_update_post(array(
					'ID' => $post_id,
					'post_title' => $post_id
				));
				add_action('save_post', array(
					$this,
					'change_title'
				) , 99, 2);
			} elseif (empty($_POST['form_name'])) {
				$new_title = $post->post_title;
				$app = str_replace("-", "_", $this->textdomain);
				$ent_list = get_option($app . '_ent_list');
				if (!empty($ent_list[$this->post_type]['unique_keys'])) {
					$uniq_keys = $ent_list[$this->post_type]['unique_keys'];
					$new_title = '';
					foreach ($uniq_keys as $mykey) {
						$tpart = emd_mb_meta($mykey, Array() , $post_id);
						if(!empty($tpart)){
							$new_title.= $tpart . " - ";
						}
					}
					$new_title = rtrim($new_title, ' - ');
				}
				if ($post->post_title == $post_id ||  ($post->post_title != $new_title && $new_title != '')) {
					remove_action('save_post', array(
						$this,
						'change_title'
					) , 99, 2);
					wp_update_post(array(
						'ID' => $post_id,
						'post_title' => $new_title
					));
					add_action('save_post', array(
						$this,
						'change_title'
					) , 99, 2);
				}
			}
		}
	}
	/**
	 * Update admin messages for specific entity
	 * @since WPAS 4.0
	 *
	 * @param array $messages
	 *
	 * @return array $messages
	 */
	public function updated_messages($messages) {
		global $post, $post_ID;
		$messages[$this->post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf(__('%s updated. <a href="%s">View %s</a>', 'emd-plugins') , $this->sing_label, esc_url(get_permalink($post_ID)) , $this->sing_label) ,
			2 => __('Custom field updated.', 'emd-plugins') ,
			3 => __('Custom field deleted.', 'emd-plugins') ,
			4 => sprintf(__('%s updated.', 'emd-plugins') , $this->sing_label) ,
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf(__('%s restored to revision from %s', 'emd-plugins') , $this->sing_label, wp_post_revision_title((int)$_GET['revision'], false)) : false,
			6 => sprintf(__('%s published. <a href="%s">View %s</a>', 'emd-plugins') , $this->sing_label, esc_url(get_permalink($post_ID)) , $this->sing_label) ,
			7 => sprintf(__('%s saved.', 'emd-plugins') , $this->sing_label) ,
			8 => sprintf(__('%s submitted. <a target="_blank" href="%s">Preview %s</a>', 'emd-plugins') , $this->sing_label, esc_url(add_query_arg('preview', 'true', get_permalink($post_ID))) , $this->sing_label) ,
			9 => sprintf(__('%s scheduled for: <strong>%s</strong>. <a target="_blank" href="%s">Preview %s</a>', 'emd-plugins') , $this->sing_label, date_i18n(__('M j, Y @ G:i','emd-plugins') , strtotime($post->post_date)) , esc_url(get_permalink($post_ID)) , $this->sing_label) ,
			10 => sprintf(__('%s draft updated. <a target="_blank" href="%s">Preview %s</a>', 'emd-plugins') , $this->sing_label, esc_url(add_query_arg('preview', 'true', get_permalink($post_ID))) , $this->sing_label) ,
		);
		return $messages;
	}
	/**
	 * Add operations button
	 * @since WPAS 4.0
	 *
	 *
	 */
	public function add_opt_button() {
		global $post_type;
		if ($post_type != $this->post_type) {
			return;
		}
		if (current_user_can('manage_operations_' . $this->post_type . "s")) {
?>
			<script type="text/javascript">
			jQuery(document).ready(function($){
					$('h2 a.add-new-h2').after('<a id="opt-<?php echo str_replace("_", "-", $this->post_type); ?>" class="add-new-h2" href="<?php echo admin_url('edit.php?post_type=' . $this->post_type . '&page=operations_' . $this->post_type); ?>" ><?php _e('Operations', 'emd-plugins'); ?></a>');
					$('li.opt_<?php echo $this->post_type; ?>').html('');
					});     
		</script>
		<?php
		}
	}
	/**
	 * Set initial taxonomy terms related to this entity
	 * @since WPAS 4.0
	 *
	 * @param array $set_tax_terms
	 * @param string $tax_name
	 *
	 */
	protected static function set_taxonomy_init($set_tax_terms, $tax_name) {
		foreach ($set_tax_terms as $my_tax_term) {
			$term_id_arr = term_exists($my_tax_term['slug'], $tax_name);
			$args = Array();
			if (!empty($my_tax_term['desc'])) {
				$args['description'] = $my_tax_term['desc'];
			}
			$args['slug'] = $my_tax_term['slug'];
			if (empty($term_id_arr)) {
				wp_insert_term($my_tax_term['name'], $tax_name, $args);
			} else {
				$args['name'] = $my_tax_term['name'];
				wp_update_term($term_id_arr['term_id'], $tax_name, $args);
			}
		}
		foreach ($set_tax_terms as $my_tax_term) {
			$args = Array();
			if (!empty($my_tax_term['parent'])) {
				$parent_term = term_exists($my_tax_term['parent'], $tax_name);
				if ($parent_term !== 0 && $parent_term !== null) {
					$args['parent'] = $parent_term['term_id'];
					$myterm = term_exists($my_tax_term['slug'], $tax_name);
					$term_id = $myterm['term_id'];
					wp_update_term($term_id, $tax_name, $args);
				}
			}
		}
		delete_option($tax_name . '_children');
	}
}
