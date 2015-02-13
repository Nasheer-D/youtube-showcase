<?php global $video_grid_count;
$ent_attrs = get_option('utube_scase_attr_list'); ?>
<div class="col-sm-3">
    <div class="panel panel-info">
        <div class="panel-body">
            <div class="thumbnail">
                <a href="<?php echo get_permalink(); ?>" title="<?php echo get_the_title(); ?>">
                    <img title="<?php echo get_the_title(); ?>" src="https://img.youtube.com/vi/<?php echo esc_html(rwmb_meta('emd_video_key')); ?>
/maxresdefault.jpg" alt="<?php echo get_the_title(); ?>">
                </a>
            </div>
        </div>
    </div>
</div>