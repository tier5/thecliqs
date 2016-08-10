<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>

<h2>
    <?php echo $this->translate('Profile Styler Plugin') ?>
</h2>

<div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render()?>
</div>


<div class='settings'>
  <?php echo $this->form->render($this); ?>
</div>