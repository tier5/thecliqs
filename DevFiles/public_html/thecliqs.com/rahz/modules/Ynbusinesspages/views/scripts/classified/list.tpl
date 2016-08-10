<?php 
    $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
?>
<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
        <div class="headline">
		<h2>
			<?php echo $this->business->__toString()." ";
				echo $this->translate('&#187; Classified Listings');
			?>
		</h2>
        </div>
	</div>
</div>

<div class="generic_layout_container layout_main ynbusinesspages_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="classified_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>

	<div class="generic_layout_container layout_middle">
        <div class="generic_layout_container">
		<!-- Menu Bar -->
        <div class="ynbusinesspages-profile-module-header">
            <div class="ynbusinesspages-profile-header-right">
                <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
                'class' => 'buttonlink'
                )) ?>

                <?php if ($this->canCreate):?>
                    <?php echo $this->htmlLink(array(
                    'route' => 'classified_general',
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
            <div class="ynbusinesspages-profile-header-content">
                <?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
                <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span>
                <?php echo $this-> translate(array("ynbusiness_classified", "Classified listings", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
                <?php endif; ?>
            </div>
        </div>

		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
		<ul class="ynbusinesspages_classified classifieds_browse">  	
		<?php foreach ($this->paginator as $classified): 
			$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($classified);?>
            <li>
                <div class='classifieds_browse_photo'>
                    <?php echo $this->htmlLink($classified->getHref(), $this->itemPhoto($classified, 'thumb.normal')) ?>
                </div>
                <div class='classifieds_browse_info'>
                    <div class='classifieds_browse_info_title'>
                        <h3>
                        <?php echo $this->htmlLink($classified->getHref(), $classified->getTitle()) ?>
                        <?php if( $classified->closed ): ?>
                            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Classified/externals/images/close.png'/>
                        <?php endif;?>
                        </h3>
                    </div>
                    <div class='classifieds_browse_info_date'>
                        <?php echo $this->timestamp(strtotime($classified->creation_date)) ?>
                        - <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()) ?>
                    </div>
                    <div class='classifieds_browse_info_blurb'>
                        <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($classified)?>
                        <?php echo $this->fieldValueLoop($classified, $fieldStructure) ?>
                    </div>
                    <div class="classifieds_browse_body">
                        <?php echo $this->string()->truncate($this->string()->stripTags($classified->body), 300) ?>
                    </div>
                </div>
 
                <?php 
                $canRemove = $business -> isAllowed('classified_delete', null, $classified);
                $canEdit = $classified->authorization()->isAllowed(null, 'edit');
                $canDelete = $classified->authorization()->isAllowed(null, 'delete');
                ?>
                <?php if ($canRemove || $canDelete || $canEdit): ?>
                <div class="ynbusinesspages-profile-module-option ">
                    <?php if ($canEdit): ?>
                        <?php echo $this->htmlLink(
                        array(
                            'action' => 'edit',
                            'classified_id' => $classified->getIdentity(),
                            'route' => 'classified_specific',
                            'reset' => true,
                            'business_id' => $this->subject()->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit Listing'), array(
                            'class' => 'buttonlink',
                        ))
                        ?>
                    <?php endif; ?>
                    <?php if ($canDelete): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'default',
                            'module' => 'classified',
                            'controller' => 'index',
                            'action' => 'delete',
                            'classified_id' => $classified->getIdentity(),
                            'business_id' => $this->subject()->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                            'format' => 'smoothbox'
                        ),
                        '<i class="fa fa-trash-o"></i>'.$this->translate('Delete Listing'),
                        array('class'=>'buttonlink smoothbox'))
                        ?>
                    <?php endif; ?> 
                    <?php if ($canRemove): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynbusinesspages_specific',
                            'action' => 'remove-item',
                            'item_id' => $classified->getIdentity(),
                            'item_type' => 'classified',
                            'item_label' => 'Listing',
                            'remove_action' => 'classified_delete',
                            'business_id' => $business->getIdentity(),
                        ),
                        '<i class="fa fa-times"></i>'.$this->translate('Delete Listing To Business'),
                        array('class'=>'buttonlink smoothbox'))
                        ?>
                    <?php endif; ?> 
                </div>
                <?php endif; ?>
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
  