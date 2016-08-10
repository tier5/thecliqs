<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
        <div class="headline">
		<h2>
			<?php echo $this->business->__toString();
				echo $this->translate('&#187; Groupbuy deals');
			?>
		</h2>
        </div>
	</div>
</div>
<div class="generic_layout_container layout_main ynbusinesspages_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="groupbuy_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	<div class="generic_layout_container layout_middle">
        <div class="generic_layout_container">

		<!-- Menu Bar -->
        <div class="ynbusinesspages-profile-module-header">
            <!-- Menu Bar -->
            <div class="ynbusinesspages-profile-header-right">
                <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
                'class' => 'buttonlink'
                )) ?>
                <?php if ($this->canCreate):?>
                    <?php echo $this->htmlLink(array(
                    'route' => 'groupbuy_general',
                    'controller' => 'index',
                    'action' => 'create',
                    'business_id' => $this->business->getIdentity(),
                    'parent_type' => 'ynbusinesspages_business',
                    ), '<i class="fa fa-plus-square"></i>'.$this->translate('Post A New Deal'), array(
                    'class' => 'buttonlink'
                    ))
                    ?>
                <?php endif; ?>
            </div>      
            <?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
            <div class="ynbusinesspages-profile-header-content">
                <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span>
                <?php echo $this-> translate(array("ynbusiness_deal", "Deals", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
            </div>
            <?php endif; ?>
        </div>  

		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
		<ul class="ynbusinesspages_groupbuy groupbuy_list">  	
		<?php foreach ($this->paginator as $groupbuy): 
			$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($groupbuy);?>
            <li>
                <div class="groupbuy_browse_photo">
                    <a href="<?php echo $groupbuy->getHref();?>">
                        <?php   echo $groupbuy->getImageHtml('deal_thumb_medium','thumb.normal1',339,195,'') ?>
                    </a>            
                </div>
                <div class="groupbuy_widget_value">
                    <div class="groupbuy_widget_value_value"> 
                    <?php echo $this->currencyadvgroup($groupbuy->value_deal, $groupbuy->currency)," ",$this->translate("Value") ?>  
                    </div>
                    <div class="groupbuy_widget_value_price"> 
                        <span class="groupbuy_widget_value_price_price"><?php echo $this->currencyadvgroup($groupbuy->price, $groupbuy->currency) ?></span> 
                    </div>
                </div>
                <div class='groupbuy_browse_info'>
                    <p class='groupbuy_browse_info_title'>
                    <?php echo $this->htmlLink($groupbuy->getHref(), $groupbuy->title) ?>
                    </p>
                    <p class='groupbuy_browse_info_date'>
                    <?php echo $this->timestamp($groupbuy->creation_date) ?>
                    <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()) ?>
                    </p>
                    <div class='groupbuy_browse_info_blurb'>
                    <?php if ($groupbuy->location_title) { ?>
                        <span>
                        <?php echo $groupbuy->location_title?>
                        </span>
                        <span>
                        <?php echo $this->translate("-"); ?>
                        </span>
                        <span>
                        <?php echo $this->number($groupbuy->current_sold) ?>    
                        </span>
                        <span> 
                        <?php echo $this->translate("Bought");?>
                        </span>
                    <?php } else { ?>
                        <span>
                        <?php echo $this->number($groupbuy->current_sold) ?>    
                        </span>
                        <span> 
                        <?php echo $this->translate("Bought");?>
                        </span>
                    <?php } ?>
                    </div>
                </div>
                <div class="ynbusinesspages-profile-module-option">
                    <?php 
                    $canRemove = $business -> isAllowed('deal_delete', null, $groupbuy);
                    $canDelete = $groupbuy->isDeleteable() && $groupbuy->status != 30;
                    $canEdit = $groupbuy->published <= 10 && $groupbuy->status < 30 && $groupbuy->isEditable();
                    $canPublish = $groupbuy->published <= 10 && $groupbuy->status < 30 && $groupbuy->published == 0;
                    ?>
                    <?php if ($canRemove || $canPublish || $canDelete || $canEdit): ?>
                    <?php if ($canEdit): ?>
                        <?php echo $this->htmlLink(
                        array(
                            'action' => 'edit',
                            'deal' => $groupbuy->getIdentity(),
                            'route' => 'groupbuy_general',
                            'reset' => true,
                            'business_id' => $this->subject()->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit Deal'), array(
                            'class' => 'buttonlink',
                        ))
                        ?>
                    <?php endif; ?>
                    <?php if ($canDelete): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'groupbuy_general',
                            'action' => 'delete',
                            'deal' => $groupbuy->getIdentity(),
                            'reset' => true,
                            'business_id' => $business->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ),
                        '<i class="fa fa-trash-o"></i>'.$this->translate('Delete Deal'),
                        array('class'=>'buttonlink smoothbox'))
                      ?>
                    <?php endif; ?> 
                    <?php if ($canPublish): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'groupbuy_general',
                            'action' => 'publish',
                            'deal' => $groupbuy->getIdentity(),
                            'reset' => true,
                            'session_id' => session_id(),
                            'business_id' => $business->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ),
                        '<i class="fa fa-cloud-upload"></i>'.$this->translate('Publish Deal'),
                        array('class'=>'buttonlink'))
                      ?>
                    <?php endif; ?> 
                    <?php if ($canRemove): ?>
                    <?php echo $this->htmlLink(array(
                        'route' => 'ynbusinesspages_specific',
                        'action' => 'remove-item',
                        'item_id' => $groupbuy->getIdentity(),
                        'item_type' => 'groupbuy_deal',
                        'item_label' => 'Deal',
                        'remove_action' => 'deal_delete',
                        'business_id' => $business->getIdentity(),
                    ),
                    '<i class="fa fa-times"></i>'.$this->translate('Delete Deal To Business'),
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
			  <?php echo $this->translate('No deals have been created.');?>
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
  