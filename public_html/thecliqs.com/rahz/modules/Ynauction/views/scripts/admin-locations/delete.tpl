  <form method="post" action="" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Delete Deal Location?") ?></h3>
      <?php if(!$this->canNotDelete):?>
      <p class="description">
        <?php echo $this->translate("Are you sure that you want to delete this location? It will not be recoverable after being deleted.") ?>
      </p>
      <?php if($this->usedCount > 0): ?>
      <?php echo $this->moveNode?>
      <br />
      <?php endif; ?>
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->classified_id?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate(" or ") ?> 
      <?php else:?>
      <p class="description">
        <?php echo $this->translate("Please add a new location before deleting this location!") ?>
      </p>
      <p> 
      <?php endif;?>
        <a onclick="parent.Smoothbox.close();" href='javascript:;'>
        <?php echo $this->translate("Cancel") ?></a>
      </p>
    </div>
  </form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
<style type="text/css">
#global_page_ynauction-admin-location-delete-location select
{
    *margin-left: -40px;
}
</style>
