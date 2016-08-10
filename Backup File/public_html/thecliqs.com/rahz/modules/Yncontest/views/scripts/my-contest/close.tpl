<div class="settings">
<div class='global_form_popup'>
  <form method="post">
    <div>
      <h3><?php echo $this->translate("Close Contest?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to close this contest?") ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->id?>"/>
        <button type='submit'><?php echo $this->translate("Close") ?></button>
        <?php Zend_Registry::get('Zend_Translate')->_(' or ') ?>
        <a href='' onclick="parent.Smoothbox.close();">
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
