<?php
/**
 * Misc Admin Functions
 *
 * @package UTUBE_SCASE
 * @version 1.0.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
add_action('edit_form_advanced', 'utube_scase_force_post_builtin');
/**
 * Add required js check for builtin fields and taxonomies
 *
 * @since WPAS 4.0
 *
 * @return js
 */
function utube_scase_force_post_builtin() {
	$post = get_post();
	if (in_array($post->post_type, Array(
		'emd_video'
	))) { ?>
   <script type='text/javascript'>
       jQuery('#publish').click(function(){
           var msg = [];
           <?php if (in_array($post->post_type, Array(
			'emd_video'
		))) { ?>
   var title = jQuery('[id^="titlediv"]').find('#title');
   if(title.val().length < 1) {
       jQuery('#title').addClass('error');
       msg.push('<?php _e('Title', 'utube-scase'); ?>');
   }
<?php
		} ?>
           
           
           
           if(msg.length > 0){
              jQuery('#publish').removeClass('button-primary-disabled');
              jQuery('#ajax-loading').attr( 'style','');
              jQuery('#post').siblings('#message').remove();
              jQuery('#post').before('<div id="message" class="error"><p>'+msg.join(', ')+' <?php _e('required', 'utube-scase'); ?>.</p></div>');
              return false; 
           }
       }); 
    </script>
<?php
	}
}
