<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>
<?php
    $owner = $this->video->getOwner();
    $item = $this->video;
?>
<span class="video_views">
    <?php echo $this->translate('Created on'); ?>
    &nbsp;<?php echo $this->timestamp(strtotime($this->video->creation_date)) ?>
    <?php if ($owner->getIdentity()) : ?>
        &nbsp;<?php echo $this->translate('by %s', $owner->__toString()) ?>
    <?php endif; ?>
    |&nbsp;    
    <?php
        $viewCount = $item->view_count;
        echo $this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$viewCount)),
        $this->translate(array(' view', ' views', $viewCount));
    ?>
    |&nbsp;
    <?php
        $likeCount = $item->like_count;
        echo $this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$likeCount)),
        $this->translate(array(' like', ' likes', $likeCount));
    ?>
    |&nbsp;
    <?php
        $commentCount = $item->comment_count;
        echo $this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$commentCount)),
        $this->translate(array(' comment', ' comments', $commentCount));
    ?>
    |&nbsp;
    <?php
        $favoriteCount = $item->comment_count;
        echo $this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$favoriteCount)),
        $this->translate(array(' favorite', ' favorites', $favoriteCount));
    ?>
</span>