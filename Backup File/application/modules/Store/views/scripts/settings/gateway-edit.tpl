<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: gateway-edit.tpl  5/7/12 6:21 PM mt.uulu $
 * @author     Mirlan
 */
?>
<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline store">
  <h2><?php echo $this->translate('Manage');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>

<div class="layout_right he-items">
  <ul class="he-item-list">
    <li>
      <div class="he-item-options">
        <?php echo $this->htmlLink(array('action' => 'gateway', 'page_id'=>$this->page->getIdentity()), $this->translate('Back'), array(
          'class' => 'buttonlink product_back')); ?>
        <br>
      </div>
    </li>
  </ul>
</div>

<div class="layout_middle settings">
  <?php echo $this->form->render($this) ?>
</div>