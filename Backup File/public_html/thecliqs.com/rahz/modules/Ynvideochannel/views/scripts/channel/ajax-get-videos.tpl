<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <?php foreach ($this->paginator as $video):?>
        <li class="ynvideochannel_videos_grid-item ynvideochannel_videos_duration_hover">
            <?php $photo_url = ($video->getPhotoUrl('thumb.normal')) ? $video->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_video_thumb_normal.png'; ?>
            
            <div class="ynvideochannel_videos_grid-item-bg" style="background-image: url('<?php echo $photo_url; ?>')">
            
                <div class="ynvideochannel_videos_grid-item-options ynvideochannel_video-channel-playlist_options clearfix">
                    <?php echo $this->partial('_video_options.tpl', 'ynvideochannel', array('video' => $video)); ?>
                    <?php echo $this->partial('_add_to_menu.tpl','ynvideochannel', array('video' => $video)); ?>
                </div>

                <div class="ynvideochannel_videos_grid-item-duration ynvideochannel_videos_duration">
                    <?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $video)); ?>
                    <a href="<?php echo $video -> getHref()?>"><i class="fa fa-play" aria-hidden="true"></i></a>
                </div>
            </div>
            
            <div class="ynvideochannel_videos_grid-item-info">
                <div class="ynvideochannel_videos_grid-item-title">
                    <a href="<?php echo $video -> getHref()?>">
                        <?php echo $video -> getTitle()?>
                    </a>
                </div>
                
                <div class="ynvideochannel_videos_grid-item-owner-creation_date">
                    <div class="ynvideochannel_videos_grid-item-owner">
                        <?php echo $this -> translate("by %s", $video -> getOwner())?>
                    </div>

                    <div class="ynvideochannel_videos_grid-item-createion_date">
                        <?php echo $this->timestamp(strtotime($video->creation_date)) ?>
                    </div>
                </div>
                
                <div class="ynvideochannel_videos_grid-item-count-rating clearfix">
                    <div class="ynvideochannel_videos_grid-item-count">
                        <?php echo $this -> translate(array("%s view", "%s views", $video -> view_count), $video -> view_count)?>
                    </div>

                    <div class="ynvideochannel_videos_grid-item-rating ynvideochannel_videos_rating">
                        <?php echo $this->partial('_video_rating.tpl', 'ynvideochannel', array('rating' => $video->rating)); ?>
                    </div>
                </div>
            </div>
        </li>
    <?php endforeach;?>
    <?php echo $this->paginationControl($this->paginator, null, array(0 => '_pagination.tpl', 1 => 'ynvideochannel'),null); ?>
<?php endif;?>