
<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php else: ?>

  <div style="padding: 10px;">

    <?php if( $this->status ): ?>
      <?php echo $this->translate('The subscription has been cancelled.') ?>
    <?php else: ?>
      <?php echo $this->translate('There was a problem cancelling the ' .
          'subscription. The message was:') ?>
      <?php echo $this->error ?>
    <?php endif; ?>

    <br />
    <br />
    <a href="javascript:void(0);" onclick="parent.Smoothbox.close(); return false">
      <?php echo $this->translate('close') ?>
    </a>

  </div>

<?php endif; ?>