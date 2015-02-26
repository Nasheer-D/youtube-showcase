<?php
/**
 * Settings Glossary Functions
 *
 * @package YT_SCASE_COM
 * @version 1.1.1
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
add_action('yt_scase_com_settings_glossary', 'yt_scase_com_settings_glossary');
/**
 * Display glossary information
 * @since WPAS 4.0
 *
 * @return html
 */
function yt_scase_com_settings_glossary() {
	global $title;
?>
<div class="wrap">
<h2><?php echo $title; ?></h2>
<p><?php _e('YouTube Showcase is a powerful but simple-to-use YouTube video gallery plugin with responsive frontend.', 'yt-scase-com'); ?></p>
<p><?php _e('The below are the definitions of entities, attributes, and terms included in Youtube Showcase.', 'yt-scase-com'); ?></p>
<div id="glossary" class="accordion-container">
<ul class="outer-border">
<li id="emd_video" class="control-section accordion-section">
<h3 class="accordion-section-title hndle" tabindex="1"><?php _e('Videos', 'yt-scase-com'); ?></h3>
<div class="accordion-section-content">
<div class="inside">
<table class="form-table"><p class"lead"><?php _e('Videos are YouTube videos identified by Video ID.', 'yt-scase-com'); ?></p><tr>
<th><?php _e('Title', 'yt-scase-com'); ?></th>
<td><?php _e(' Title is a required field. Title does not have a default value. ', 'yt-scase-com'); ?></td>
</tr><tr>
<th><?php _e('Video Key', 'yt-scase-com'); ?></th>
<td><?php _e('<p>The unique 11 digit alphanumeric video key found on the YouTube video. For example; in https://www.youtube.com/watch?v=uVgWZd7oGOk. uVgWZd7oGOk is the video id.</p> Video Key is a required field. Being a unique identifier, it uniquely distinguishes each instance of Video entity. Video Key is filterable in the admin area. Video Key does not have a default value. ', 'yt-scase-com'); ?></td>
</tr><tr>
<th><?php _e('Excerpt', 'yt-scase-com'); ?></th>
<td><?php _e(' Excerpt does not have a default value. ', 'yt-scase-com'); ?></td>
</tr><tr>
<th><?php _e('Featured', 'yt-scase-com'); ?></th>
<td><?php _e('Adds the video to featured video list. Featured is filterable in the admin area. Featured does not have a default value. ', 'yt-scase-com'); ?></td>
</tr><tr>
<th><?php _e('Content', 'yt-scase-com'); ?></th>
<td><?php _e(' Content does not have a default value. ', 'yt-scase-com'); ?></td>
</tr></table>
</div>
</div>
</li>
</ul>
</div>
</div>
<?php
}
