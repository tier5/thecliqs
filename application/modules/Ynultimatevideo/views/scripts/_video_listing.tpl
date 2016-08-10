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
    $photoUrl = $this-> video ->getPhotoUrl('thumb.profile');
    if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynultimatevideo/externals/images/nophoto_video_thumb_normal.png';
?>
<div class="ynultimatevideo_wrapper" style="background-image: url(<?php echo $photoUrl; ?>)">
    
    <div class="ynultimatevideo_info_bot">
        <span class="ynultimatevideo_duration">
            <?php if ($this->video->duration): ?>
                <?php echo '<i class="fa fa-clock-o"></i>'.$this->partial('_video_duration.tpl', 'ynultimatevideo', array('video' => $this->video)) ?>
            <?php endif ?>
        </span>
        
        <span class="ynultimatevideo_most_rating ynultimatevideo_rating">
            <?php echo $this->partial('_video_rating.tpl', 'ynultimatevideo', array('rating' => $this->video->rating)); ?>
        </span>
    </div>

    <div class="ynultimatevideo_background_opacity"></div>

    <div class="ynultimatevideo_most_play video-play-btn">
        <a href="<?php echo $this->video->getHref() ?>">
            <i class="fa fa-play"></i>
        </a>
    </div>
</div>


<?php
    echo $this->htmlLink($this->video->getHref(), $this->video->getTitle(), array('class' => 'ynultimatevideo_title', 'title' => $this->video->getTitle()))
?>

<div class="ynultimatevideo_author">
    <?php $user = $this->video->getOwner() ?>
    <?php if ($user) : ?>
        <span>
            <?php echo $this->translate('By') ?>
        </span>
        <?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
    <?php endif; ?>

</div>

<span class="ynultimatevideo_views">
    <span class="ynultimatevideo_count">
        <?php echo '<i class="fa fa-eye"></i>'.$this->translate(array('%1$s', '%1$s', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
    </span>
    <span class="ynultimatevideo_count">
        <?php
            $likeCount = $this->video->likes()->getLikeCount();
            echo '<i class="fa fa-heart"></i>'.$this->translate(array('%1$s', '%1$s', $likeCount), $this->locale()->toNumber($likeCount));
        ?>
    </span>
     <span class="ynultimatevideo_count">
        <?php
            $commentCount = $this->video->comments()->getCommentCount();
            echo '<i class="fa fa-comments"></i>'.$this->translate(array('%1$s', '%1$s', $commentCount), $this->locale()->toNumber($commentCount));
        ?>
    </span>
</span>
