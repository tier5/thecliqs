<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  09.03.12 11:22 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  function send_gift(id) {
    if ($('browse_gifts_container')) {
      gift_manager.user_id = id;
      gift_manager.getGifts();
    } else {
      window.location.href = '<?php echo $this->url(array(), 'hegift_general')?>/index/user/'+id;
    }
  }
</script>

<div class="birthday-widget-container">
  <?php foreach ($this->birthdays as $birthday) : ?>
    <div class="birthday-item">
      <div class="photo">
        <?php echo $this->htmlLink($birthday['user']->getHref(), $this->itemPhoto($birthday['user'], 'thumb.icon'), array('target' => '_blank'));?>
      </div>
      <div class="right">
        <div class="title">
          <?php echo $this->htmlLink($birthday['user']->getHref(), $birthday['user']->getTitle(), array('target' => '_blank'));?>
        </div>
        <div class="clr"></div>
        <div class="descr">
          <span>
            <?php echo $this->translate('HEGIFT_birthday %s', $this->translate($birthday['when'])); ?>
          </span>
        </div>
        <div class="clr"></div>
        <?php echo $this->htmlLink('javascript:void(0)', ($birthday['sent']) ? $this->translate('HEGIFT_Send Again') : $this->translate('HEGIFT_Send Gift'), array('class' => 'buttonlink birthday_widget_link item_icon_gift', 'onclick' => 'send_gift('.$birthday['user']->getIdentity().')'));?>
      </div>
      <div class="clr"></div>
    </div>
  <?php endforeach; ?>
  <div class="clr"></div>
</div>