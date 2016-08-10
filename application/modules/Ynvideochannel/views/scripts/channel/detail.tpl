<?php $viewer = $this -> viewer ?>
<?php $channel = $this->channel ?>

<div class="ynvideochannel_channel_detail">
    <?php $cover_url = ($channel->getCoverUrl('thumb.main')) ? $channel->getCoverUrl('thumb.main') : 'application/modules/Ynvideochannel/externals/images/nophoto_channel_cover.png'; ?>
    <div class="ynvideochannel_channel_detail-bg" style="background-image: url('<?php echo $cover_url?>')">
        <div class="ynvideochannel_channel_detail-bgopacity"></div>
        <?php $photo_url = ($channel->getPhotoUrl('thumb.normal')) ? $channel->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_channel_thumb_normal.png'; ?>
        <div class="ynvideochannel_channel_detail-thumb" style="background-image: url('<?php echo $photo_url?>')"></div>
        
        <div class='ynvideochannel_channel_detail-title'><a href="<?php echo $channel -> getHref()?>"><?php echo $channel -> getTitle()?></a></div>
        
        
        <div class="ynvideochannel_channel_detail-category-date-owner">
            <span class="ynvideochannel_channel_detail-category">
                <?php if ($channel->category_id)
                    echo $this->partial('_category_breadcrumb.tpl', 'ynvideochannel', array('item' => $channel));
                ?>
            </span>
            &nbsp;.&nbsp;
            <span class="ynvideochannel_channel_detail-date-owner">
                <?php echo $this -> translate("%1s by %2s", $this->timestamp(strtotime($channel->creation_date)), $channel -> getOwner());?>
            </span>
        </div>

        <div class="ynvideochannel_channel_detail-count">
            <span id="ynvideochannel_subscriber_count_<?php echo $channel -> channel_id ?>"><?php echo $this -> translate(array("%s subscriber", "%s subscribers", $channel -> subscriber_count), $channel -> subscriber_count)?></span>&nbsp;.&nbsp;
            <span><?php echo $this -> translate(array("%s video", "%s videos", $channel -> video_count), $channel -> video_count)?></span>&nbsp;.&nbsp;
            <span><?php echo $this -> translate(array("%s like", "%s likes", $channel -> like_count), $channel -> like_count)?></span>&nbsp;.&nbsp;
            <span><?php echo $this -> translate(array("%s comment", "%s comments", $channel -> comment_count), $channel -> comment_count)?></span>
        </div>

        <div class="ynvideochannel_channel_detail-btnsubscribe">
            <?php if($viewer->getIdentity() != $channel->owner_id && $viewer->getIdentity() != 0):
                echo $this->partial('_subscribe_channel.tpl', 'ynvideochannel', array('item' => $channel, 'user_id' => $viewer->getIdentity()));
            endif;?>
        </div>
    </div>

    <?php $description = $channel->description; 
        $description = str_replace( "\r\n", "<br />", $description);
        $description = str_replace( "\n", "<br />", $description);
    ?>
    <?php if($description): ?>
    <div class="ynvideochannel_channel_detail-description-block">
        <div class="ynvideochannel_channel_view_description ynvideochannel_channel_show_less" id="ynvideochannel_channel_detail_moreless">
            <div class="ynvideochannel_channel_view_description-all">
                <?php echo $description; ?>
            </div>
        </div>
    </div>

    <div class="ynvideochannel_show_less_more" style="display: none;">
        <a href="javascript:void(0)" class="ynvideochannel_link_more">
            <?php echo $this->translate('View more') ?>
        </a>
    </div>
    <?php endif; ?>
    
    <div class="ynvideochannel_channel_detail-actions clearfix">
        <div class="ynvideochannel_addthis">
            <div class="addthis_sharing_toolbox"></div>
        </div>
        
        <div class="ynvideochannel_channel_detail-actions-right">
                <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                <?php echo $this->htmlLink(array(
                'module'=>'activity',
                'controller'=>'index',
                'action'=>'share',
                'route'=>'default',
                'type'=>'ynvideochannel_channel',
                'id' => $channel -> getIdentity(),
                'format' => 'smoothbox'
                ), '<i class="fa fa-share-alt"></i>'.$this->translate("Share"), array('class' => 'ynvideochannel_share_button smoothbox')); ?>
                <?php $isLiked = $channel->likes()->isLike($this->viewer()) ? 1 : 0; ?>
                <a id="ynvideochannel_like_button" class="ynvideochannel_like_button <?php if( $isLiked ) echo 'ynvideochannel_liked' ?>" href="javascript:void(0);" onclick="onlike('<?php echo $channel->getType() ?>', '<?php echo $channel -> getIdentity() ?>', <?php echo $isLiked ?>);">
                    <?php if( $isLiked ): ?>
                    <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Liked");?>
                    <?php else: ?>
                    <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Like");?>
                    <?php endif; ?>
                </a>
                <?php endif ?>

            <div class="ynvideochannel_channel_video_detail_more">
                <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                <span class="ynvideochannel_channel_video_detail_more-btn"><i class="fa fa-chevron-down"></i></span>

                <ul class="ynvideochannel_channel_video_detail_more-explain">
                    <li>
                        <?php echo $this->htmlLink(
                        array(
                        'route'=>'ynvideochannel_general',
                        'action' => 'send-to-friends',
                        'id' => $channel -> getIdentity(),
                        'type' => 'channel'
                        ),
                        '<i class="fa fa-envelope"></i>'.$this->translate('Send to Friends'),
                        array(
                        'class' => 'smoothbox'
                        )
                        )?>
                    </li>

                    <?php
                        $url = $this->url(array(
                        'module' => 'core',
                        'controller' => 'report',
                        'action' => 'create',
                        'subject' => $channel -> getGuid()
                        ),'default', true);
                    ?>
                    <li>
                        <a href="javascript:void(0)" onclick="openPopup('<?php echo $url?>')"><i class="fa fa-bolt"></i><?php echo $this->translate('Report') ?></a>
                    </li>

                    <li class="ynvideochannel_block" style="display:none">
                        <form id="ynvideochannel_form_return_url" onsubmit="return false;">
                            <span id="global_content_simple">
                                <label class="ynvideochannel_popup_label"><?php echo $this->translate("URL")?></label>
                                <input style="max-width: 100%" type="text" id="ynvideochannel_return_url" class="ynvideochannel_return_url"/>
                                <br/>
                                <div class="ynvideochannel_center" style="padding-top: 10px">
                                    <a href="javascript:void(0)" onclick="closeSmoothbox()" class="ynvideochannel_bold_link">
                                        <button><?php echo $this->translate('Close')?></button>
                                    </a>
                                </div>
                            </span>
                        </form>
                    </li>
                </ul>
                <?php endif ?>
            </div>

            <div class="ynvideochannel_channel_detail-options"><?php echo $this->partial('_channel_options.tpl', 'ynvideochannel', array('channel' => $channel,'showEditDel' => true)); ?></div>
        </div>
    </div>
</div>

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynvideochannel.addthis.pubid', 'younet');?>" async="async"></script>
<script type="text/javascript">
    function onlike(itemType, itemId, isLiked) {
        if (isLiked) {
            unlike(itemType, itemId);
        } else {
            like(itemType, itemId);
        }
    }
    function like(itemType, itemId)
    {
        new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/like',
            method: 'post',
            data : {
                format: 'json',
                type : itemType,
                id : itemId,
                comment_id : 0
            },
            onSuccess: function(responseJSON, responseText) {
                if (responseJSON.status == true)
                {
                    var html = '<a id="ynvideochannel_like_button" class="ynvideochannel_like_button ynvideochannel_liked" href="javascript:void(0);" onclick="unlike(\'<?php echo $channel->getType()?>\', \'<?php echo $channel -> getIdentity() ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Liked'); ?></a>';
                    $("ynvideochannel_like_button").outerHTML = html;
                }
            },
            onComplete: function(responseJSON, responseText) {
            }
        }).send();
    }

    function unlike(itemType, itemId)
    {
        new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/unlike',
            method: 'post',
            data : {
                format: 'json',
                type : itemType,
                id : itemId,
                comment_id : 0
            },
            onSuccess: function(responseJSON, responseText) {
                if (responseJSON.status == true)
                {
                    var html = '<a id="ynvideochannel_like_button" class="ynvideochannel_like_button" href="javascript:void(0);" onclick="like(\'<?php echo $channel -> getType()?>\', \'<?php echo $channel -> getIdentity() ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Like'); ?></a>';
                    $("ynvideochannel_like_button").outerHTML = html;
                }
            }
        }).send();
    }

    function openPopup(url)
    {
        if(window.innerWidth <= 480)
        {
            Smoothbox.open(url, {autoResize : true, width: 300});
        }
        else
        {
            Smoothbox.open(url);
        }
    }
    ynvideochannelChannelOptions();

    //CHANNEL & VIDEO DETAIL MORE DROPDOWN
    $$('.ynvideochannel_channel_video_detail_more-btn').removeEvents('click').addEvent('click', function() {
        this.getParent('.ynvideochannel_channel_video_detail_more').toggleClass('explained');
    });

    $$('.ynvideochannel_channel_video_detail_more-btn').addEvent('outerClick',function(){
        var popup = this.getParent('.ynvideochannel_channel_video_detail_more');
        if (popup.hasClass('explained')){
            popup.removeClass('explained');
        }
    });

    //CHECK VIEWMORE DESCRIPTION
    var desc_h = $$('.ynvideochannel_channel_view_description-all').getHeight();
    if(desc_h > 54){
        $$('.ynvideochannel_show_less_more').show();
    }

    jQuery('.ynvideochannel_show_less_more a').bind('click', function() {
        if (jQuery(this).hasClass('ynvideochannel_link_more')) {
            jQuery(this).html('<?php echo $this->translate('View less') ?>');
            jQuery(this).removeClass('ynvideochannel_link_more');
            jQuery(this).addClass('ynvideochannel_link_less');
            jQuery('#ynvideochannel_channel_detail_moreless').removeClass('ynvideochannel_channel_show_less');
        } else {
            jQuery(this).html('<?php echo $this->translate('View more') ?>');
            jQuery(this).addClass('ynvideochannel_link_more');
            jQuery(this).removeClass('ynvideochannel_link_less');
            jQuery('#ynvideochannel_channel_detail_moreless').addClass('ynvideochannel_channel_show_less');
        }
    });

 </script>


