<style type="text/css">
	.listing_options a{
		margin-left: 10px;
	}
</style>
<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->listing->__toString();
				echo $this->translate('&#187; Photos');
			?>
		</h2>
	</div>
</div>

<div class="listing_options">
    <?php if( $this->listing->isOwner($this->viewer())): ?>
        <?php if ($this->listing->isEditable()) : ?>
        <?php echo $this->htmlLink(
        array('route' => 'ynlistings_general', 'action' => 'edit', 'id' => $this->listing->getIdentity()), 
        $this->translate('Edit Listing'), 
        array('class' => 'buttonlink icon_listings_edit')) ?>
        <?php endif; ?>
        
        <?php 
        $category = $this->listing->getCategory();
        if ($this->can_select_theme): 
        ?>
        <?php echo $this->htmlLink(
        array('route' => 'ynlistings_general','controller' => 'index','action' => 'select-theme', 'listing_id' => $this->listing->getIdentity()), 
        $this->translate('Select Theme'), 
        array('class' => 'smoothbox buttonlink icon_listings_select_theme')) ?>
        <?php endif; ?>
              
        <?php if(Engine_Api::_()->hasItemType('video')): ?>
        <?php echo $this->htmlLink(
        array('route' => 'ynlistings_extended','controller' => 'video','action' => 'list', 'listing_id' => $this->listing->getIdentity()), 
        $this->translate('Add Videos'), 
        array('class' => 'buttonlink icon_listings_add_videos')) ?>
        <?php endif;?>
        
        <?php if ($this->listing->status == 'open' && $this->listing->approved_status == 'approved') : ?>
            <?php echo $this->htmlLink(
            array('route' => 'ynlistings_general', 'action' => 'close', 'id' => $this->listing->getIdentity()), 
            $this->translate('Close Listing'), 
            array('class' => 'buttonlink smoothbox icon_listings_close')) ?>
        <?php endif; ?>
        <?php if ($this->listing->status == 'draft') : ?>
            <?php echo $this->htmlLink(
            array('route' => 'ynlistings_general', 'action' => 'place-order', 'id' => $this->listing->getIdentity()), 
            $this->translate('Publish Listing'), 
            array('class' => 'buttonlink icon_listings_publish')) ?>
        <?php endif; ?>
        <?php if ($this->listing->status == 'closed' || $this->listing->status == 'expired') : ?>
            <?php echo $this->htmlLink(
            array('route' => 'ynlistings_general', 'action' => 're-open', 'id' => $this->listing->getIdentity()), 
            $this->translate('Re-open Listing'), 
            array('class' => 'buttonlink smoothbox icon_listings_open')) ?>
        <?php endif; ?>
        
        <?php if ($this->listing->isDeletable()) : ?>
        <?php echo $this->htmlLink(
        array('route' => 'ynlistings_general', 'action' => 'delete', 'id' => $this->listing->getIdentity()), 
        $this->translate('Delete Listing'), 
        array('class' => 'buttonlink smoothbox icon_listings_delete')) ?>
        <?php endif; ?>
    <?php endif ;?>
</div>
<br/>
<div class="ynlisting_listing_action">
<a class='buttonlink icon_listings_add_photos' href="<?php echo $this->url(array('controller'=>'photo','action'=>'upload','listing_id'=>$this->listing->getIdentity(),'profile' => '1'),'ynlistings_extended') ?>">
	<?php echo $this->translate('Add more photos'); ?>
</a>
</div>

<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form ynlistings_browse_filters">
  <div>
    <div>
      <div class="form-elements">
        <?php echo $this->form->getDecorator('FormErrors')->setElement($this->form)->render();?>
        
     <?php if(Count($this->paginator) > 0): ?>
      <ul class='ynlistings_editphotos'>        
        <?php foreach( $this->paginator as $photo ): ?>
          <li class="ynlistings_editphotos_item">
            <div class="ynlistings_editphotos_photo">
              <?php echo $this->itemPhoto($photo, 'thumb.normal')  ?>
            </div>
            <div class="ynlistings_editphotos_info">
              <?php
                $key = $photo->getGuid();
                echo $this->form->getSubForm($key)->render($this);
              ?>
              <div class="ynlistings_editphotos_action">
	              <div class="ynlistings_editphotos_cover">
	                <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->listing->photo_id == $photo->file_id ): ?> checked="checked"<?php endif; ?> />
	              </div>
	              <div class="ynlistings_editphotos_label">
	                <label><?php echo $this->translate('Main Photo');?></label>
	              </div>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="ynlistings_editphotos_button_updated">
	      <?php echo $this->form->execute->render(); ?>
	      <?php echo $this->form->cancel; ?>
      </div>
      <?php else : ?>
			<div class="tip">
				<span>
					<?php echo $this->translate('There is no photos found.'); ?>
				</span>
			</div>
      <?php endif; ?>
        </div>
    </div>
  </div>
</form>

<?php if( $this->paginator->count() > 0 ): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>

<script type="text/javascript">
function removeSubmit(){
   $('execute').hide(); 
}
</script>
