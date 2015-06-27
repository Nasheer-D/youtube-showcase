<?php global $video_items_count;
$ent_attrs = get_option('yt_scase_com_attr_list');
?>
<div class="item <?php echo (($video_items_count == 0) ? 'active' : ''); ?>">
    <div class="emd-embed-responsive">
        <iframe src="https://www.youtube.com/embed/<?php echo esc_html(emd_mb_meta('emd_video_key')); ?>
?html5=1?autoplay=<?php echo esc_html(emd_mb_meta('emd_video_autoplay')); ?>
" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="panel panel-default">
        <div class="video-summary"> <?php echo $post->post_excerpt; ?> </div>
    </div>
</div>