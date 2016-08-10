  <form method="post" action="" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Delete Category?") ?></h3>
      <?php if(!$this->canNotDelete):?>   
      <p class="description">
        <?php echo $this->translate("Are you sure that you want to delete this category? It will not be recoverable after being deleted.") ?>
      </p>
      <?php if($this->usedCount > 0): ?>
      <?php echo $this->moveNode?>
      <br />
      <?php endif; ?>
      <p>
      	<?php if(count($this->moveCates) > 0 ):?>
      		<p class="description">
      			<?php echo $this->translate("If you delete this category, all existing listings will be moved to another one.");?>
      		</p>
      		<p class="description">
      		<?php echo $this->translate("Move to Category");?>	
      		<select name='move_category'>
      			<option value ='none'><?php echo $this->translate('None') ?></option>
      			<?php foreach($this->moveCates as $item) :?>
      					<option value='<?php echo $item->getIdentity()?>'>
      						<?php echo $this->translate($item->title)?>
      					</option>
      			<?php endforeach; ?>
      		</select>
      		</p>
      	<?php endif ;?>
        <input type="hidden" name="confirm" value="<?php echo $this->classified_id?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate(" or ") ?>   
      <?php else:?>
      <p class="description">
        <?php echo $this->translate("Please add a new category before deleting this category!") ?>
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
#global_page_groupbuy-admin-category-delete-category select
{
    *margin-left: -40px;
}
</style>
