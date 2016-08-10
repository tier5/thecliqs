<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<div style="padding: 10px; padding-bottom: 0px;">
  <?php if( $this->error == 1 ): ?>
    <?php echo $this->translate('Embedding of videos has been disabled.') ?>
    <?php return ?>
  <?php elseif( $this->error == 2 ): ?>
    <?php echo $this->translate('Embedding of videos has been disabled for this video.') ?>
    <?php return ?>
  <?php elseif( !$this->video || $this->video->status != 1 ): ?>
    <?php echo $this->translate('The video you are looking for does not exist or has not been processed yet.') ?>
    <?php return ?>
  <?php endif; ?>

  <h3>
    <?php echo $this->translate("HTML Code"); ?>
  </h3>
  <br />
  <textarea cols="50" style="width: 100%" rows="4"><?php echo trim($this->embedCode);?></textarea>
  <br />
  <br />
  <div>
    <a href="javascript:void(0);" onclick="parent.Smoothbox.close();">
      <button><?php echo $this->translate('Close') ?></button>
    </a>
  </div>
</div>