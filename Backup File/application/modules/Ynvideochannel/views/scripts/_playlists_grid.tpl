<ul class="ynvideochannel_playlists_grid clearfix">
    <?php foreach($this->playlists as $item):?>
        <li class="ynvideochannel_playlists_grid-item">
            <div class="ynvideochannel_playlists_grid-content">
                <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_playlist_thumb_icon.png'; ?>

                <div class="ynvideochannel_playlists_grid-bg" style="background-image: url(<?php echo $photo_url ?>)">
                    <a href="<?php echo $item -> getHref()?>">
                        <div class="ynvideochannel_playlists_grid-count">
                            <i class="fa fa-bars"></i>
                            <span><?php echo $this->translate(array('<span class="ynvideochannel_playlists_grid-number">%s</span> video', '<span class="ynvideochannel_playlists_grid-number">%s</span> videos', $item->video_count), $this->locale()->toNumber($item->video_count)) ?></span>
                        </div>
                    </a>
                </div>

                <div class="ynvideochannel_playlists_grid-info">
                    <div class="ynvideochannel_playlists_grid-title"><?php echo $item ?></div>

                    <div class="ynvideochannel_playlists_grid-owner">
                        <span>
                            <span><?php echo $this->translate('by') ?></span>
                            <?php echo $item->getOwner(); ?>
                             <span>&nbsp;.&nbsp;</span>
                            <span><?php echo $this->locale()->todateTime(strtotime($item->creation_date), array('type' => 'date')) ?></span>
                        </span>
                    </div>
                </div>



                <div class="ynvideochannel_playlists_grid-actions ynvideochannel_video-channel-playlist_options">
                    <?php echo $this->partial('_playlist_options.tpl', 'ynvideochannel', array('playlist' => $item)); ?>
                </div>

                <ul class="ynvideochannel_playlists_grid-playlist-videos">
                    <?php foreach($item->getVideos(2) as $video): ?>
                        <li>
                            <i class="fa fa-angle-right"></i>&nbsp;<?php echo $this->htmlLink($video->getHref(), $video->getTitle()) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </li>
    <?php endforeach;?>
</ul>

<script type="text/javascript">
    ynvideochannelPlaylistOptions();
</script>