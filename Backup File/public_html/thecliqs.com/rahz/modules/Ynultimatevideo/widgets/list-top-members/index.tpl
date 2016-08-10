<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<ul class="ynultimatevideo_topmembers clearfix">
    <?php foreach ($this->videoSignatures as $signature): ?>
       <?php
            $user = Engine_Api::_()->user()->getUser($signature->user_id);
        ?>
        
        <?php 
           $photoUrl = $user ->getPhotoUrl(thumb.profile);
           if (!$photoUrl)  $photoUrl = $this->layout()->staticBaseUrl.'application/modules/Ynultimatevideo/externals/images/nophoto_user_thumb_profile.png';
        ?>

        <?php if ($user->getIdentity()) : ?>
            <li class="ynultimatevideo_topmembers_item">
                <div class="ynultimatevideo_topmembers_img">
                    <a href="<?php echo $user->getHref() ?>" class="clearfix" style="background-image: url(<?php echo $photoUrl; ?>)"></a>
                </div>
                
                <div class="ynultimatevideo_topmembers_info">
                    <div class="ynultimatevideo_topmembers_title">
                        <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
                    </div>

                    <div class="ynultimatevideo_topmembers_count">
                        <?php echo $this->translate(array('%1$s video', '%1$s videos', $signature->video_count),
                                $this->locale()->toNumber($signature->video_count))?>
                    </div>
                </div>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>