<?php
/**
* YouNet Company
*
* @category   Application_Extensions
* @package    Ynultimatevideo
* @author     YouNet Company
*/
?>

<div class="ynultimatevideo_featured_item ms-slide">
    <?php
        $poster = $this->video->getOwner();
        $photoUrl = $this->video ->getPhotoUrl('thumb.main');
        if (!$photoUrl) $photoUrl = $this->baseUrl().'/application/modules/Ynultimatevideo/externals/images/nophoto_video_thumb_normal.png';
    ?>
    <img src="" data-src="<?php echo $photoUrl; ?>" alt="<?php echo $this->video->title ?>"/>
    
    <div class="ynultimatevideo_btn_time_box">
        <div class="ynultimatevideo_video-play-btn video-play-btn">
            <a href="<?php echo $this->video->getHref() ?>">
                <i class="fa fa-play"></i>
            </a>
        </div>
        <?php if (isset($this->video->duration) && $this->video->duration > 0): ?>
            <span class="ynultimatevideo_video_time video-time"><?php echo ($this -> video -> duration >= 3600) ? gmdate("G:i:s", $this -> video -> duration) : gmdate("i:s", $this -> video -> duration) ?></span>
        <?php endif; ?>
    </div>


    <div class="ynultimatevideo_featured_main_info">
        <?php echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon'), array('class' => 'ynultimatevideo_img_owner clearfix')) ?>
        
        <?php echo $this->htmlLink($this->video->getHref(), $this->video->title, array('title' => $this->string()->stripTags($this->video->title), 'class' => 'ynultimatevideo_title')) ?>
        <?php $item = $this->video; ?>
        
        <div class="ynultimatevideo_featured_info_detail">
            <span class="ynultimatevideo_owner_name">
                <?php echo $this->translate('by ')?>
                <?php echo $this->htmlLink($poster->getHref(), htmlspecialchars ($this->string()->truncate($poster->getTitle(), 20)), array('title' => $poster->getTitle()))?>
            </span>
            
            <span class="ynultimatevideo_featured_count">
                <u>&nbsp;&nbsp;●&nbsp;&nbsp;</u>
                <?php
                    $viewCount = $item->view_count;
                    echo $this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$viewCount)),
                    $this->translate(array('<b> view</b>', '<b> views</b>', $viewCount));
                ?>
            </span>

            <span class="ynultimatevideo_featured_count">
                <u>&nbsp;&nbsp;●&nbsp;&nbsp;</u>
                <?php
                    $likeCount = $item->like_count;
                    echo $this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$likeCount)),
                    $this->translate(array('<b> like</b>', '<b> likes</b>', $likeCount));
                ?>
            </span>

            <span class="ynultimatevideo_featured_count">
                <u>&nbsp;&nbsp;●&nbsp;&nbsp;</u>
                <?php
                    $commentCount = $item->comment_count;
                    echo $this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$commentCount)),
                    $this->translate(array('<b> comment</b>', '<b> comments</b>', $commentCount));
                ?>
            </span>

            <span class="ynultimatevideo_featured_rating ynultimatevideo_rating">
                <?php echo $this->partial('_video_rating.tpl', 'ynultimatevideo', array('rating' => $this->video->rating)); ?>
            </span>
        </div>
    </div>


    <div class="ynultimatevideo_featured_main_info_v">
        <?php echo $this->htmlLink($this->video->getHref(), $this->video->title, array('title' => $this->string()->stripTags($this->video->title), 'class' => 'ynultimatevideo_title')) ?>

        <?php echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon'), array('class' => 'ynultimatevideo_img_owner clearfix')) ?>
        
        <div class="ynultimatevideo_featured_info_detail">
            <span class="ynultimatevideo_owner_name">
                <?php echo $this->translate('by ')?>
                <?php echo $this->htmlLink($poster->getHref(), htmlspecialchars ($this->string()->truncate($poster->getTitle(), 20)), array('title' => $poster->getTitle()))?>
            </span>
            

            <div class="ynultimatevideo_featured_info_detail_v">
                <span class="ynultimatevideo_featured_rating ynultimatevideo_rating">
                    <?php echo $this->partial('_video_rating.tpl', 'ynultimatevideo', array('rating' => $this->video->rating)); ?>
                </span>
    
                <span class="ynultimatevideo_featured_count">
                    <i class="fa fa-eye"></i>
                    <?php
                        echo $this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$this->video->view_count));
                    ?>
                </span>

                <span class="ynultimatevideo_featured_count">
                    <i class="fa fa-heart"></i>
                    <?php
                        echo $this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$this->video->like_count));
                    ?>
                </span>

                <span class="ynultimatevideo_featured_count">
                    <i class="fa fa-comments"></i>
                    <?php
                        echo $this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$this->video->comment_count));
                    ?>
                </span>
            </div>


        </div>
    </div>

    
    <div class="ynultimatevideo_featured_infomation ms-thumb">  
        <div class="ynultimatevideo_featured_thumb">
            <?php
                $thumbPhotoUrl = $this->video ->getPhotoUrl('thumb.normal');
                if (!$thumbPhotoUrl) $thumbPhotoUrl = $this->baseUrl().'/application/modules/Ynultimatevideo/externals/images/nophoto_video_thumb_normal.png';
            ?>
            <img src="<?php echo $thumbPhotoUrl; ?>" alt="">
        </div>
    </div>

</div>



