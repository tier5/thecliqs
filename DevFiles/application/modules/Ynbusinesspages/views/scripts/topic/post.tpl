<div class="generic_layout_container layout_top">
  <div class="generic_layout_container layout_middle">
    <div class="headline">
<h2>
  <?php echo $this->business->__toString()." ".$this->translate("&#187; Discussions") ?>
</h2>
</div>
</div>
</div>

<div class="generic_layout_container layout_main">
  <div class="generic_layout_container layout_middle">
    <div class="generic_layout_container">
<h3>
  <?php echo $this->topic->__toString() ?>
</h3>
<br />
<?php if( $this->message ) echo $this->message ?>
<?php if( $this->form ) echo $this->form->render($this) ?>
</div>
</div>
</div>