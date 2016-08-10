<form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Delete Ad Block") ?></h3>
      <p>
        <?php echo $this->translate("There are ads campaigns on this block and they may not run properly when this block is removed. Are you sure you want to delete this Ad Block?") ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->adblock_id?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate(" or ") ?> 
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>