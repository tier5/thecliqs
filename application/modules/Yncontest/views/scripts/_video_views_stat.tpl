<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<?php
    $owner = $this->video->getOwner();
?>
<span class="video_views">
    <?php echo $this->translate('Created on'); ?>
    &nbsp;<?php echo $this->timestamp(strtotime($this->video->start_date)) ?>
    <?php if ($owner->getIdentity()) : ?>
        &nbsp;<?php echo $this->translate('by %s', $owner->__toString()) ?>
    <?php endif; ?>
    |&nbsp;    
    <?php echo $this->translate(array('%1$s view', '%1$s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
    |&nbsp;
    <?php echo $this->translate(array('%1$s comment', '%1$s comments', $this->video->comments()->getCommentCount()), $this->locale()->toNumber($this->video->comments()->getCommentCount())) ?>
    |&nbsp;
    <?php echo $this->translate(array('%1$s like', '%1$s likes', $this->video->likes()->getLikeCount()), $this->locale()->toNumber($this->video->likes()->getLikeCount())) ?>
    |&nbsp;
    <?php echo $this->translate(array('%1$s favorite', '%1$s favorites', $this->video->favorite_count), $this->locale()->toNumber($this->video->favorite_count)) ?>
</span>