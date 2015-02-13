<?php global $video_items_count;
$ent_attrs = get_option('utube_scase_attr_list'); ?>
<div class="item <?php echo (($video_items_count == 0) ? 'active' : ''); ?>">
    <div class="emd-embed-responsive">
        <iframe src="https://www.youtube.com/embed/<?php echo esc_html(rwmb_meta('emd_video_key')); ?>
?html5=1" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="panel panel-default">
        <div class="panel-body"> <?php echo $post->post_excerpt; ?> </div>
    </div>
</div>