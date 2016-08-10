<div class="settings">
<div class='global_form_box'>
  <?php if ($this->ids):?>
  <form method="post">
    <div>
      <h3><?php echo $this->translate("Delete the selected announcements?") ?></h3>
      <p>
      	<?php echo $this -> translate(array("Are you sure that you want to delete the %s announcement? It will not be recoverable after being deleted.", "Are you sure that you want to delete the %s announcements? It will not be recoverable after being deleted." , $this->count), $this->count);?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value='true'/>
        <input type="hidden" name="ids" value="<?php echo $this->ids?>"/>

        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
        <a href='javascript:history.go(-1);'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
  <?php else: ?>
    <?php echo $this->translate("Please select an announcement to delete.") ?> <br/><br/>
    <a href="<?php echo $this->url(array('action' => 'manage')) ?>" class="buttonlink icon_back">
      <?php echo $this->translate("Go Back") ?>
    </a>
  <?php endif;?>
</div>
</div>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
