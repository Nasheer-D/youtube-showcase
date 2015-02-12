<?php global $video_indicators_count;
$ent_attrs = get_option('utube_scase_attr_list'); ?>
<div class="col-sm-3 <?php echo (($video_indicators_count == 0) ? 'active' : ''); ?>" data-slide-to="!#layout_counter_id#" data-target="#emdvideos">
    <div class="panel panel-info">
        <div class="panel-body">
            <div class="thumbnail">
                <img src="https://img.youtube.com/vi/<?php echo esc_html(rwmb_meta('emd_video_key')); ?>
/maxresdefault.jpg" alt="<?php echo get_the_title(); ?>">
            </div>
        </div>
        <div class="panel-footer"><?php echo get_the_title(); ?></div>
    </div>
</div>