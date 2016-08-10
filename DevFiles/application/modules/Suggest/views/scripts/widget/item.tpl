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

<div class="suggest-item" id="<?php echo $this->wid; ?>__suggest-item-<?php echo $this->item->getGuid(); ?>">
  
  <div class="photo">
    <?php

      $link = $this->htmlLink($this->item->getHref(), $this->htmlImage($this->baseUrl().'/application/modules/Suggest/externals/images/nophoto/'.$this->item->getType().'.png', '', array('class' => 'thumb_icon item_photo_'.$this->item->getType())));

      try { 
		$new_link = $this->htmlLink($this->item->getHref(), $this->itemPhoto($this->item, 'thumb.icon'));
     	if ($new_link){
			$link = $new_link; 	
		}
	  } catch (Exception $e){}

      echo $link;

    ?>
  </div>

  <div class="right">
    
    <div class="title">
      <?php echo $this->htmlLink($this->item->getHref(), $this->truncate($this->item->getTitle(), 12), array('title' => $this->item->getTitle())); ?>
    </div>
    
    <div class="clr"></div>

    <div class="descr">
      <span>
        <?php echo $this->suggestDetails($this->item); ?>
      </span>
    </div>

    <div class="clr"></div>

    <?php echo $this->partial('widget/options.tpl', 'suggest', array('object' => $this->item)); ?>
    
    <div class="clr"></div>
    
    <a class="suggest-reject <?php echo $this->wid; ?>__reject" id="<?php echo $this->wid; ?>--<?php echo $this->item->getGuid(); ?>" href="javascript:void(0)" onfocus="this.blur();"></a>
    
  </div>
  
  <div class="clr"></div>

</div>