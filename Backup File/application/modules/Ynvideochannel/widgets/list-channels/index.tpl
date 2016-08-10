<div class="ynvideochannel_count_channels">
    <i class="fa fa-desktop" aria-hidden="true"></i>
    <?php $totalChannels = $this->paginator->getTotalItemCount();?>
    <?php echo $this -> translate(array("%s channel", "%s channels", $totalChannels), $totalChannels)?>
</div>
<?php if ($totalChannels > 0):?>
    <?php $viewer = $this -> viewer();?>
    <ul class="ynvideochannel_channel_listing_items">
        <?php foreach($this -> paginator as $item):?>
        <li class="ynvideochannel_channel_listing_item clearfix">
            <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_channel_thumb_normal.png'; ?>
            <a href="<?php echo $item -> getHref()?>" class="ynvideochannel_channel_listing_item-bg" style="background-image: url('<?php echo $photo_url?>')">
            </a>

            <div class="ynvideochannel_channel_listing_item-info">
                <a class="ynvideochannel_channel_listing_item-title" href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a>

                <div class="ynvideochannel_channel_listing_item-category-description">
                    <span class="ynvideochannel_channel_listing_item-category">
                        <?php if ($item->category_id)
                            echo $this->partial('_category_breadcrumb.tpl', 'ynvideochannel', array('item' => $item));
                        ?>
                    </span>

                    <?php if($item->description): ?>
                    <span class="ynvideochannel_channel_listing_item-description">
                        &nbsp;-&nbsp;
                        <?php echo $this -> string() -> truncate(strip_tags($item -> description), 200);?>
                    </span>
                    <?php endif; ?>
                </div>

                <div class="ynvideochannel_channel_listing_item-count">
                    <span id="ynvideochannel_subscriber_count_<?php echo $item -> channel_id ?>">
                        <?php echo $this -> translate(array("%s subscriber", "%s subscribers", $item -> subscriber_count), $item -> subscriber_count)?>
                    </span>&nbsp;.&nbsp;
                    <span><?php echo $this -> translate(array("%s video", "%s videos", $item -> video_count), $item -> video_count)?></span>&nbsp;.&nbsp;
                    <span><?php echo $this -> translate(array("%s like", "%s likes", $item -> like_count), $item -> like_count)?></span>&nbsp;.&nbsp;
                    <span><?php echo $this -> translate(array("%s comment", "%s comments", $item -> comment_count), $item -> comment_count)?></span>
                </div>

                <?php
                $videos = $item -> getVideos(4);
                if(count($videos)):?>
                <ul class="ynvideochannel_channel_listing_videos">
                    <?php foreach($videos as $video):?>
                    <?php $photo_url = ($video->getPhotoUrl('thumb.normal')) ? $video->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_video_thumb_normal.png'; ?>
                    <li style="background-image: url('<?php echo $photo_url; ?>');">
                        <a href="<?php echo $video -> getHref()?>"></a>
                    </li>
                    <?php endforeach;?>
                </ul>

                <?php if(($item -> video_count - 4) > 0): ?>
                    <span class="ynvideochannel_channel_listing_videos-count">
                        +<?php echo $item -> video_count - 4; ?>
                    </span>
                <?php endif; ?>

                <?php endif;?>

            </div>

            <div class="ynvideochannel_channel_listing_item-actions">
                <div class="ynvideochannel_channel_listing_item-options">
                    <?php echo $this->partial('_channel_options.tpl', 'ynvideochannel', array('channel' => $item,'showEditDel' => true)); ?>
                </div>

                <?php if($viewer->getIdentity() != $item->owner_id): ?>
                <div class="ynvideochannel_channel_listing_item-subscribe">
                    <?php echo $this->partial('_subscribe_channel.tpl', 'ynvideochannel', array('item' => $item, 'user_id' => $viewer->getIdentity())); ?>
                </div>
                <?php endif; ?>
            </div>

        </li>
        <?php endforeach;?>
    </ul>

    <?php
        echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues
    )); ?>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('No channels found.');?>
        </span>
    </div>
<?php endif; ?>

<script type="text/javascript">
    ynvideochannelChannelOptions();
</script>