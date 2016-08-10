<?php $video = $this->video; ?>
<?php $watchlaterTable = Engine_Api::_() -> getDbTable('watchlaters', 'ynultimatevideo'); ?>
<li class="ynultimatevideo_list_most_item clearfix">
    <div class="ynultimatevideo_list_most_item_content clearfix">

        <?php
            $photoUrl = $video ->getPhotoUrl('thumb.profile');
            if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynultimatevideo/externals/images/nophoto_video_thumb_normal.png';
        ?>
        <div class="ynultimatevideo_wrapper" style="background-image: url(<?php echo $photoUrl; ?>)">
            <div class="ynultimatevideo_wrapper_casual" style="background-image: url(<?php echo $photoUrl; ?>); display:none">
            </div>

            <div class="ynultimatevideo_info_casual" style="display:none">
                <?php
                    echo $this->htmlLink($video->getHref(), $video->getTitle(), array('class' => 'ynultimatevideo_title', 'title' => $video->getTitle()))
                ?>

                <?php $user = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($this->video) ?>
                <?php if ($user) : ?>
                <span class="ynultimatevideo_author_name">
                    <span>
                        <?php echo $this->translate('by') ?>
                    </span>
                    <?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
                </span>
                <?php endif; ?>

                <div class="ynultimatevideo_play_btn_casual">
                    <a href="<?php echo $video->getHref() ?>">
                        <i class="fa fa-play"></i>
                    </a>
                </div>
            </div>


            <div class="ynultimatevideo_info_bot">
                <span class="ynultimatevideo_duration">
                    <?php if ($video->duration): ?>
                        <?php echo '<i class="fa fa-clock-o"></i>'.$this->partial('_video_duration.tpl', 'ynultimatevideo', array('video' => $video)) ?>
                    <?php else: ?>
                        <i class="fa fa-clock-o"></i>00:00
                    <?php endif ?>
                </span>

                <span class="ynultimatevideo_most_rating ynultimatevideo_rating">
                    <?php echo $this->partial('_video_rating.tpl', 'ynultimatevideo', array('rating' => $video->rating)); ?>
                </span>
            </div>

            <div class="ynultimatevideo_background_opacity"></div>

            <div class="ynultimatevideo_most_play video-play-btn">
                <a href="<?php echo $video->getHref() ?>">
                    <i class="fa fa-play"></i>
                </a>
            </div>
        </div>

        <?php if (!$this->isLogAsBusiness): ?>
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
        <?php endif;?>

        <div class="ynultimatevideo_content_padding">
            <?php
                echo $this->htmlLink($video->getHref(), $video->getTitle(), array('class' => 'ynultimatevideo_title', 'title' => $video->getTitle()))
            ?>

            <div class="ynultimatevideo_author clearfix">
                <?php $user = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($this->video) ?>
                <?php if ($user) : ?>
                <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'ynultimatevideo_img_owner clearfix')) ?>

                <span class="ynultimatevideo_author_name">
                    <span>
                        <?php echo $this->translate('by') ?>
                    </span>
                    <?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
                </span>
                <?php endif; ?>

                <span class="ynultimatevideo_views">
                        <span class="ynultimatevideo_count">
                            <?php echo '<i class="fa fa-eye"></i>'.$this->translate(array('%1$s', '%1$s', $video->view_count), $this->locale()->toNumber($video->view_count)) ?>
                        </span>
                        <span class="ynultimatevideo_count">
                            <?php
                                $likeCount = $video->likes()->getLikeCount();
                                echo '<i class="fa fa-heart"></i>'.$this->translate(array('%1$s', '%1$s', $likeCount), $this->locale()->toNumber($likeCount));
                            ?>
                        </span>
                         <span class="ynultimatevideo_count">
                            <?php
                                $commentCount = $video->comments()->getCommentCount();
                                echo '<i class="fa fa-comments"></i>'.$this->translate(array('%1$s', '%1$s', $commentCount), $this->locale()->toNumber($commentCount));
                            ?>
                        </span>
                </span>
            </div>

            <?php if ($video->description): ?>
                <div class="ynultimatevideo_desc">
                    <?php echo $this->string()->stripTags($video->description) ?>
                </div>
            <?php endif ?>


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
                </div>

                <div class="ynultimatevideo_author_rating">
                    <span class="ynultimatevideo_listview_author">
                        <span>
                            <?php echo $this->translate('by') ?>
                        </span>
                        <?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
                    </span>
                    <span class="ynultimatevideo_most_rating ynultimatevideo_rating">
                        <?php echo $this->partial('_video_rating.tpl', 'ynultimatevideo', array('rating' => $video->rating)); ?>
                    </span>
                </div>

            </div>
        </div>
    </div>
</li>
