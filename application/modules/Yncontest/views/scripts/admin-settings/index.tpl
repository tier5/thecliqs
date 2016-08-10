<h2><?php echo $this->translate("Contest Plugin") ?></h2>

<!-- admin main menu -->
<?php echo $this->content()->renderWidget('yncontest.admin-main-menu') ?>

<div class='clear'>
  <div class='settings'>

    <?php echo $this->form->render($this); ?>
  </div>
</div>
