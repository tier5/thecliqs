<?php $item = $this->item; ?>
<?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') :
    'application/modules/Ynvideochannel/externals/images/nophoto_channel_thumb_normal.png'; ?>
<li class="ynvideochannel_manage_channel_item">
    <a href="<?php echo $item -> getHref()?>" class="ynvideochannel_manage_channel_item-bg" style="background-image: url('<?php echo $photo_url?>')"></a>
    <?php if($this -> showOption) : ?>
    <div class="ynvideochannel_manage_channel_item-options">
        <?php echo $this->partial('_channel_options.tpl', 'ynvideochannel', array('channel' => $item, 'showEditDel' => true)); ?>
    </div>
    <?php endif ?>
    <div class="ynvideochannel_manage_channel_item-info">
        <a class="ynvideochannel_manage_channel_item-title" href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a>

        <div class="ynvideochannel_manage_channel_item-category-description">
            <span class="ynvideochannel_manage_channel_item-category">
                <?php if ($item->category_id)
                    echo $this->partial('_category_breadcrumb.tpl', 'ynvideochannel', array('item' => $item));
                ?>
            </span>

            <?php if ($item -> description): ?>
            <span class="ynvideochannel_manage_channel_item-description">
                &nbsp;-&nbsp;
                 <?php echo $this -> string() -> truncate(strip_tags($item -> description), 300);?>
            </span>
            <?php endif; ?>
        </div>

        <div class="ynvideochannel_manage_channel_item-count">
            <span id="ynvideochannel_subscriber_count_<?php echo $item -> channel_id ?>">
                <?php echo $this -> translate(array("%s subscriber", "%s subscribers", $item -> subscriber_count), $item -> subscriber_count)?>
            </span>&nbsp;.&nbsp;
            <span><?php echo $this -> translate(array("%s video", "%s videos", $item -> video_count), $item -> video_count)?></span>&nbsp;.&nbsp;
            <span><?php echo $this -> translate(array("%s like", "%s likes", $item -> like_count), $item -> like_count)?></span>&nbsp;.&nbsp;
            <span><?php echo $this -> translate(array("%s comment", "%s comments", $item -> comment_count), $item -> comment_count)?></span>
        </div>
    </div>

    <div class="ynvideochannel_manage_channel_item-btn-unsubscribe" style="display: none;">
        <?php echo $this->htmlLink(array(
            'route' => 'ynvideochannel_channel',
            'action' => 'unsubscribe',
            'channel_id' => $item->getIdentity(),
            'format' => 'smoothbox'
            ), $this->translate("unsubscribe"), array('class' => 'smoothbox icon_ynvideochannel_unsubscribe'));
        ?>
    </div>
</li>