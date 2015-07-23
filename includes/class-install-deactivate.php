<?php
/**
 * Install and Deactivate Plugin Functions
 * @package YT_SCASE_COM
 * @version 1.4.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
if (!class_exists('Yt_Scase_Com_Install_Deactivate')):
	/**
	 * Yt_Scase_Com_Install_Deactivate Class
	 * @since WPAS 4.0
	 */
	class Yt_Scase_Com_Install_Deactivate {
		private $option_name;
		/**
		 * Hooks for install and deactivation and create options
		 * @since WPAS 4.0
		 */
		public function __construct() {
			$this->option_name = 'yt_scase_com';
			$curr_version = get_option($this->option_name . '_version', 1);
			$new_version = constant(strtoupper($this->option_name) . '_VERSION');
			if (version_compare($curr_version, $new_version, '<')) {
				$this->set_options();
				update_option($this->option_name . '_version', $new_version);
			}
			register_activation_hook(YT_SCASE_COM_PLUGIN_FILE, array(
				$this,
				'install'
			));
			register_deactivation_hook(YT_SCASE_COM_PLUGIN_FILE, array(
				$this,
				'deactivate'
			));
			add_action('admin_init', array(
				$this,
				'setup_pages'
			));
			add_action('admin_notices', array(
				$this,
				'install_notice'
			));
			add_action('generate_rewrite_rules', 'emd_create_rewrite_rules');
			add_filter('query_vars', 'emd_query_vars');
			add_action('admin_init', array(
				$this,
				'register_settings'
			) , 0);
			add_filter('tiny_mce_before_init', array(
				$this,
				'tinymce_fix'
			));
		}
		/**
		 * Runs on plugin install to setup custom post types and taxonomies
		 * flushing rewrite rules, populates settings and options
		 * creates roles and assign capabilities
		 * @since WPAS 4.0
		 *
		 */
		public function install() {
			Emd_Video::register();
			flush_rewrite_rules();
			$this->set_roles_caps();
			$this->set_options();
		}
		/**
		 * Runs on plugin deactivate to remove options, caps and roles
		 * flushing rewrite rules
		 * @since WPAS 4.0
		 *
		 */
		public function deactivate() {
			flush_rewrite_rules();
			$this->remove_caps_roles();
			$this->reset_options();
		}
		/**
		 * Register notification and/or license settings
		 * @since WPAS 4.0
		 *
		 */
		public function register_settings() {
			emd_glob_register_settings($this->option_name);
		}
		/**
		 * Sets caps and roles
		 *
		 * @since WPAS 4.0
		 *
		 */
		public function set_roles_caps() {
			global $wp_roles;
			if (class_exists('WP_Roles')) {
				if (!isset($wp_roles)) {
					$wp_roles = new WP_Roles();
				}
			}
			if (is_object($wp_roles)) {
				$this->set_reset_caps($wp_roles, 'add');
			}
		}
		/**
		 * Removes caps and roles
		 *
		 * @since WPAS 4.0
		 *
		 */
		public function remove_caps_roles() {
			global $wp_roles;
			if (class_exists('WP_Roles')) {
				if (!isset($wp_roles)) {
					$wp_roles = new WP_Roles();
				}
			}
			if (is_object($wp_roles)) {
				$this->set_reset_caps($wp_roles, 'remove');
			}
		}
		/**
		 * Set , reset capabilities
		 *
		 * @since WPAS 4.0
		 * @param object $wp_roles
		 * @param string $type
		 *
		 */
		public function set_reset_caps($wp_roles, $type) {
			$caps['enable'] = Array(
				'edit_emd_videos' => Array(
					'administrator'
				) ,
				'view_yt_scase_com_dashboard' => Array(
					'administrator'
				) ,
			);
			foreach ($caps as $stat => $role_caps) {
				foreach ($role_caps as $mycap => $roles) {
					foreach ($roles as $myrole) {
						if (($type == 'add' && $stat == 'enable') || ($stat == 'disable' && $type == 'remove')) {
							$wp_roles->add_cap($myrole, $mycap);
						} else if (($type == 'remove' && $stat == 'enable') || ($type == 'add' && $stat == 'disable')) {
							$wp_roles->remove_cap($myrole, $mycap);
						}
					}
				}
			}
		}
		/**
		 * Set app specific options
		 *
		 * @since WPAS 4.0
		 *
		 */
		private function set_options() {
			update_option($this->option_name . '_setup_pages', 1);
			$ent_list = Array(
				'emd_video' => Array(
					'label' => __('Videos', 'yt-scase-com') ,
					'sortable' => 0,
					'unique_keys' => Array(
						'emd_video_key'
					) ,
					'req_blt' => Array(
						'blt_title' => Array(
							'msg' => __('Title', 'yt-scase-com')
						) ,
					) ,
				) ,
			);
			update_option($this->option_name . '_ent_list', $ent_list);
			$shc_list['app'] = 'Youtube Showcase';
			$shc_list['integrations']['video_gallery'] = Array(
				'type' => 'integration',
				'app_dash' => 0,
				'page_title' => __('Video Gallery', 'yt-scase-com')
			);
			$shc_list['shcs']['video_grid'] = Array(
				"class_name" => "emd_video",
				"type" => "std",
				'page_title' => __('Video Grid Gallery', 'yt-scase-com') ,
			);
			if (!empty($shc_list)) {
				update_option($this->option_name . '_shc_list', $shc_list);
			}
			$attr_list['emd_video']['emd_video_key'] = Array(
				'visible' => 1,
				'label' => __('Video Key', 'yt-scase-com') ,
				'display_type' => 'text',
				'required' => 1,
				'srequired' => 0,
				'filterable' => 1,
				'list_visible' => 1,
				'desc' => __('<p>The unique 11 digit alphanumeric video key found on the YouTube video. For example; in https://www.youtube.com/watch?v=uVgWZd7oGOk. uVgWZd7oGOk is the video id.</p>', 'yt-scase-com') ,
				'type' => 'char',
				'minlength' => 11,
				'maxlength' => 11,
				'uniqueAttr' => true,
			);
			$attr_list['emd_video']['emd_video_featured'] = Array(
				'visible' => 1,
				'label' => __('Featured', 'yt-scase-com') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 1,
				'list_visible' => 1,
				'desc' => __('Adds the video to featured video list.', 'yt-scase-com') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_thumbnail_resolution'] = Array(
				'visible' => 1,
				'label' => __('Video Image Resolution', 'yt-scase-com') ,
				'display_type' => 'select',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 0,
				'list_visible' => 1,
				'desc' => __('<p>Sets the resolution of video thumbnail image. The image size for each option;<br />
<strong>Medium</strong> - 320 x 180, <strong>High</strong> - 480x360, <strong>Standard</strong> - 640 x 480, <strong>Max</strong> -1280 x 720</p>', 'yt-scase-com') ,
				'type' => 'char',
				'options' => array(
					'' => __('Please Select', 'yt-scase-com') ,
					'sd' => __('Standard', 'yt-scase-com') ,
					'mq' => __('Medium', 'yt-scase-com') ,
					'hq' => __('High', 'yt-scase-com') ,
					'maxres' => __('Max', 'yt-scase-com')
				) ,
				'std' => 'mq',
			);
			$attr_list['emd_video']['emd_video_autoplay'] = Array(
				'visible' => 1,
				'label' => __('Video Autoplay', 'yt-scase-com') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'srequired' => 0,
				'filterable' => 0,
				'list_visible' => 1,
				'desc' => __('When set the player starts video automatically. It may not work in all devices due to vendor preferences.', 'yt-scase-com') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			if (!empty($attr_list)) {
				update_option($this->option_name . '_attr_list', $attr_list);
			}
			$glob_list['res_enable_thumbs'] = Array(
				'label' => __('Enable Thumbnails For Small Screens', 'yt-scase-com') ,
				'type' => 'checkbox',
				'desc' => 'Enables or disables thumbnail navigation in video gallery for screens less than 769px.',
				'values' => '',
				'dflt' => '',
				'required' => 0,
			);
			if (!empty($glob_list)) {
				update_option($this->option_name . '_glob_list', $glob_list);
			}
			if (!empty($glob_forms_list)) {
				update_option($this->option_name . '_glob_forms_list', $glob_forms_list);
			}
			$tax_list['emd_video']['category'] = Array(
				'label' => __('Categories', 'yt-scase-com') ,
				'default' => '',
				'type' => 'builtin',
				'hier' => 1,
				'sortable' => 0,
				'required' => 0,
				'srequired' => 0
			);
			$tax_list['emd_video']['post_tag'] = Array(
				'label' => __('Tags', 'yt-scase-com') ,
				'default' => '',
				'type' => 'builtin',
				'hier' => 1,
				'sortable' => 0,
				'required' => 0,
				'srequired' => 0
			);
			if (!empty($tax_list)) {
				update_option($this->option_name . '_tax_list', $tax_list);
			}
			if (!empty($rel_list)) {
				update_option($this->option_name . '_rel_list', $rel_list);
			}
			$emd_activated_plugins = get_option('emd_activated_plugins');
			if (!$emd_activated_plugins) {
				update_option('emd_activated_plugins', Array(
					'yt-scase-com'
				));
			} elseif (!in_array('yt-scase-com', $emd_activated_plugins)) {
				array_push($emd_activated_plugins, 'yt-scase-com');
				update_option('emd_activated_plugins', $emd_activated_plugins);
			}
			//conf parameters for incoming email
			//conf parameters for inline entity
			//action to configure different extension conf parameters for this plugin
			do_action('emd_extension_set_conf');
		}
		/**
		 * Reset app specific options
		 *
		 * @since WPAS 4.0
		 *
		 */
		private function reset_options() {
			delete_option($this->option_name . '_ent_list');
			delete_option($this->option_name . '_shc_list');
			delete_option($this->option_name . '_attr_list');
			delete_option($this->option_name . '_tax_list');
			delete_option($this->option_name . '_rel_list');
			delete_option($this->option_name . '_adm_notice1');
			delete_option($this->option_name . '_adm_notice2');
			delete_option($this->option_name . '_setup_pages');
			$emd_activated_plugins = get_option('emd_activated_plugins');
			if (!empty($emd_activated_plugins)) {
				$emd_activated_plugins = array_diff($emd_activated_plugins, Array(
					'yt-scase-com'
				));
				update_option('emd_activated_plugins', $emd_activated_plugins);
			}
		}
		/**
		 * Show install notices
		 *
		 * @since WPAS 4.0
		 *
		 * @return html
		 */
		public function install_notice() {
			if (isset($_GET[$this->option_name . '_adm_notice1'])) {
				update_option($this->option_name . '_adm_notice1', true);
			}
			if (current_user_can('manage_options') && get_option($this->option_name . '_adm_notice1') != 1) {
?>
<div class="updated">
<?php
				printf('<p><a href="%1s" target="_blank"> %2$s </a>%3$s<a style="float:right;" href="%4$s"><span class="dashicons dashicons-dismiss" style="font-size:15px;"></span>%5$s</a></p>', 'https://docs.emdplugins.com/docs/youtube-showcase-community-documentation/?pk_campaign=youtube-showcase&pk_source=plugin&pk_medium=link&pk_content=notice', __('New To Youtube Showcase? Review the documentation!', 'wpas') , __('&#187;', 'wpas') , esc_url(add_query_arg($this->option_name . '_adm_notice1', true)) , __('Dismiss', 'wpas'));
?>
</div>
<?php
			}
			if (isset($_GET[$this->option_name . '_adm_notice2'])) {
				update_option($this->option_name . '_adm_notice2', true);
			}
			if (current_user_can('manage_options') && get_option($this->option_name . '_adm_notice2') != 1) {
?>
<div class="updated">
<?php
				printf('<p><a href="%1s" target="_blank"> %2$s </a>%3$s<a style="float:right;" href="%4$s"><span class="dashicons dashicons-dismiss" style="font-size:15px;"></span>%5$s</a></p>', 'https://emdplugins.com/plugins/youtube-showcase-professional/?pk_campaign=youtube-showcase&pk_source=plugin&pk_medium=link&pk_content=notice', __('Upgrade to Professional Version Now!', 'wpas') , __('&#187;', 'wpas') , esc_url(add_query_arg($this->option_name . '_adm_notice2', true)) , __('Dismiss', 'wpas'));
?>
</div>
<?php
			}
			if (current_user_can('manage_options') && get_option($this->option_name . '_setup_pages') == 1) {
				echo "<div id=\"message\" class=\"updated\"><p><strong>" . __('Welcome to Youtube Showcase', 'yt-scase-com') . "</strong></p>
           <p class=\"submit\"><a href=\"" . add_query_arg('setup_yt_scase_com_pages', 'true', admin_url('index.php')) . "\" class=\"button-primary\">" . __('Setup Youtube Showcase Pages', 'yt-scase-com') . "</a> <a class=\"skip button-primary\" href=\"" . add_query_arg('skip_setup_yt_scase_com_pages', 'true', admin_url('index.php')) . "\">" . __('Skip setup', 'yt-scase-com') . "</a></p>
         </div>";
			}
		}
		/**
		 * Setup pages for components and redirect to dashboard
		 *
		 * @since WPAS 4.0
		 *
		 */
		public function setup_pages() {
			if (!is_admin()) {
				return;
			}
			global $wpdb;
			if (!empty($_GET['setup_' . $this->option_name . '_pages'])) {
				$shc_list = get_option($this->option_name . '_shc_list');
				$types = Array(
					'forms',
					'charts',
					'shcs',
					'datagrids',
					'integrations'
				);
				foreach ($types as $shc_type) {
					if (!empty($shc_list[$shc_type])) {
						foreach ($shc_list[$shc_type] as $keyshc => $myshc) {
							if (isset($myshc['page_title'])) {
								$pages[$keyshc] = $myshc;
							}
						}
					}
				}
				foreach ($pages as $key => $page) {
					$found = "";
					$page_content = "[" . $key . "]";
					$found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_type='page' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%"));
					if ($found != "") {
						continue;
					}
					$page_data = array(
						'post_status' => 'publish',
						'post_type' => 'page',
						'post_author' => get_current_user_id() ,
						'post_title' => $page['page_title'],
						'post_content' => $page_content,
						'comment_status' => 'closed'
					);
					$page_id = wp_insert_post($page_data);
				}
				delete_option($this->option_name . '_setup_pages');
				wp_redirect(admin_url('index.php?yt-scase-com-installed=true'));
				exit;
			}
			if (!empty($_GET['skip_setup_' . $this->option_name . '_pages'])) {
				delete_option($this->option_name . '_setup_pages');
				wp_redirect(admin_url('index.php?'));
				exit;
			}
		}
		public function tinymce_fix($init) {
			$init['wpautop'] = false;
			return $init;
		}
	}
endif;
return new Yt_Scase_Com_Install_Deactivate();
