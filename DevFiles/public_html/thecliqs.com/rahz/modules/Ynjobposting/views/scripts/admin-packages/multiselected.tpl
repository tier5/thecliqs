<div class="settings">
<div class='global_form'>
  <?php if ($this->ids):?>
  <form method="post">
    <div>
      <h3><?php echo $this->translate($this->action.' the selected packages?') ?></h3>
      <p>
        <?php echo $this->translate('Are you sure that you want to '.lcfirst($this->action).' selected packages?') ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value='true'/>
        <input type="hidden" name="ids" value="<?php echo $this->ids?>"/>
        <input type="hidden" name="select_action" value="<?php echo $this->action?>"/>
        <button type='submit'><?php echo $this->translate($this->action) ?></button>
        <?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
        <a href='<?php echo $this->url(array('action' => 'index', 'id' => null));?>'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
  <?php else: ?>
  <form>
    <div>
      <h3><?php echo $this->translate($this->action.' the selected packages?') ?></h3>
      <p>
        <?php echo $this->translate('Please select at least one package to '.lcfirst($this->action).'.') ?>
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
