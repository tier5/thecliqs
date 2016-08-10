<div class="settings">
<div class='global_form'>
  <?php if ($this->ids):?>
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Remove the selected history items?") ?></h3>
      <p>
        <?php echo $this->translate(array('Are you sure that you want to remove %s history item?','Are you sure that you want to remove %s history items?', $this->count),$this->count) ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value='true'/>
        <input type="hidden" name="ids" value="<?php echo $this->ids?>"/>
        <button type='submit'><?php echo $this->translate("Remove") ?></button>
        <?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
        <a href='javascript:void(0)' onclick="parent.Smoothbox.close()">
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
  <?php else: ?>
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Remove the selected history items?") ?></h3>
      <p>
        <?php echo $this->translate("Please select at least one history item to remove.") ?>
      </p>
      <br/>
      <button type='button' onclick="parent.Smoothbox.close()"><?php echo $this->translate("Close") ?></button>
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
