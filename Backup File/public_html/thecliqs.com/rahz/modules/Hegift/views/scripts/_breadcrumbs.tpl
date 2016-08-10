<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _breadcrumbs.tpl  06.03.12 17:09 TeaJay $
 * @author     Taalay
 */
?>

<div class="create_own_gift_path">
  <a class="own_gift_path own-gift-home<?php if ($this->action == 'create' || $this->action == 'select-send') echo '-'.$this->type; else echo ' active'?> buttonlink" href="<?php echo $this->url(array(), 'hegift_own', true)?>">
    <span><?php echo $this->translate('HEGIFT_Choose Type')?></span>
  </a>
  <a class="own_gift_path own_gift_arrow_right buttonlink">&nbsp;</a>
  <a class="own_gift_path own-gift-create <?php if ($this->action == 'create') echo 'active';?> buttonlink">
    <span><?php echo $this->translate('HEGIFT_Create Gift')?></span>
  </a>
  <a class="own_gift_path own_gift_arrow_right buttonlink">&nbsp;</a>
  <a class="own_gift_path own-gift-send <?php if ($this->action == 'select-send') echo 'active';?> buttonlink">
    <span><?php echo $this->translate('HEGIFT_Select Friends and Send')?></span>
  </a>
</div>

<br />