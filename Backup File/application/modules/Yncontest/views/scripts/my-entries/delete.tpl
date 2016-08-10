<div class="settings">
<div class='global_form_popup'>
  <form method="post">
    <div>
      <h3><?php echo $this->translate("Delete Entry?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to delete this entry?") ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->id?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate(" or ") ?>
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
