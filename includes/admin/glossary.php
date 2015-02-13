<?php
/**
 * Settings Glossary Functions
 *
 * @package UTUBE_SCASE
 * @version 1.0.1
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
add_action('utube_scase_settings_glossary', 'utube_scase_settings_glossary');
/**
 * Display glossary information
 * @since WPAS 4.0
 *
 * @return html
 */
function utube_scase_settings_glossary() {
	global $title;
?>
<div class="wrap">
<h2><?php echo $title; ?></h2>
<p><?php _e('YouTube Showcase is a powerful but simple-to-use YouTube video gallery plugin with responsive frontend.', 'utube-scase'); ?></p>
<p><?php _e('The below are the definitions of entities, attributes, and terms included in Youtube Showcase.', 'utube-scase'); ?></p>
<div id="glossary" class="accordion-container">
<ul class="outer-border">
<li id="emd_video" class="control-section accordion-section">
<h3 class="accordion-section-title hndle" tabindex="1"><?php _e('Videos', 'utube-scase'); ?></h3>
<div class="accordion-section-content">
<div class="inside">
<table class="form-table"><p class"lead"><?php _e('Videos are YouTube videos identified by Video ID.', 'utube-scase'); ?></p><tr>
<th><?php _e('Title', 'utube-scase'); ?></th>
<td><?php _e(' Title is a required field. Title does not have a default value. ', 'utube-scase'); ?></td>
</tr><tr>
<th><?php _e('Video Key', 'utube-scase'); ?></th>
<td><?php _e('<p>The unique 11 digit alphanumeric video key found on the YouTube video. For example; in https://www.youtube.com/watch?v=uVgWZd7oGOk. uVgWZd7oGOk is the video id.</p> Video Key is a required field. Being a unique identifier, it uniquely distinguishes each instance of Video entity. Video Key is filterable in the admin area. Video Key does not have a default value. ', 'utube-scase'); ?></td>
</tr><tr>
<th><?php _e('Excerpt', 'utube-scase'); ?></th>
<td><?php _e(' Excerpt does not have a default value. ', 'utube-scase'); ?></td>
</tr><tr>
<th><?php _e('Featured', 'utube-scase'); ?></th>
<td><?php _e('Adds the video to featured video list. Featured is filterable in the admin area. Featured does not have a default value. ', 'utube-scase'); ?></td>
</tr><tr>
<th><?php _e('Content', 'utube-scase'); ?></th>
<td><?php _e(' Content does not have a default value. ', 'utube-scase'); ?></td>
</tr></table>
</div>
</div>
</li>
</ul>
</div>
</div>
<?php
}
