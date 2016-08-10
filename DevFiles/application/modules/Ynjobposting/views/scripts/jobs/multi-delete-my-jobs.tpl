<div class='global_form'>
  <?php if ($this->ids):?>
  <form method="post">
    <div>
      <h3><?php echo $this->translate("Delete the selected %s jobs?", $this->mode) ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to delete %d %s jobs?", $this->count, $this->mode) ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value='true'/>
        <input type="hidden" name="ids" value="<?php echo $this->ids?>"/>
        <input type="hidden" name="mode" value="<?php echo $this->mode?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
        <a href='<?php echo $this->url(array('action' => 'manage', 'mode' => $this->mode));?>'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
  <?php else: ?>
  <form>
    <div>
      <h3><?php echo $this->translate("Delete the selected %s jobs?", $this->mode) ?></h3>
      <p>
        <?php echo $this->translate("Please select at least one %s job to delete.", $this->mode) ?>
      </p>
      <br/>
      <a href="<?php echo $this->url(array('action' => 'manage', 'mode' => $this->mode)) ?>" class="buttonlink icon_back">
        <?php echo $this->translate("Go Back") ?>
      </a>
    </div>
   </form>
  <?php endif;?>
</div>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
