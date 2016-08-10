<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: item.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>

<div class="suggest-item" id="<?php echo $this->wid; ?>__suggest-item-<?php echo $this->item->getGuid(); ?>" style="border: none;">
    <div class="only_photo">
      <?php
        if (!isset($this->item->photo_id)) {
          echo $this->htmlLink($this->item->getHref(), $this->htmlImage($this->baseUrl().'/application/modules/Suggest/externals/images/nophoto/'.$this->item->getType().'_normal.png', '', array('class' => 'thumb_normal item_photo_'.$this->item->getType())));
        } else {
          echo $this->htmlLink($this->item->getHref(), $this->itemPhoto($this->item, 'thumb.normal'));
        }
      ?>
    </div>
    <div class="clr"></div>
    <a class="suggest-reject <?php echo $this->wid; ?>__reject" id="<?php echo $this->wid; ?>--<?php echo $this->item->getGuid(); ?>" href="javascript:void(0)" onfocus="this.blur();"></a>  
</div>