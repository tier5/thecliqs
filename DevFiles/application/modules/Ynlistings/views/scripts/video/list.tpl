<style type="text/css">
	#delete-wrapper{
		float:left;
	}
	#delete-element{
		min-width: 70px;
	}
	.photo-delete-wrapper
	{
		padding-left: 135px
	} 
	#profile_main_video
	{
		padding-left: 135px
	}
	.ynlistings_editphotos_info > div{
		margin-top: 10px;
	}
	#buttons-label{
		display:none;
	}
	.listing_options a{
		margin-left: 10px;
	}
	.form-wrapper{
		padding-left: 130px;
	}
</style>
<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->listing->__toString();
				echo $this->translate('&#187; Videos');
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
              
        <?php echo $this->htmlLink(
        array('route' => 'ynlistings_extended','controller' => 'photo','action' => 'index', 'listing_id' => $this->listing->getIdentity()), 
        $this->translate('Add Photos'), 
        array('class' => 'buttonlink icon_listings_add_photos')) ?>
        
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
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<!-- Menu Bar -->
		<div>
			<?php if( $this->canCreate ): ?>
				<?php echo $this->htmlLink(array(
					'route' => 'video_general',
					'action' => 'create',
					'type_parent' =>'ynlistings_listing',
					'profile' => 'profile',
					'id_subject' =>  $this->listing->getIdentity(),
				  ), $this->translate('Add New Video'), array(
					'class' => 'buttonlink icon_listings_add_videos'
				)) ?>
			<?php endif; ?>
		</div>
		
		<!-- Content -->
		<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form ynlistings_browse_filters">
		    <div style="float: none">
		      <div class="form-elements">
		        <?php echo $this->form->getDecorator('FormErrors')->setElement($this->form)->render();?>
		        
		     <?php if ($this->paginator->getTotalItemCount()> 0) : ?>
		      <ul class='ynlistings_editphotos'>        
		        <?php foreach ($this->paginator as $item): ?>
		          <li>
		            <div class="ynlistings_editphotos_photo">
						<?php
							echo $this->partial('_video_listing.tpl', 'ynlistings', array(
								'video' => $item,
								'infoCol' => $this->infoCol,
							));
						?>
		            </div>
		            <div class="ynlistings_editphotos_info">
		            	<?php
			                $key = $item->getGuid();
			                echo $this->form->getSubForm($key)->render($this);
			              ?>
			          <br/>
			          <div id='profile_main_video'>
		              	  <div class="ynlistings_editphotos_cover">
			                <input type="radio" name="cover" value="<?php echo $item->getIdentity() ?>" <?php if( $this->listing->video_id == $item->video_id ): ?> checked="checked"<?php endif; ?> />
			              </div>
			              <div class="ynlistings_editphotos_label">
			                <label><?php echo $this->translate('Main Video');?></label>
			              </div>
		              </div>
		            </div>
		            <br/>
		          </li>
		        <?php endforeach; ?>
		      </ul>
			      <div class="form-wrapper">
			      <div class="form-label" id="buttons-label">&nbsp;</div>
			      <?php echo $this->form->execute->render(); ?>
			       <?php echo $this->form->cancel; ?>
		       	</div>
		       <?php else : ?>
				<div class="tip">
					<span>
						<?php echo $this->translate('There is no videos found.'); ?>
					</span>
				</div>
		      <?php endif; ?>
		        </div>
		    </div>
		</form>
		<br/>
		
	</div>
</div>
<!-- Menu Bar -->


<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('title'))
	    {
	      new OverText($('title'), 
	      {
	        poll: true,
	        pollInterval: 500,
	        positionOptions: {
	          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          offset: {
	            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
	            y: 2
	          }
	        }
	      });
	    }
	 });
</script>