<?php
    $playlist = $this->playlist;
    $playlist_id = $playlist->getIdentity();
    $poster = $this->playlist->getOwner();
?>

<div class="ynvideochannel_playlist_detail_profile">
    <div class="ynvideochannel_playlist_detail_profile-title">
        <?php echo $playlist->getTitle() ?>
    </div>

    <div class="ynvideochannel_playlist_detail_profile-info clearfix">
        <div class="ynvideochannel_playlist_detail_profile-owner-img">
            <?php echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon'), array()) ?>
        </div>

        <div class="ynvideochannel_playlist_detail_profile-categories-owner-date">
            <div class="ynvideochannel_playlist_detail_profile-categories">
                <span><?php echo $this->translate('Category')?>:</span>
                <span><?php echo $playlist->getCategory() ?></span>
            </div>
            <div class="ynvideochannel_playlist_detail_profile-owner">
                <span><?php echo $this->translate('Posted by') ?></span>
                <span><?php echo $this->htmlLink($poster, $poster->getTitle()) ?></span>
            </div>
            <div class="ynvideochannel_playlist_detail_profile-date">
                <span><?php echo $this->timestamp($playlist->creation_date) ?></span>
            </div>
        </div>

        <div class="ynvideochannel_playlist_detail_profile-button">
            <?php echo $this->translate(array('%s like', '%s likes', $playlist->like_count), $this->locale()->toNumber($playlist->like_count)) ?>
            <?php echo $this->translate(array('%s comment', '%s comments', $playlist->comment_count), $this->locale()->toNumber($playlist->comment_count)) ?>
            <?php echo $this->translate(array('%s view', '%s views', $playlist->view_count), $this->locale()->toNumber($playlist->view_count)) ?>
        </div>
    </div>

    <div class="ynvideochannel_playlist_detail_profile-description-actions">
        <div class="ynvideochannel_playlist_detail_profile-addthis">
            <div class="addthis_sharing_toolbox"></div>
        </div>

        <div class="ynvideochannel_playlist_detail_profile-actions">
            <?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
                <?php echo $this->htmlLink(array(
                    'module'=>'activity',
                    'controller'=>'index',
                    'action'=>'share',
                    'route'=>'default',
                    'type'=>'ynvideochannel_playlist',
                    'id' => $playlist_id,
                    'format' => 'smoothbox'
                ), '<i class="fa fa-share-alt"></i>'.$this->translate("Share"), array('class' => 'smoothbox')); ?>

                <?php $isLiked = $playlist->likes()->isLike($this->viewer()) ? 1 : 0; ?>
                <a id="ynvideochannel_like_button" href="javascript:void(0);" onclick="onlike('<?php echo $playlist->getType() ?>', '<?php echo $playlist->getIdentity() ?>', <?php echo $isLiked ?>);">
                    <?php if( $isLiked ): ?>
                        <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Liked");?>
                    <?php else: ?>
                        <?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Like");?>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            <?php if ($playlist->isEditable() || $playlist->isDeletable()) :?>
                <div>
                    <span><i class="fa fa-cog"></i></span>
                    <div>
                        <?php if ($playlist->isEditable()) :?>
                            <a href="javascript:void(0);"><i class="fa fa-pencil-square-o"></i><?php echo $this->translate('Edit')?></a>
                        <?php endif;?>

                        <?php if ($playlist->isDeletable()) :?>
                            <a href="javascript:void(0);"><i class="fa fa-trash"></i><?php echo $this->translate('Delete')?></a>
                        <?php endif;?>
                    </div>
                </div>
            <?php endif;?>
        </div>

    </div>
    <?php if ($playlist->description): ?>
        <div class="ynvideochannel_playlist_detail_profile-description">
            <p><?php echo $playlist->description ?></p>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynvideochannel.addthis.pubid', 'younet');?>" async="async"></script>