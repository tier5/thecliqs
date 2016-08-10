<?php $item = $video = $this->video ?>
<?php $watchlaterTable = Engine_Api::_() -> getDbTable('watchlaters', 'ynultimatevideo'); ?>
<div class="ynultimatevideo_list_most_item_content clearfix">

        <?php if (Engine_Api::_()->ynultimatevideo()->canAddToPlaylist()): ?>
            <div class="list-view-video-action-add-playlist show-hide-action action-container">
                <a class="ynultimatevideo-action-link show-hide-btn" href="javascript:void(0)" title="<?php echo $this->translate('Add to playlist')?>">
                    <i class="fa fa-plus"></i>
                </a>
                <div class="ynultimatevideo-action-pop-up" style="display: none">
                    <div class="add-to-playlist-notices"></div>
                    <?php
                        $addedWatchlater = false;
                        if ($video -> getType() == 'ynultimatevideo_video'){
                            $addedWatchlater = $watchlaterTable->isAdded($video->getIdentity(), $this->viewer()->getIdentity());
                        }
                    ?>
                    <div class="video-action-add-playlist">
                        <div class="video-action-watch-later" onclick="ynultimatevideoAddToWatchLater(this, '<?php echo $video -> getIdentity() ?>');">
                            <?php if ($addedWatchlater): ?>
                                <i class="fa fa-ban"></i>
                                <?php echo $this->translate('Unwatched') ?>
                            <?php else: ?>
                                <i class="fa fa-play-circle"></i>
                                <?php echo $this->translate('Watch Later') ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="video-action-add-playlist dropdow-action-add-playlist">
                        <span><?php echo $this-> translate('add to') ?></span>
                        <?php $url = $this->url(array('action'=>'render-playlist-list', 'subject'=>$video->getGuid()),'ynultimatevideo_playlist', true)?>
                        <div rel="<?php echo $url;?>" class="video-loading add-to-playlist-loading" style="display: none;text-align: center">
                            <span class="ajax-loading">
                                <img src='application/modules/Ynultimatevideo/externals/images/loading.gif'/>
                            </span>
                        </div>
                        <div class="box-checkbox">
                            <?php echo $this->partial('_add_exist_playlist.tpl', 'ynultimatevideo', array('item' => $video)); ?>
                        </div>
                    </div>

                    <?php if (Engine_Api::_()->ynultimatevideo()->canCreatePlaylist()): ?>
                        <div class="video-action-dropdown ynultimatevideo-action-dropdown">
                            <a href="javascript:void(0);" onclick="ynultimatevideoAddNewPlaylist(this, '<?php echo $video->getGuid()?>');" class="ynultimatevideo-action-link add-to-playlist" data="<?php echo $video->getGuid()?>"><i class="fa fa-plus"></i><span><?php echo $this->translate('Add to new playlist')?></span></a>
                                <span class="play_list_span"></span>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        <?php endif;?>

    <div class='ynultimatevideo_options_block'>
        <?php
            if ($this->manageType == 'playlist'){
                echo $this->htmlLink(
                    Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                        'action' => 'remove',
                        'video_id' => $this->video->getIdentity()), null),
                        null, array('class' => 'ynultimatevideo_options_btn fa fa-remove smoothbox')
                    );
            }
        ?>

        <?php if($this->manageType == 'my'): ?>
            <span class="ynultimatevideo_options_btn"><i class="fa fa-pencil"></i></span>
            <div class="ynultimatevideo_options">
                <?php
                    echo $this->htmlLink(array(
                            'route' => 'ynultimatevideo_general',
                            'action' => 'edit',
                            'video_id' => $item->video_id
                        ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit video'), array('class' => 'icon_ynultimatevideo_edit'));
                ?>
                <?php
                    if ($item->status != 2) {
                        echo $this->htmlLink(array(
                            'route' => 'ynultimatevideo_general',
                            'action' => 'delete',
                            'video_id' => $item->video_id,
                            'format' => 'smoothbox'
                        ), '<i class="fa fa-trash"></i>'.$this->translate('Delete video'), array('class' => 'smoothbox icon_ynultimatevideo_delete'));
                    }
                ?>
            </div>
        <?php endif; ?>
    </div>

    <?php
        $photoUrl = $item ->getPhotoUrl('thumb.normal');
        if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynultimatevideo/externals/images/nophoto_video_thumb_normal.png';
    ?>

    <div class="ynultimatevideo_wrapper" style="background-image: url(<?php echo $photoUrl;?>)">
        <?php if ($item->duration): ?>
            <div class="ynultimatevideo_info_bot">
                <span class="ynultimatevideo_duration">
                    <?php echo '<i class="fa fa-clock-o"></i>'.$this->partial('_video_duration.tpl', 'ynultimatevideo', array('video' => $item)) ?>
                </span>
            </div>
        <?php endif ?>

        <div class="ynultimatevideo_background_opacity"></div>

        <div class="ynultimatevideo_most_play video-play-btn">
            <a href="<?php echo $item->getHref() ?>">
                <i class="fa fa-play"></i>
            </a>
        </div>
    </div>

    <div class="ynultimatevideo_content_padding">
        <?php
            echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'ynultimatevideo_title', 'title' => $item->getTitle()))
        ?>
        
        <?php if ($item->description && $item->status == 1): ?>
            <div class="ynultimatevideo_desc">
                <?php echo $this->string()->stripTags($item->description) ?>
            </div>
        <?php endif ?>

        <div class="video_info video_info_in_list">
            <?php
            $session = new Zend_Session_Namespace('mobile');
            if ($item->status == 0): ?>
            <div class="tip">
                    <span>
                        <?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.') ?>
                    </span>
            </div>
            <?php elseif ($item->status == 2): ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.') ?>
                </span>
            </div>
            <?php elseif ($item->status == 3): ?>
            <div class="tip">
                <span>
                    <?php
                    if($session -> mobile)
                        echo $this->translate('Video conversion failed.');
                    else
                        echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>');
                    ?>
                </span>
            </div>
            <?php elseif ($item->status == 4): ?>
            <div class="tip">
                <span>
                    <?php
                    if($session -> mobile)
                        echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG.');
                    else
                        echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                </span>
            </div>
            <?php elseif ($item->status == 5): ?>
            <div class="tip">
                <span>
                    <?php
                    if($session -> mobile)
                        echo $this->translate('Video conversion failed. Audio files are not supported.');
                    else
                        echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                </span>
            </div>
            <?php elseif ($item->status == 7): ?>
            <div class="tip">
                <span>
                    <?php
                    if($session -> mobile)
                        echo $this->translate('Video conversion failed. You may be over the site upload limit.');
                    else
                        echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($item->status == 1): ?>
            <div class="ynultimatevideo_listview_count_author_rating clearfix">
            <div class="ynultimatevideo_box_count">
                <span class="ynultimatevideo_count">
                    <?php
                        $viewCount = $video->view_count;
                        echo $this->translate(array('<span>%1$s</span> view', '<span>%1$s</span> views', $viewCount), $this->locale()->toNumber($viewCount));
                    ?>
                </span>
                <span class="ynultimatevideo_count">
                    <?php
                        $likeCount = $video->like_count;
                        echo $this->translate(array('<span>%1$s</span> like', '<span>%1$s</span> likes', $likeCount), $this->locale()->toNumber($likeCount));
                    ?>
                </span>
                <span class="ynultimatevideo_count">
                    <?php
                        $commentCount = $video->comment_count;
                        echo $this->translate(array('<span>%1$s</span> comment', '<span>%1$s</span> comments', $commentCount), $this->locale()->toNumber($commentCount));
                    ?>
                </span>
                <span class="ynultimatevideo_count">
                    <?php
                        $favoriteCount = $video->favorite_count;
                        echo $this->translate(array('<span>%1$s</span> favorite', '<span>%1$s</span> favorites', $favoriteCount), $this->locale()->toNumber($favoriteCount));
                    ?>
                </span>
            </div>

            <div class="ynultimatevideo_author_rating">
                <span class="ynultimatevideo_listview_author">
                    <?php $user = $video->getOwner() ?>
                    <span>
                        <?php echo $this->translate('by') ?>
                    </span>
                    <?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
                </span>
                <span class="ynultimatevideo_most_rating ynultimatevideo_rating">
                    <?php echo $this->partial('_video_rating.tpl', 'ynultimatevideo', array('rating' => $this->video->rating)); ?>
                </span>
            </div>

        </div>
        <?php endif ?>
    </div>
</div>