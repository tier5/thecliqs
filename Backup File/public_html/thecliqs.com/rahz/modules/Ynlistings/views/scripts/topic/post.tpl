<h2>
  <?php echo $this->listing->__toString() ?>
  <?php echo $this->translate(' &#187; Discussions');?>
</h2>

<h3>
  <?php echo $this->topic->__toString() ?>
</h3>
<br />
<?php if( $this->message ) echo $this->message ?>

<?php if( $this->form ) echo $this->form->render($this) ?>