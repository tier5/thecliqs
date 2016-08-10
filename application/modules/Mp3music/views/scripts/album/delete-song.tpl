<div class='global_form_popup'>
  <form method="POST" action="<?php echo $this->url() ?>">
    <div>
      <h3><?php echo $this->translate('Delete song?') ?></h3>
      <p>
        <?php echo $this->translate('Are you sure that you want to delete the selected song? This action cannot be undone.') ?>
      </p>
      <p>&nbsp;</p>
      <p>
        <input type="hidden" name="album_id" value="<?php echo $this->song_id?>"/>
        <button type='submit'><?php echo $this->translate('Delete') ?></button>
        <?php echo $this->translate("or") ?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
</div>

