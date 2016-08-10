
<div class="tabs popup_form" style="margin: 0; padding: 20px;">
<?php echo $this->form->render($this); ?>
</div>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>