<?php
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynvideochannel/externals/scripts/jquery-1.10.2.min.js');
?>

<?php
    $playlist = $item = $this->playlist;
    $playlist_id = $playlist->getIdentity();
    $poster = $this->playlist->getOwner();
?>

<div class="ynvideochannel_playlist_detail_profile">
    <div class="ynvideochannel_playlist_detail_profile-title">
        <?php echo $playlist->getTitle() ?>
    </div>

    <div class="ynvideochannel_playlist_detail_profile-info clearfix">
        <div class="ynvideochannel_playlist_detail_profile-infoleft">
            <div class="ynvideochannel_playlist_detail_profile-owner-img">
                <?php echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon'), array()) ?>
            </div>

            <div class="ynvideochannel_playlist_detail_profile-categories-owner-date">
                <div class="ynvideochannel_playlist_detail_profile-categories">
                    <span><?php echo $this->translate('Category')?>:</span>
                    <?php if ($playlist->category_id):
                        echo $this->partial('_category_breadcrumb.tpl', 'ynvideochannel', array('item' => $playlist));
                    endif; ?>
                </div>
                <div class="ynvideochannel_playlist_detail_profile-owner">
                    <span><?php echo $this->translate('Posted by') ?></span>
                    <span><?php echo $this->htmlLink($poster, $poster->getTitle()) ?></span>
                </div>
                <div class="ynvideochannel_playlist_detail_profile-date">
                    <span><?php echo $this->timestamp($playlist->creation_date) ?></span>
                </div>
            </div>
        </div>

        <div class="ynvideochannel_playlist_detail_profile-count">
            <div class="ynvideochannel_playlist_detail_profile-count-item">
                <?php echo $this->translate(array('<span>%s</span> like', '<span>%s</span> likes', $playlist->like_count), $this->locale()->toNumber($playlist->like_count)) ?>
            </div>
            <div class="ynvideochannel_playlist_detail_profile-count-item">
                <?php echo $this->translate(array('<span>%s</span> comment', '<span>%s</span> comments', $playlist->comment_count), $this->locale()->toNumber($playlist->comment_count)) ?>
            </div>
            <div class="ynvideochannel_playlist_detail_profile-count-item">
                <?php echo $this->translate(array('<span>%s</span> view', '<span>%s</span> views', $playlist->view_count), $this->locale()->toNumber($playlist->view_count)) ?>
            </div>
        </div>
    </div>

    <!-- REUSE CODE OF VIDEO DETAIL -->
    <div class="ynvideochannel_video_detail-actions-description">
        <div class="ynvideochannel_video_detail-actions clearfix">
            <div class="ynvideochannel_addthis">
                <div class="addthis_sharing_toolbox"></div>
            </div>

            <div class="ynvideochannel_video_detail-actions-right">
                <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                    <?php echo $this->htmlLink(array(
                        'module'=>'activity',
                        'controller'=>'index',
                        'action'=>'share',
                        'route'=>'default',
                        'type'=>'ynvideochannel_playlist',
                        'id' => $playlist_id,
                        'format' => 'smoothbox'
                    ), '<i class="fa fa-share-alt"></i>'.$this->translate("Share"), array('class' => 'ynvideochannel_share_button smoothbox')); ?>

                    <?php $isLiked = $playlist->likes()->isLike($this->viewer()) ? 1 : 0; ?>
                    <a id="ynvideochannel_like_button" class="ynvideochannel_like_button <?php if( $isLiked ) echo 'ynvideochannel_liked' ?>" href="javascript:void(0);" onclick="onlike('<?php echo $playlist->getType() ?>', '<?php echo $playlist->getIdentity() ?>', <?php echo $isLiked ?>);">
                        <?php if( $isLiked ): ?>
                            <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Liked");?>
                        <?php else: ?>
                            <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Like");?>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <?php if ($playlist->isEditable() || $playlist->isDeletable()) :?>
                    <div class="ynvideochannel_video_detail-options">
                        <?php echo $this->partial('_playlist_options.tpl', 'ynvideochannel', array('playlist' => $playlist)); ?>
                    </div>
                <?php endif;?>
            </div>
        </div>

        <?php if ($playlist->description): ?>
            <div class="ynvideochannel_video_detail-description-block">
                <div class="ynvideochannel_video_view_description ynvideochannel_video_show_less" id="ynvideochannel_video_detail_moreless">

                    <div class="ynvideochannel_video_view_description-detail">
                        <div class="ynvideochannel_video_detail-descriptions">
                            <?php $description = $playlist->description;
                            $description = str_replace( "\r\n", "<br />", $description);
                            $description = str_replace( "\n", "<br />", $description);
                            echo $description; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ynvideochannel_show_less_more" style="display: none;">
                <a href="javascript:void(0)" class="ynvideochannel_link_more">
                    <?php echo $this->translate('View more') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynvideochannel.addthis.pubid', 'younet');?>" async="async"></script>

<script>
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
                    var html = '<a id="ynvideochannel_like_button" class="ynvideochannel_like_button ynvideochannel_liked" href="javascript:void(0);" onclick="unlike(\'<?php echo $playlist->getType()?>\', \'<?php echo $playlist->getIdentity() ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Liked'); ?></a>';
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
                    var html = '<a id="ynvideochannel_like_button" class="ynvideochannel_like_button" href="javascript:void(0);" onclick="like(\'<?php echo $playlist->getType()?>\', \'<?php echo $playlist->getIdentity() ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Like'); ?></a>';
                    $("ynvideochannel_like_button").outerHTML = html;
                }
            }
        }).send();
    }

    //CHECK VIEWMORE DESCRIPTION
    var desc_h = $$('.ynvideochannel_video_detail-descriptions').getHeight();
    if(desc_h > 54){
        $$('.ynvideochannel_show_less_more').show();
    }

    jQuery('.ynvideochannel_show_less_more a').bind('click', function() {
        if (jQuery(this).hasClass('ynvideochannel_link_more')) {
            jQuery(this).html('<?php echo $this->translate('View less') ?>');
            jQuery(this).removeClass('ynvideochannel_link_more');
            jQuery(this).addClass('ynvideochannel_link_less');
            jQuery('#ynvideochannel_video_detail_moreless').removeClass('ynvideochannel_video_show_less');
        } else {
            jQuery(this).html('<?php echo $this->translate('View more') ?>');
            jQuery(this).addClass('ynvideochannel_link_more');
            jQuery(this).removeClass('ynvideochannel_link_less');
            jQuery('#ynvideochannel_video_detail_moreless').addClass('ynvideochannel_video_show_less');
        }
    });

    ynvideochannelPlaylistOptions();
</script>



