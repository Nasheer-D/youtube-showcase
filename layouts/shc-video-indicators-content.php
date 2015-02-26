<?php global $video_indicators_count;
$ent_attrs = get_option('yt_scase_com_attr_list'); ?>
<div class="col-sm-3 <?php echo (($video_indicators_count == 0) ? 'active' : ''); ?>" data-slide-to="<?php echo $video_indicators_count; ?>" data-target="#emdvideos">
    <div class="panel panel-info">
        <div class="panel-body">
            <div class="thumbnail">
                <img src="https://img.youtube.com/vi/<?php echo esc_html(emd_mb_meta('emd_video_key')); ?>
/mqdefault.jpg" alt="<?php echo get_the_title(); ?>">
            </div>
        </div>
        <div class="panel-footer"><?php echo get_the_title(); ?></div>
    </div>
</div>