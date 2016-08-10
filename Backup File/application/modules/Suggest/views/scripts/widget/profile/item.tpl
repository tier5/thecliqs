<?php
$item = $this->item;
$wid = $this->wid;
?>
  
<div class="suggest-item" id="<?php echo $wid; ?>__suggest-item-profile_photo_suggest_<?php echo $item->getIdentity(); ?>" style="float: left;">
  
  <?php $ids[] = $item->getIdentity(); ?>
  
  <div class="photo">
  <?php
    if (!isset($item->photo_id)) {
      echo $this->htmlLink($item->getHref(), $this->htmlImage($this->baseUrl() . '/application/modules/Suggest/externals/images/nophoto/' . $item->getType() . '.png', '', array('class' => 'thumb_icon item_photo_' . $item->getType())));
    } else {
      echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'));
    }
  ?>
  </div>
  
  <div class="right">
    <div class="title"><?php echo $this->htmlLink($this->item->getHref(), $this->truncate($this->item->getTitle(), 12), array('title' => $this->item->getTitle())); ?></div>
    <div class="clr"></div>
    <div class="descr"><?php echo $this->translate('suggest_Suggest %s profile photo.', $item->getTitle()); ?></div>
    <div class="clr"></div>
    <a class="suggest-reject <?php echo $wid; ?>__reject" id="<?php echo $wid; ?>--profile_photo_suggest_<?php echo $item->getIdentity(); ?>" href="javascript:void(0)" onfocus="this.blur();"></a>
  </div>
  <div class="clr"></div>
    <?php
      $url = $this->url(array(
          'controller' => 'index',
          'action' => 'suggest-photo',
          'object_type' => $item->getType(),
          'object_id' => $item->getIdentity(),
        ), 'suggest_general');

      $label = $this->translate('suggest_Suggest Profile Photo');
      $params = array('class' => 'buttonlink suggest_widget_link smoothbox suggest_view_photo', 'target' => '_blank');

      echo $this->htmlLink($url, $label, $params);
    ?>
  <div class="clr"></div>
  
</div>