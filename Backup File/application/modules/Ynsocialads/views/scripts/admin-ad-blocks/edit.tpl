<div class="tabs popup_form">
<?php echo $this->form->render($this); ?>
</div>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>