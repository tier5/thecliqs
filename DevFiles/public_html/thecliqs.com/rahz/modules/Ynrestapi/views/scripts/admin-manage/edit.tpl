<h2><?php echo $this->translate("YN SE API Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class="settings">
    <?php echo $this->form->render($this) ?>
  </div>
</div>