<?php echo $this->form->render($this) ?>

<?php if($this->isPost): ?>
<script type="text/javascript">
  parent.getPhoto();
  parent.Smoothbox.close();
</script>
<?php endif; ?>
