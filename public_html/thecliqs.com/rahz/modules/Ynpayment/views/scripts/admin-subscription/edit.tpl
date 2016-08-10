<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php else: ?>
  <script type="text/javascript">
    parent.Smoothbox.close();
  </script>
<?php endif; ?>