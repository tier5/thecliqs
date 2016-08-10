<?php
$item = $this->item;
$wid = $this->wid;

$this->headTranslate(array('Suggest %s to your friends'))
?>
  
<div class="suggest-item" id="<?php echo $wid; ?>__suggest-item-friend_<?php echo $item->getIdentity(); ?>" style="float: left;">
  
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
    <div class="descr"><?php echo $this->translate('SUGGEST_Help %s find more friends.', $item->getTitle()); ?></div>
    <div class="clr"></div>
    <a class="suggest-reject <?php echo $wid; ?>__reject" id="<?php echo $wid; ?>--friend_<?php echo $item->getIdentity(); ?>" href="javascript:void(0)" onfocus="this.blur();"></a>
  </div>
  <div class="clr"></div>
    <?php
      $url = 'javascript:void(0)';
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR ."modules" . DIRECTORY_SEPARATOR . "Suggest" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "scripts";
      $label = $this->translate('Suggest To Friends');
      $params = array('class' => 'buttonlink suggest_widget_link suggest_view_friend', 'onClick' => 'window.friends.friend_'.$item->getIdentity().'.box();' );
      echo $this->htmlLink($url, $label, $params);
    ?>
  <div class="clr"></div>

  <script type="text/javascript">
    HESuggest.scriptpath = <?php echo Zend_Json_Encoder::encode($path); ?>;
    en4.core.runonce.add(function(){
      if (!window.friends) {
        window.friends = {};
      }

      var options = {
        c: "window.friends.callback_<?php echo $item->getIdentity(); ?>.suggest",
        listType: "all",
        m: "suggest",
        l: "getSuggestItems",
        t: "<?php echo $this->translate("Suggest %s to your friends", $item->getTitle()); ?>",
        ipp: 30,
        nli: 0,
        params: {
          scriptpath: <?php echo Zend_Json_Encoder::encode($path); ?>,
          suggest_type: 'link_user',
          object_type: 'user',
          object_id: <?php echo (int)$item->getIdentity(); ?>
        }
      };

      window.friends.callback_<?php echo $item->getIdentity(); ?> = new FriendSuggest(<?php echo $item->getIdentity(); ?>);
      window.friends.friend_<?php echo $item->getIdentity(); ?> = new HEContacts(options);
    });
  </script>
  
</div>