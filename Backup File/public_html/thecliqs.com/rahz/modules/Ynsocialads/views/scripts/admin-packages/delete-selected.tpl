<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class="settings">
<div class='global_form'>
  <?php if ($this->ids):?>
  <form method="post">
    <div>
      <h3><?php echo $this->translate("Delete the selected packages?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to delete the %d packages?", $this->count) ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value='true'/>
        <input type="hidden" name="ids" value="<?php echo $this->ids?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
        <a href='<?php echo $this->url(array('action' => 'index', 'id' => null));?>'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
  <?php else: ?>
  <form>
    <div>
      <h3><?php echo $this->translate("Delete the selected packages?") ?></h3>
      <p>
        <?php echo $this->translate("Please select a package to delete.") ?>
      </p>
      <br/>
      <a href="<?php echo $this->url(array('action' => 'index')) ?>" class="buttonlink icon_back">
        <?php echo $this->translate("Go Back") ?>
      </a>
    </div>
   </form>
  <?php endif;?>
</div>
</div>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
