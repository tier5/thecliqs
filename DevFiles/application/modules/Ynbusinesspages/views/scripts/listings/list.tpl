<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
        <div class="headline">
		<h2>
			<?php echo $this->business->__toString()." ";
				echo $this->translate('&#187; Listings');
			?>
		</h2>
        </div>
	</div>
</div>
<div class="generic_layout_container layout_main ynbusinesspages_list ynlistings_grid-view">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="listing_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>

	<div class="generic_layout_container layout_middle">
        <div class="generic_layout_container">
        <div class="ynbusinesspages-profile-module-header">
            <!-- Menu Bar -->
            <div class="ynbusinesspages-profile-header-right">
                <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
                'class' => 'buttonlink'
                )) ?>
                <?php if ($this->canCreate):?>
                    <?php echo $this->htmlLink(array(
                    'route' => 'ynlistings_general',
                    'controller' => 'index',
                    'action' => 'create',
                    'business_id' => $this->business->getIdentity(),
                    'parent_type' => 'ynbusinesspages_business',
                    ), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Listing'), array(
                    'class' => 'buttonlink'
                    ))
                    ?>
                <?php endif; ?>         
            </div>      
            <?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
            <div class="ynbusinesspages-profile-header-content">
                <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span>
                <?php echo $this-> translate(array("ynbusiness_listing", "Listings", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
            </div>
            <?php endif; ?>
        </div>  
		
		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
		<ul class="ynbusinesspages_listing generic_list_widget listing_browse listing_browse_view_content ynlistings-tabs-content clearfix">  
            <?php foreach ($this->paginator as $listing): 
            	$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($listing);?>
            <li>
                <div class="grid-view">
                    <div class="ynlisting-grid-item">
                        <div class="ynlisting-grid-item-content">
                            <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png";?>
                            <div class="item-background" style="background-image: url(<?php echo $photo_url; ?>);">
    
                                <?php if ($listing->featured) : ?>
                                    <div class="featureListing"></div>
                                <?php endif; ?>
    
                                <?php if ($listing->isNew()) : ?>
                                    <div class="newListing"></div>
                                <?php endif; ?>
    
                                <div class="ynlisting-item-rating">
                                    <?php echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $listing)); ?>
                                </div>
                            </div>
                            <div class="item-front-info">
                                <div class="listing_title">
                                    <?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
                                </div>    
    
                                <div class="listing_price">
                                    <?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
                                </div>
                            </div>
                        </div>
                        <div class="ynlisting-grid-item-hover">
                            <div class="ynlisting-grid-item-hover-background">
                                <div class="listing_view_more"> 
                                    <?php echo $this->htmlLink($listing->getHref(), $this->translate('View more <span class="fa fa-arrow-right"></span> ') );?>
                                </div>
    
                                <div class="short_description">
                                    <?php echo strip_tags($listing->short_description)?>
                                </div>
    
                                <div class="listing_creation">
                                    <span class="author-avatar"><?php echo $this->htmlLink($owner, $this->itemPhoto($owner, 'thumb.icon'))?></span>
                                    <span><?php echo $this->translate('by ')?></span>
                                    <span class="author-title"><?php echo $owner?></span>
                                </div>                                                               
                            </div>
                        </div>
                    </div>            
                </div> 

                <div class="ynbusinesspages-profile-module-option">
                    <?php 
                    $canRemove = $business -> isAllowed('listing_delete', null, $listing);
                    $canDelete = $listing->isDeletable();
                    $canEdit = $listing->isEditable();
                    $canPublish = $listing->isEditable() && ($listing->status == 'draft');
                    ?>
                    <?php if ($canRemove || $canPublish || $canDelete || $canEdit): ?>
                    <?php if ($canEdit): ?>
                        <?php echo $this->htmlLink(
                        array(
                            'action' => 'edit',
                            'id' => $listing->getIdentity(),
                            'route' => 'ynlistings_general',
                            'business_id' => $this->subject()->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit Listing'), array(
                            'class' => 'buttonlink',
                        ))
                        ?>
                    <?php endif; ?>

                    <?php if ($canDelete): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynlistings_general',
                            'action' => 'delete',
                            'id' => $listing->getIdentity(),
                            'business_id' => $business->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ),
                        '<i class="fa fa-trash-o"></i>'.$this->translate('Delete Listing'),
                        array('class'=>'buttonlink smoothbox'))
                      ?>
                    <?php endif; ?> 

                    <?php if ($canPublish): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynlistings_general',
                            'action' => 'place-order',
                            'id' => $listing->getIdentity(),
                            'business_id' => $business->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ),
                        '<i class="fa fa-cloud-upload"></i>'.$this->translate('Publish Listing'),
                        array('class'=>'buttonlink'))
                      ?>
                    <?php endif; ?>    

                    <?php if ($canRemove): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynbusinesspages_specific',
                            'action' => 'remove-item',
                            'item_id' => $listing->getIdentity(),
                            'item_type' => 'ynlistings_listing',
                            'item_label' => 'Listing',
                            'remove_action' => 'listing_delete',
                            'business_id' => $business->getIdentity(),
                        ),
                        '<i class="fa fa-times"></i>'.$this->translate('Delete Listing To Business'),
                        array('class'=>'buttonlink smoothbox'))
                        ?>
                    <?php endif; ?> 
                    <?php endif; ?>
                </div>
            </li> 
            <?php endforeach; ?>             
        </ul>   
		<?php if( $this->paginator->count() > 0 ): ?>
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		<?php endif; ?>
		<?php else: ?>
		<div class="tip">
			<span>
			  <?php echo $this->translate('No listings have been created.');?>
			</span>
		</div>
		<?php endif; ?>
        </div>
	</div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('search'))
	    {
	      new OverText($('search'), 
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
  