<ul class="ynvideochannel_channels_grid clearfix">
    <?php foreach($this -> channels as $item):?>
        <li class="ynvideochannel_channels_grid-item">


            <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/noimg_channel.jpg'; ?>
            
            <div class="ynvideochannel_channels_grid-bg" style="background-image: url('<?php echo $photo_url; ?>') ">
                <a href="<?php echo $item -> getHref()?>"></a>
                <?php if($item -> is_featured):?>
                    <div class="ynvideochannel_videos_channel-featured">
                        <?php echo $this -> translate("Featured")?>
                    </div>
                <?php endif;?>
                <div class="ynvideochannel_channels_grid-bgopacity">
                    <div class="ynvideochannel_channels_grid-count-videos">
                        <i class="fa fa-video-camera" aria-hidden="true"></i> <?php echo $item -> video_count?>
                    </div>

                    <a class="ynvideochannel_channels_grid-title" href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a>

                    <div class="ynvideochannel_channels_grid-count">
                        <span id="ynvideochannel_subscriber_count_<?php echo $item -> channel_id ?>"><?php echo $this -> translate(array("%s subscriber", "%s subscribers", $item -> subscriber_count), $item -> subscriber_count)?></span>
                        &nbsp;.&nbsp;
                        <span><?php echo $this -> translate(array("%s like", "%s likes", $item -> like_count), $item -> like_count)?></span>
                        &nbsp;.&nbsp;
                        <span><?php echo $this -> translate(array("%s comment", "%s comments", $item -> comment_count), $item -> comment_count)?></span>
                    </div>

                    <div class="ynvideochannel_channels_grid-descriptions"><?php echo $this -> string() -> truncate(strip_tags($item -> description), 200);?></div>
                    
                    <div class="ynvideochannel_channels_grid-buttons">
                        <a class="ynvideochannel_channel_view_btn" href="<?php echo $item -> getHref()?>"><?php echo $this -> translate("View channel");?></a>
                    </div>

                </div>
            </div>
        
            <div class="ynvideochannel_channels_grid-options">
                <?php echo $this->partial('_channel_options.tpl', 'ynvideochannel', array('channel' => $item, 'showEditDel' => true)); ?>
            </div>
        </li>
    <?php endforeach;?>
</ul>


<script type="text/javascript">
    ynvideochannelChannelOptions();
</script>