<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-09-14 17:07:11 taalay $
 * @author     Taalay
 */

?>

<?php if( !$this->video || $this->video->status !=1 ):
  echo $this->translate('The video you are looking for does not exist or has not been processed yet.');
  return; // Do no render the rest of the script in this mode
endif; ?>

<div class="layout_main">
  <div class='layout_middle video_view_container'>

    <div class="video_view video_view_container">
      <div class="video_embed">
        <?php echo $this->videoEmbedded ?>
      </div>
      <div class="video_date">
        <?php echo $this->translate('Posted') ?>
        <?php echo $this->timestamp($this->video->creation_date) ?>
      </div>
    </div>
  </div>
</div>
