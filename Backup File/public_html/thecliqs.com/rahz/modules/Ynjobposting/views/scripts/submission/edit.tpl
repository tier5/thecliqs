<h3>
	<a title="<?php echo $this->company->name;?>" href="<?php echo $this->company->getHref();?>"><?php echo $this->string()->truncate($this->string()->stripTags($this->company->name), 30); ?></a> &#187; <?php echo $this->translate("Edit Submission Form");?>
</h3>

<div class='global_form'>
  <?php echo $this->form->render($this) ?>
</div>