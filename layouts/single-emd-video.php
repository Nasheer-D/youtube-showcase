<?php $ent_attrs = get_option('utube_scase_attr_list'); ?>
<div style="position:relative" class="emd-container">
<div class="emd-embed-responsive">
	<iframe src="https://www.youtube.com/embed/<?php echo esc_html(rwmb_meta('emd_video_key')); ?>
?html5=1" frameborder="0" allowfullscreen></iframe>
</div>
<div class="video-summary"><?php echo $post->post_content; ?></div>
</div><!--container-end-->