<h2><?php echo $this->translate("Auction Plugin") ?></h2>
       <?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<br />

<div class="clear">
	<div class="settings">
	<?php echo $this->form->render($this )?>
		
	</div>
</div>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {
 display: table;
  height: 65px;
}
</style>