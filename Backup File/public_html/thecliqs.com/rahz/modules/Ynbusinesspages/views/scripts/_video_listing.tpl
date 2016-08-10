<div class="ynvideo_thumb_wrapper video_thumb_wrapper">
    <?php if ($this->video->duration): ?>
        <?php echo $this->partial('_video_duration.tpl', 'ynbusinesspages', array('video' => $this->video)) ?>
    <?php endif ?>
    <?php
    if ($this->video->photo_id) 
    {
        echo $this->htmlLink($this->video->getHref(), $this->itemPhoto($this->video, 'thumb.normal'));
    } 
    else 
    {
        echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
    }
    ?>
</div>
<?php 
    echo $this->htmlLink($this->video->getHref(), 
            $this->string()->truncate($this->video->getTitle(), 30), 
            array('class' => 'ynvideo_title', 'title' => $this->video->getTitle())) 
?>

<div class="video_author">
    <?php $user = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($this->video) ?>
    <?php if ($user) : ?>
        <?php echo $this->translate('By') ?>
        <?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
    <?php endif; ?>
    <?php 
    	$session = new Zend_Session_Namespace('mobile');
		 if(!$session -> mobile)
		 {
    ?>
    |
    <?php } ?>
    <span class="video_views">
        <?php if (!isset($this->infoCol) || ($this->infoCol == 'view')) : ?>
            <?php echo $this->translate(array('%1$s view', '%1$s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
        <?php else : ?>
            <?php if ($this->infoCol == 'like') : ?>
                <?php
                    $likeCount = $this->video->likes()->getLikeCount();
                    echo $this->translate(array('%1$s like', '%1$s likes', $likeCount), $this->locale()->toNumber($likeCount));
                ?>
            <?php elseif ($this->infoCol == 'comment') : ?>
                <?php
                    $commentCount = $this->video->comments()->getCommentCount();
                    echo $this->translate(array('%1$s comment', '%1$s comments', $commentCount), $this->locale()->toNumber($commentCount));
                ?>
            <?php elseif ($this->infoCol == 'favorite') : ?>
            <?php
                echo $this->translate(array('%1$s favorite', '%1$s favorites', $this->video->favorite_count), $this->locale()->toNumber($this->video->favorite_count));
            ?>
            <?php endif; ?>
        <?php endif; ?>
    </span>
</div>
<?php 
    echo $this->partial('_video_rating_big.tpl', 'ynbusinesspages', array('video' => $this->video));
?>