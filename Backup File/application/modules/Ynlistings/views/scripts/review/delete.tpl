<?php if ($this->error) : ?>
<div class="tip">
    <span><?php echo $this->message?></span>
</div>
<?php else :?>
<?php echo $this->form->render($this) ?>
<?php endif; ?>