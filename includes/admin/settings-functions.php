<?php
/**
 * Settings Functions
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       WPAS 4.4
 */
if (!defined('ABSPATH')) exit;

add_action('emd_show_settings_page','emd_show_settings_page',1);
/**
 * Show settings page for global variables
 *
 * @param string $app
 * @since WPAS 4.4
 *
 * @return html page content
 */

function emd_show_settings_page($app){
	global $title;
	$variables = get_option($app . '_glob_list',Array());
	$form_variables = get_option($app . '_glob_forms_list');
?>
	<div class="wrap">
	<h2><?php echo $title; ?></h2>
<?php	
	if(!empty($variables)){
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'global';
		$tabs['global'] = __('Global', 'emd_plugins');
		echo '<p>' . settings_errors($app . '_glob_list') . '</p>';
	}
	if(!empty($form_variables)){
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'forms';
		$tabs['forms'] = __('Forms', 'emd_plugins');
		echo '<p>' . settings_errors($app . '_glob_forms_list') . '</p>';
	}
	if(empty($variables) && empty($form_variables)){
		echo '<h4>' . __('No settings found.','emd-plugins');
		echo '</div>';
		return;
	}
	if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == true){
		echo '<div id="message" class="updated">' . __('Settings Saved.','emd-plugins') . '</div>';
	}
	echo '<h2 class="nav-tab-wrapper">';
	foreach ($tabs as $ktab => $mytab) {
		$tab_url[$ktab] = esc_url(add_query_arg(array(
					'tab' => $ktab
				)));
		$active = "";
		if ($active_tab == $ktab) {
			$active = "nav-tab-active";
		}
		echo '<a href="' . esc_url($tab_url[$ktab]) . '" class="nav-tab ' . $active . '" id="nav-' . $ktab . '">' . $mytab . '</a>';
	}
	echo '</h2>';
	if(!empty($variables)){
		emd_glob_view_tab($app,$active_tab,$variables);
	}
	if(!empty($form_variables)){
		emd_glob_forms_tab($app,$active_tab,$form_variables,$variables);
	}
	echo '</div>';
}
function emd_glob_register_settings($app){
	register_setting($app . '_glob_list', $app . '_glob_list', 'emd_glob_sanitize');
	register_setting($app . '_glob_forms_list', $app . '_glob_forms_list', 'emd_glob_forms_sanitize');
	$variables = get_option($app . '_glob_list');
	if(!empty($variables)){
		foreach($variables as $id => $myvar){
			$args['key'] = $id;
			$args['val'] = "";
			add_settings_field($app . '_glob_list[' . $id . ']', $myvar['label'], 'emd_glob_' . $myvar['type'] . '_callback',$app . '_settings','',$args);
		}
	}
}
function emd_glob_sanitize($input){
	$variables = get_option($input['app'] . '_glob_list');
	foreach($variables as $kv => $val){
		if(isset($input[$kv])){
			$variables[$kv]['val'] = $input[$kv];
		}
		elseif($val['type'] == 'checkbox') {
			$variables[$kv]['val'] = 0;
		}
		if($val['required'] == 1 && empty($input[$kv])){
			$error_message = sprintf(__( "%s is required.", 'emd-plugins'),$val['label']);
                	add_settings_error($input['app'] . '_glob_list','required-' . $kv,$error_message,'error');
		}
		if($val['type'] == 'map'){
			$variables[$kv]['map'] = $input[$kv.'_map'];
			$variables[$kv]['width'] = $input[$kv.'_width'];
			$variables[$kv]['height'] = $input[$kv.'_height'];
			$variables[$kv]['zoom'] = $input[$kv.'_zoom'];
			$variables[$kv]['map_type'] = $input[$kv.'_map_type'];
			$variables[$kv]['marker'] = $input[$kv.'_marker'];
			$variables[$kv]['load_info'] = $input[$kv.'_load_info'];
			$variables[$kv]['marker_title'] = $input[$kv.'_marker_title'];
			$variables[$kv]['info_window'] = $input[$kv.'_info_window'];
			if(empty($input[$kv])){
				$variables[$kv]['map'] = "";
			}
		}
	}
	return $variables;
}
function emd_glob_forms_sanitize($input){
	$form_variables = get_option($input['app'] . '_glob_forms_list');
	$attr_list = get_option($input['app'] . '_attr_list');
	//$tax_list = get_option($input['app']. '_tax_list');
	//$rel_list = get_option($input['app'] . '_rel_list');
	$shc_list = get_option($input['app'] . '_shc_list');
	foreach($form_variables as $kv => $val){
		foreach($val as $kfield => $vfield){
			if($kfield != 'captcha'){
				if(isset($input[$kv][$kfield]['req']) && $input[$kv][$kfield]['req'] == 1){
					if(!isset($input[$kv][$kfield]['show'])){
						$form_variables[$kv][$kfield]['show'] = 1;
					}
					$form_variables[$kv][$kfield]['req'] = 1;
				}
				elseif(!isset($input[$kv][$kfield]['req'])){
					$form_variables[$kv][$kfield]['req'] = 0;
				}
				if($kfield == 'btn'){
					$form_variables[$kv][$kfield]['show'] = 1;
					$form_variables[$kv][$kfield]['req'] = 1;
				}
				elseif(!empty($attr_list[$shc_list['forms'][$kv]['ent']][$kfield]) && $shc_list['forms'][$kv]['type'] == 'submit' && isset($attr_list[$shc_list['forms'][$kv]['ent']][$kfield]['uniqueAttr']) && $attr_list[$shc_list['forms'][$kv]['ent']][$kfield]['uniqueAttr'] == true){
					$form_variables[$kv][$kfield]['show'] = 1;
					if($shc_list['forms'][$kv]['type'] == 'submit'){
						$form_variables[$kv][$kfield]['req'] = 1;
					}
				}
				elseif(isset($input[$kv][$kfield]['show']) && $input[$kv][$kfield]['show'] == 1){
					$form_variables[$kv][$kfield]['show'] = 1;
				}
				else {
					$form_variables[$kv][$kfield]['show'] = 0;
				}
				if(isset($input[$kv][$kfield]['size'])){
					$form_variables[$kv][$kfield]['size'] = $input[$kv][$kfield]['size'];
				}
				else {
					$form_variables[$kv][$kfield]['size'] = '';
				}
			}
			else {
				$form_variables[$kv][$kfield] = $input[$kv][$kfield];
			}
		}
	}
	return $form_variables;
}
function emd_glob_text_callback($args){
	$html = '<input type="text" class="' . $size . '-text" id="' . esc_attr($args['key']) . '" name="' . esc_attr($args['key']) . '" value="' . esc_attr(stripslashes($args['val'])) . '"/>';
	echo $html;
}
function emd_glob_val($app,$key){
	$variables = get_option(str_replace("-","_",$app) . '_glob_list');
	if($variables[$key]['type'] == 'checkbox_list' || $variables[$key]['type'] == 'multi_select'){
		if(!empty($variables[$key]['val'])){
			return implode(',',$variables[$key]['val']);
		}
	}
	elseif(isset($variables[$key]['val'])) {
		return $variables[$key]['val'];
	}
	return '';
}
function emd_glob_view_tab($app,$active_tab,$variables){
?>
	<div class='tab-content' id='tab-global' <?php if ( 'global' != $active_tab ) { echo 'style="display:none;"'; } ?>>
<?php	echo '<form method="post" action="options.php">';
	settings_fields($app .'_glob_list'); ?>
	<?php if(!empty($variables)){
	echo '<input type="hidden" name="' . esc_attr($app) . '_glob_list[app]" id="' . esc_attr($app) . '_glob_list_app" value="' . $app . '">';
	echo '<table class="form-table">
		<tbody>';
	foreach($variables as $id => $myvar){
		echo '<tr>
			<th scope="row">
			<label for="' . $id . '">';
		echo $myvar['label']; 
		if($myvar['required'] == 1){
			echo '<span class="dashicons dashicons-star-filled" style="font-size:10px;color:red;"></span>';
		}
		echo '</label>
			</th>
			<td>';
		$val = "";
		if(isset($myvar['val'])){
			$val = $myvar['val'];
			if($myvar['type'] == 'checkbox' && $val == 1){
				$val = 'checked';
			}
		}
		elseif(!empty($myvar['dflt'])){
			if(($myvar['type'] == 'checkbox_list' || $myvar['type'] == 'multi_select') && !is_array($myvar['dflt'])){
				$dflt = $myvar['dflt'];
				$val= Array("$dflt");
			}
			else {
				$val = $myvar['dflt'];
			}
		}
		switch($myvar['type']){
			case 'text':
				echo "<input id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "]' type='text' value='" . $val ."'></input>";
				break;
			case 'map':
				$width = isset($myvar['width']) ? $myvar['width'] : '';
				$height = isset($myvar['height']) ? $myvar['height'] : '';
				$zoom = isset($myvar['zoom']) ? $myvar['zoom'] : '14';
				$map_coord = isset($myvar['map']) ? $myvar['map'] : '';
				$marker = isset($myvar['marker']) ? 'checked' : '';
				$load_info = isset($myvar['load_info']) ? 'checked' : '';
				$map_type = isset($myvar['map_type']) ? $myvar['map_type'] : '';
				$marker_title = isset($myvar['marker_title']) ? $myvar['marker_title'] : '';
				$info_window = isset($myvar['info_window']) ? $myvar['info_window'] : '';
				echo "<input id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "]' type='text' size='50' value='" . $val ."'></input>";
				 if(!empty($myvar['desc'])){
                        		echo "<p class='description'>" . $myvar['desc'] . "</p>";
                		}
				echo "<tr><th scope='row'></th><td><table><th scope='row'><label>" . __('Frontend Map Settings','emd-plugins') . "</th><td></td></tr>
				<th scope='row'><label for='" . $id . "_width'>" . __('Width','emd-plugins') . "</th><td><input id='" . esc_attr($app) . "_glob_list_" . $id . "_width' name='" . esc_attr($app) . "_glob_list[" . $id . "_width]' type='text' value='" . $width . "'></input><p class='description'>" . __('Sets the map width.You can use \'%\' or \'px\'. Default is 100%.','emd-plugins') . "</p></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_height'>" . __('Height','emd-plugins') . "</th><td><input id='" . esc_attr($app) . "_glob_list_" . $id . "_height' name='" . esc_attr($app) . "_glob_list[" . $id . "_height]' type='text' value='" . $height ."'></input><p class='description'>" . __('Sets the map height. You can use \'px\'. Default is 480px.','emd-plugins') . "</p></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_zoom'>" . __('Zoom','emd-plugins') . "</th><td><select id='" . esc_attr($app) . "_glob_list_" . $id . "_zoom' name='" . esc_attr($app) . "_glob_list[" . $id . "_zoom]'>";
				for($i=20;$i >=1;$i--){
					echo "<option value='" . $i . "'";
					if($zoom == $i){
						echo " selected";
					}
					echo ">" . $i . "</option>";
				}
				echo "</select></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_map_type'>" . __('Type','emd-plugins') . "</th><td><select id='" . esc_attr($app) . "_glob_list_" . $id . "_map_type' name='" . esc_attr($app) . "_glob_list[" . $id . "_map_type]'>";
				$map_types = Array("ROADMAP","SATELLITE","HYBRID","TERRAIN");
				foreach($map_types as $mtype){
					echo "<option value='" . $mtype . "'";
					if($map_type == $mtype){
						echo " selected";
					}
					echo ">" . $mtype . "</option>";
				}
				echo "</select></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_marker'>" . __('Marker','emd-plugins') . "</th><td><input id='" . esc_attr($app) . "_glob_list_" . $id . "_marker' name='" . esc_attr($app) . "_glob_list[" . $id . "_marker]' type='checkbox' value=1 $marker></input></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_marker_title'>" . __('Marker Title','emd-plugins') . "</th><td><input id='" . esc_attr($app) . "_glob_list_" . $id . "_marker_title' name='" . esc_attr($app) . "_glob_list[" . $id . "_marker_title]' type='text' value='" . $marker_title ."'></input><p class='description'>" . __('Sets the marker title when hover.','emd-plugins') . "</p></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_info_window'>" . __('Info Window','emd-plugins') . "</th><td><textarea id='" . esc_attr($app) . "_glob_list_" . $id . "_info_window' name='" . esc_attr($app) . "_glob_list[" . $id . "_info_window]'>" . $info_window . "</textarea><p class='description'>" . __('Sets the content of the info box. You can use html tags.','emd-plugins') . "</p></td></tr>";
				echo "<tr><th scope='row'><label for='" . $id . "_load_info'>" . __('Display Info Window on Page Load','emd-plugins') . "</th><td><input id='" . esc_attr($app) . "_glob_list_" . $id . "_load_info' name='" . esc_attr($app) . "_glob_list[" . $id . "_load_info]' type='checkbox' value=1 $load_info></input></td></tr>";
				echo "<tr><th><p class='description'>" . __('You can drag and drop the marker to specify the exact location.','emd-plugins') . "</th><td><div class='emd-mb-map-field'><div class='emd-mb-map-canvas' data-default-loc=''></div>
					<input type='hidden' name='" . esc_attr($app) . "_glob_list[" . $id . "_map]' class='emd-mb-map-coordinate' value='" . $map_coord ."'>";
                                echo "<button style='display:none;' class='button emd-mb-map-goto-address-button' value='".  esc_attr($app) . "_glob_list_" . $id . "'>Find Address</button>";
				echo "</div></td></tr></table></td></tr>";
				break;
			case 'textarea':
				echo "<textarea id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "]'>" . $val ."</textarea>";
				break;
			case 'wysiwyg':
				echo wp_editor($val, esc_attr($app) . "_glob_list_" . $id, array(
							'tinymce' => false,
							'textarea_rows' => 10,
							'media_buttons' => true,
							'textarea_name' => esc_attr($app) . "_glob_list[" . $id . "]",
							'quicktags' => Array(
								'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,spell'
								)
							));
				break;
			case 'checkbox':
				echo "<input id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "]' type='checkbox' value='1'";
				if($val === 'checked'){
					echo " checked";
				}
				echo "></input>";
				break;
			case 'checkbox_list':
				if (!empty($myvar['values'])) {
					foreach($myvar['values'] as $kvalue => $mvalue){
						if (in_array($kvalue,$val)) {
							$checked = 'checked';
						} else {
							$checked = '';
						}
						echo "<input name='" . esc_attr($app) . "_glob_list[" . $id . "][] id='" . esc_attr($app) . "_glob_list[" . $id . "]" . "' type='checkbox' value='" . $kvalue . "' " . $checked . "/>&nbsp;";
						echo "<label for='" . esc_attr($app) . "_glob_list[" . $id . "]'>" . $mvalue . "</label><br/>";
					}
				}
				break;
			case 'radio':
				if (!empty($myvar['values'])) {
					foreach($myvar['values'] as $kvalue => $mvalue){
						if ($val == $kvalue) {
							$checked = 'checked';
						} else {
							$checked = '';
						}
						echo "<input name='" . esc_attr($app) . "_glob_list[" . $id . "] id='" . esc_attr($app) . "_glob_list_" . $id .  "' type='radio' value='" . $kvalue . "' " . $checked . "/>&nbsp;";
						echo "<label for='" . esc_attr($app) . "_glob_list[" . $id . "]'>" . $mvalue . "</label><br/>";
					}
				}
				break;
			case 'select':
				echo "<select id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "]'>";
				foreach($myvar['values'] as $kvalue => $mvalue){
					if($val == $kvalue){
						$selected = "selected";
					}
					else {
						$selected = "";
					}
					echo "<option value='" . $kvalue . "' " . $selected . "/>";
					echo  $mvalue . "</option>";
				}
				echo "</select>";
				break;
			case 'multi_select':
				echo "<select id='" . esc_attr($app) . "_glob_list_" . $id . "' name='" . esc_attr($app) . "_glob_list[" . $id . "][]' multiple>";
				foreach($myvar['values'] as $kvalue => $mvalue){
					if(in_array($kvalue,$val)){
						$selected = "selected";
					}
					else {
						$selected = "";
					}
					echo "<option value='" . $kvalue . "' " . $selected . "/>";
					echo  $mvalue . "</option>";
				}
				echo "</select>";
				break;
		}
		if(!empty($myvar['desc'])){
			echo "<p class='description'>" . $myvar['desc'] . "</p>";
		}
		
		echo '</td>
			</tr>';
	}
	echo '</tbody></table>';
}
?>
	</tbody>
	</table>
<?php
	submit_button(); 
	echo '</form></div>';
}
function emd_glob_forms_tab($app,$active_tab,$form_variables,$glb_list){
	$attr_list = get_option($app . '_attr_list');
	$tax_list = get_option($app . '_tax_list');
	$rel_list = get_option($app . '_rel_list');
	$shc_list = get_option($app . '_shc_list');
	echo '<div class="tab-content" id="tab-forms"';
	if ( 'forms' != $active_tab ) { 
		echo 'style="display:none;"'; 
	} 
	echo '>';
	echo '<form method="post" action="options.php">';
	settings_fields($app .'_glob_forms_list'); 
	$form_html = '<h4>';
	$form_header = __('Use the section below to show or hide corresponding form elements. Size column refers to the form elements length relative to the other elements in the same row. Total element size in each row can not exceed 12 units. When you hide or show an element you may adjust sizes of the other elements in the same row. The form elements which are in the same row are color coded.','emd-plugins'); 
	$form_content = '</h4>';
	$form_content .= '<div id="forms-list" class="accordion-container"><ul class="outer-border">';
	$unique_text = 0;
	if(!empty($form_variables)){
		foreach($form_variables as $key => $val){
			$form_label = isset($shc_list['forms'][$key]['page_title']) ? $shc_list['forms'][$key]['page_title'] : ucwords(str_replace("_"," ",$shc_list['forms'][$key]['name']));
			$form_content .= '<li id="' . esc_attr($key) . '" class="control-section accordion-section">
			<h3 class="accordion-section-title hndle" tabindex="0">' . $form_label . '</h3>';
			$form_content .= '<div class="accordion-section-content"><div class="inside">';
			$form_content .= '<input type="hidden" name="' . esc_attr($app) . '_glob_forms_list[app]" id="' . esc_attr($app) . '_glob_forms_list_app" value="' . $app . '">';
			$form_content .= '<table class="form-table">';
			$row = 1;
			foreach($val as $elm_key => $elm_val){
				$label = "";
				$req = 0;
				$unique = false;
				$req_disable = 0;
				if($elm_key != 'captcha'){
					$form_content .= '<tr'; 
					if($row == $elm_val['row']){
						$form_content .= ' style="background-color: rgb(245, 245, 255);"';
					}
					else {
						$row = $elm_val['row'] + 1;
					}
					$req = $elm_val['req'];
					$form_content .= '>
						<th scope="row">
						<label for="' . $elm_key . '">';
					if(!empty($attr_list[$shc_list['forms'][$key]['ent']][$elm_key])){
						$label = $attr_list[$shc_list['forms'][$key]['ent']][$elm_key]['label'];
						if($attr_list[$shc_list['forms'][$key]['ent']][$elm_key]['display_type'] == 'file'){
							$req_disable = 1;
						}
						if(isset($attr_list[$shc_list['forms'][$key]['ent']][$elm_key]['uniqueAttr']) && $shc_list['forms'][$key]['type'] == 'submit'){
							$unique_text = 1;
							$unique = $attr_list[$shc_list['forms'][$key]['ent']][$elm_key]['uniqueAttr'];
						}
					}
					elseif(!empty($tax_list[$shc_list['forms'][$key]['ent']][$elm_key])){
						$label = $tax_list[$shc_list['forms'][$key]['ent']][$elm_key]['label'];
					}
					elseif(!empty($rel_list[$elm_key])){
						if($shc_list['forms'][$key]['ent'] == $rel_list[$elm_key]['from']){
							$label = $rel_list[$elm_key]['from_title'];
						}
						else {
							$label = $rel_list[$elm_key]['to_title'];
						}
					}
					elseif($elm_val['label']) {
						$label = $elm_val['label'];
						if(!empty($glb_list[$elm_key])){
							$req_disable = 1;
						}
					}
					$form_content .= sprintf(__('Show %s','emd-plugins'),$label); 
					$form_content .= '</label>
						</th>
						<td>';
					$form_content .= '<input id="' . esc_attr($app) . '_glob_forms_list_' . $elm_key . '_show" name="' . esc_attr($app) . '_glob_forms_list[' . $key . '][' . $elm_key . '][show]" type="checkbox" value=1';
					if($elm_val['show'] == 1){
						$form_content .= " checked";
					}
					if($unique == true || $elm_key == 'btn'){
						$form_content .= " disabled";
					}
					if(!$elm_val['size']){
						$elm_val['size'] = "";
					}
					$form_content .= '></input></td><th>Required:</th><td>
					<input type="checkbox" value="1" id="' . esc_attr($app) . '_glob_forms_list_' . $elm_key . '" name="' . esc_attr($app) . '_glob_forms_list[' . $key . '][' . $elm_key . '][req]"';
					if($req == 1){
						$form_content .= ' checked';
					}
					if($req_disable == 1 || $unique == true || $elm_key == 'btn'){
						$form_content .= ' disabled';
					}
                                        $form_content .= '></td>';
					$form_content .= '<th scope="row">Size:</th><td>
					<input type="text" class="small-text" id="' . esc_attr($app) . '_glob_forms_list_' . $elm_key . '" name="' . esc_attr($app) . '_glob_forms_list[' . $key . '][' . $elm_key . '][size]" value="' . $elm_val['size'] . '">
					</td></tr>';
				}
				else {
					$captcha_str = '<tr><th scope="row"><label for="captcha">' . __('Show Captcha','emd-plugins') . '</label></th>';
					$captcha_str .= '<td><select id="' . esc_attr($app) . '_glob_forms_list_captcha" name="' . esc_attr($app) . '_glob_forms_list[' . $key . '][captcha]">';
					$captcha_vals = Array('never-show' => __('Never Show','emd-plugins'),
							'show-always' => __('Always Show','emd-plugins'),
							'show-to-visitors' => __('Visitors Only','emd-plugins')
						);
					foreach($captcha_vals as $ckey => $cval){
						$captcha_str .= '<option value="' . $ckey . '"';
						if($elm_val == $ckey){
							$captcha_str .= ' selected';
						}		
						$captcha_str .= '>' . $cval . '</option>';
					}
					$captcha_str .= '</select>';
				}
			}
			$form_content .= $captcha_str;
			$form_content .= '</table>';
			$form_content .= '</div></div></li>';
		}
	}
	if($unique_text == 1){
		$form_header .= __('The unique form elements are disabled however you can change their sizes.','emd-plugins');
	}
	$form_html .= $form_header . $form_content . '</div>';
	echo $form_html;
	submit_button(); 
	$form_html .= '</form></div>';
}
function emd_get_global_map($app,$key){
	$glob_list = get_option(str_replace("-","_",$app) . '_glob_list');
	$inp_args = Array();
	$width = '100%'; // Map width, default is 640px. You can use '%' or 'px'
	$height = '480px'; // Map height, default is 480px. You can use '%' or 'px'
	if(!empty($glob_list[$key]['map'])){
		$value_map = $glob_list[$key]['map'];
		$map_arr = explode(",",$value_map);
		$latitude = $map_arr[0];
		$longitude = $map_arr[1];
		$marker = ($glob_list[$key]['marker']) ? true : false;
		$load_info = ($glob_list[$key]['load_info']) ? true : false;
		$zoom = (int) $glob_list[$key]['zoom'];
		if(!empty($glob_list[$key]['width'])){
			$width = $glob_list[$key]['width'];
		}
		if(!empty($glob_list[$key]['height'])){
			$height = $glob_list[$key]['height'];
		}
		$inp_args = array(
			'latitude'     => $latitude,
			'longitude'    => $longitude,
			'zoom'         => $zoom,  // Map zoom, default is the value set in admin, and if it's omitted - 14
			'width'        => $width,
			'height'       => $height,
			// Map type, see https://developers.google.com/maps/documentation/javascript/reference#MapTypeId
			'mapTypeId'    => $glob_list[$key]['map_type'],
			'marker'       => $marker, // Display marker? Default is 'true',
			'load_info'    => $load_info,
			'marker_title' => $glob_list[$key]['marker_title'], // Marker title when hover
			'info_window'  => $glob_list[$key]['info_window'], // Info window content, can be anything. HTML allowed.
		);
	}
	$args = wp_parse_args( $inp_args, array(
			'latitude'     => '25.7616798',
			'longitude'    => '-80.19179020000001',
			'zoom'         => 14,
			'mapTypeId'    => 'ROADMAP',
			'marker'       => false,
			'load_info'    => false,
			'width'        => $width,
			'height'       => $height,
			'marker_title' => '',
			'info_window'  => '',
			'js_options'   => array(),
		) );
	$args['js_options'] = wp_parse_args( $args['js_options'], array(
				'zoom'      => $args['zoom'],
				'marker_title' => $args['marker_title'],
				'mapTypeId' => $args['mapTypeId'],
			) );
	$js_options =  esc_attr( wp_json_encode( $args ) );
	return ' <style type="text/css" media="screen">
		/*<![CDATA[*/
		.gm-style img{ 
		max-width:none !important; 
		/*]]>*/} 
		</style>
		<div class="emd-mb-map-canvas" data-map_options="' . $js_options . '" style="width:' . $args['width'] . ';height:' . $args['height'] . ';"></div>';
	}


