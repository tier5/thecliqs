<div class="settings">
<div class='global_form'>
  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Clear History?") ?></h3>
      <p>
        <?php echo $this->translate('Are you sure that you want to clear the music history?') ?>
      </p>
      <br />
      <p>
        <button type='submit'><?php echo $this->translate("Remove") ?></button>
        <?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
        <a href='javascript:void(0)' onclick="parent.Smoothbox.close()">
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
</div>
</div>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
