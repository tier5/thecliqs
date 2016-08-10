<div class="suggest-object">
  <div class="photo">
    <?php echo $this->htmlLink($this->suggest->getObjectHref(), $this->itemPhoto($this->object, null, '', array('height' => '110px'))); ?>
  </div>

  <div class="info">
    <div class="description">
      <?php echo $this->suggest->getDescription(); ?>
    </div>
    <div class="clr"></div>
    <?php echo $this->suggestOptions($this->suggest); ?>
  </div>
  
  <div class="clr"></div>
</div>