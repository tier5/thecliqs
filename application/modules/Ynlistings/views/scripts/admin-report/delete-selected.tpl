<div class="settings">
<div class='global_form'>
  <?php if ($this->ids):?>
  <form method="post">
    <div>
      <h3><?php echo $this->translate("Delete the selected report(s)?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to delete the %d report(s)? It will not be recoverable after being deleted.", $this->count) ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value='true'/>
        <input type="hidden" name="ids" value="<?php echo $this->ids?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
        <a href='<?php echo $this->url(array('controller' => 'report', 'action' => 'manage', 'id' => null));?>'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
  <?php else: ?>
  <form>
    <div>
      <h3><?php echo $this->translate("Delete the selected report(s)?") ?></h3>
      <p>
        <?php echo $this->translate("Please select a report to delete.") ?>
      </p>
      <br/>
      <a href="<?php echo $this->url(array('controller' => 'report', 'action' => 'manage')) ?>" class="buttonlink icon_back">
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
