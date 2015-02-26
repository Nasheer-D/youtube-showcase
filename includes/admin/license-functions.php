<?php
/**
 * License Functions
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       WPAS 4.2
 */
if (!defined('ABSPATH')) exit;

add_action('emd_show_license_page','emd_show_license_page',1);
/**
 * Show settings page for licenses
 *
 * @param string $app
 * @since WPAS 4.2
 *
 * @return html page content
 */

function emd_show_license_page($app){
	//display app license and all extension licenses	
	global $title;
	$settings = get_option('emd_license_settings');
	?>
		<div class="wrap">
		<h2><?php echo $title; ?></h2>
		<p><?php _e('Please enter and activate your <b>license keys</b>.', 'emd-plugins'); ?></p>
		<form method="post" action="options.php">
		<table class="form-table">
		<tbody>
		<?php 
		settings_fields('emd_licenses'); 
	if(!empty($settings[$app])){
		emd_show_license_row($app,$settings[$app]['name']);
	}
	foreach($settings as $key => $val){
		if($val['type'] == 'ext'){
			emd_show_license_row($key,$val['name']);
		}
	} 
	?>
		</tbody>
		</table>
		<?php submit_button(); ?>
		</form>
		</div>
		<?php
}

/**
 * Show input field and license activate/deactivate link for app and each extension
 *
 * @param string $app
 * @param string $name license name
 * @since WPAS 4.2
 *
 * @return html page content
 */
function emd_show_license_row($app,$name){
	$license = false;
	$status = "inactive";
	$licenses = get_option('emd_licenses',Array());
	if(!empty($licenses) && isset($licenses[$app . '_license_key'])){
		$license = $licenses[$app . '_license_key'];
	}
	if(!empty($licenses) && isset($licenses[$app . '_license_status'])){
		$status = $licenses[$app . '_license_status'];
	}
	?>
		<tr>
		<th scope="row">
		<?php echo $name; ?>
		</th>
		<td>
		<input id="<?php esc_attr_e($app) ?>_license_key" name="emd_licenses[<?php esc_attr_e($app) ?>_license_key]" type="text" class="regular-text" value="<?php esc_attr_e($license); ?>" />
		<?php if (false !== $license) { ?>
		<input type="hidden" id="<?php esc_attr_e($app) ?>_license_status" name="emd_licenses[<?php esc_attr_e($app) ?>_license_status]" value="<?php echo $status;?>">
			<?php if ($status == 'valid') { ?>
				<?php wp_nonce_field($app . '_license_nonce', $app . '_license_nonce'); ?>
					<input type="submit" class="button-secondary" name="<?php esc_attr_e($app) ?>_license_deactivate" value="<?php _e('Deactivate License', 'emd-plugins'); ?>"/>
					<?php
			} else {
				wp_nonce_field($app . '_license_nonce', $app . '_license_nonce'); ?>
					<input type="submit" class="button-secondary" name="<?php esc_attr_e($app) ?>_license_activate" value="<?php _e('Activate License', 'emd-plugins'); ?>"/>
					<?php
			}
			echo "&nbsp;<span style='color:red;font-weight:700;'>" . strtoupper($status) . "</span>";
		}?>
	</td>
		</tr>
		<?php 
}

add_action( 'admin_init', 'emd_license_register');

/**
 * Register license settings option
 *
 * @since WPAS 4.2
 *
 */
function emd_license_register(){
	if ( false == get_option( 'emd_licenses' ) ) {
                add_option( 'emd_licenses' );
        }
	$settings = get_option('emd_license_settings');
	if(!empty($settings)){
		foreach($settings as $key => $val){
			$status = "";
			if(isset($_POST[$key . '_license_status'])){
				$status = $_POST[$key . '_license_status'];
			}
			add_settings_field($key . '_license_key', $val['name'],'','emd_licenses');
			add_settings_field($key . '_license_status', $status,'','emd_licenses');
		}
	}
	register_setting('emd_licenses','emd_licenses','emd_sanitize_license');
}
/**
 * Sanitize license settings
 *
 * @since WPAS 4.2
 * @param array $new
 *
 * @return array $new
 */
function emd_sanitize_license($new){
	if ( empty( $_POST['_wp_http_referer'] ) ) {
                return $new;
        }
	$old = get_option('emd_licenses');
	foreach($new as $nkey => $nval){
		if(preg_match('/_license_key$/',$nkey)){
			$match = str_replace("_license_key","",$nkey);
			if(empty($new[$nkey])){
				unset($new[$nkey]);
				unset($_POST['emd_licenses'][$nkey]);
			}
			elseif (isset($old[$nkey]) && $old[$nkey] != $new[$nkey]) {
				unset($new[$match.'_license_status']); //new license has been entered, so must reactivate
			}
			elseif(isset($new[$match .'_license_status'])) {
				$_POST['emd_licenses'][$match .'_license_status'] = $new[$match .'_license_status'];
			}
		}
	}
	return $new;
}
add_action( 'admin_init', 'emd_activate_deactivate_license');
/**
 * Activate/Deactivate license by calling edd api on plugin author's site
 * @since WPAS 4.0
 *
 */
function emd_activate_deactivate_license() {
	if(!isset($_POST['emd_licenses'])) return;
	$license_action = "";
	$license_on = "";
	$license_settings = get_option('emd_license_settings');
	if(!empty($license_settings)){
		foreach($license_settings as $key => $val){
			// listen for our activate button to be clicked
			if (isset($_POST[$key . '_license_activate'])) {
				$license_action = "activate";
				$license_on = $key;
				break;
			} elseif (isset($_POST[$key . '_license_deactivate'])) {
				$license_action = "deactivate";
				$license_on = $key;
				break;
			}
		}
	}
	if (!empty($license_action)) {
		$emd_licenses = $_POST['emd_licenses'];
		// run a quick security check
		if (!check_admin_referer($license_on . '_license_nonce', $license_on . '_license_nonce')) return;
		// retrieve the license from the database
		$license = trim($emd_licenses[$license_on . '_license_key']);
		// data to send in our API request
		$api_params = array(
				'edd_action' => $license_action . '_license',
				'license' => $license,
				'item_name' => urlencode(constant(strtoupper($license_on) . '_EDD_ITEM_NAME')) , // the name of product
				'url' => home_url()
				);

		// Call the custom API.
		$response = wp_remote_get(add_query_arg($api_params, constant(strtoupper($license_on) . '_EDD_STORE_URL')) , array(
					'timeout' => 15,
					'sslverify' => false
					));
		// make sure the response came back okay
		if (is_wp_error($response)) return false;
		// decode the license data
		$license_data = json_decode(wp_remote_retrieve_body($response));

		if ($license_action == 'activate') {
			$emd_licenses[$license_on . '_license_status']= $license_data->license;
			update_option('emd_licenses',$emd_licenses);
		} elseif ($license_action == 'deactivate' && $license_data->license == 'deactivated') {
			$emd_licenses[$license_on . '_license_status']= $license_data->license;
			update_option('emd_licenses',$emd_licenses);
		}
	}
}

