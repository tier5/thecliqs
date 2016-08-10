<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  18.02.12 14:40 TeaJay $
 * @author     Taalay
 */
?>

<div class="headline">
  <h2>
    <?php echo $this->translate('Virtual Gifts');?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<?php echo $this->render('_breadcrumbs.tpl');?>

<?php if ($this->type == 'video') : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('HEGIFT_Have to install ffmpeg, contact with administrator of server.'); ?>
    </span>
  </div>
<?php endif; ?>

<div class="layout_middle">
  <div class="gift-welcome-title">
    <h1><?php echo $this->translate('HEGIFT_Create and Send Own Gift.')?></h1>
    <p class="gift_description"><?php echo $this->translate('HEGIFT_You can create and send Own Gift. Gifts are photo, audio, and video.')?></p>
  </div>

  <div class="gift-cols">
    <div class="gift-col">
      <?php echo $this->htmlLink($this->url(array('action'=>'create', 'type' => 'photo'), 'hegift_own', true), '<img src="application/modules/Hegift/externals/images/big_gift_photo.png" class="image_focus gift-img" alt="" >');?>
      <h3><?php echo $this->translate('HEGIFT_Send photo gift.') . ', ' . $this->translate("HEGIFT_%s credit", $this->locale()->toNumber($this->photo_price))?></h3>
      <p><?php echo $this->translate('DESC_Create Photo Gift and Send')?></p>
    </div>

    <div class="gift-col">
      <?php echo $this->htmlLink($this->url(array('action'=>'create', 'type' => 'audio'), 'hegift_own', true), '<img src="application/modules/Hegift/externals/images/big_gift_audio.png" class="image_focus gift-img" alt="">');?>
      <h3><?php echo $this->translate('HEGIFT_Send audio gift.') . ', ' . $this->translate("HEGIFT_%s credit", $this->locale()->toNumber($this->audio_price))?></h3>
      <p><?php echo $this->translate('DESC_Create Audio Gift and Send')?></p>
    </div>

    <div class="gift-col">
      <?php echo $this->htmlLink($this->url(array('action'=>'create', 'type' => 'video'), 'hegift_own', true), '<img src="application/modules/Hegift/externals/images/big_gift_video.png" class="image_focus gift-img" alt="">');?>
      <h3><?php echo $this->translate('HEGIFT_Send video gift.') . ', ' . $this->translate("HEGIFT_%s credit", $this->locale()->toNumber($this->video_price))?></h3>
      <p><?php echo $this->translate('DESC_Create Video Gift and Send')?></p>
    </div>
  </div>
</div>