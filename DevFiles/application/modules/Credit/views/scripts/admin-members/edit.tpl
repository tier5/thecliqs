<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl  05.01.12 18:11 TeaJay $
 * @author     Taalay
 */
?>

<h2>
  <?php echo $this->translate('Credits Plugin') ?>&nbsp;&raquo;
  &nbsp;<a href="<?php echo $this->url(array('module' => 'credit', 'controller' => 'members'),'admin_default', true) ?>"><?php echo $this->translate("Members") ?></a>&nbsp;&raquo;
  &nbsp;<a href="<?php echo $this->user->getHref() ?>"><?php echo $this->user->getTitle() ?></a>
</h2>
<br />
<?php if( count($this->custom_nav) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->custom_nav)->render()
    ?>
  </div>
<?php endif; ?>

<br />

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>