<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<div class="headline">
		<h2>
			<?php echo $this->business->__toString();
				echo $this->translate('&#187; Wikis');
			?>
		</h2>
		</div>
	</div>
</div>
<div class="generic_layout_container layout_main">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="poll_search_form">
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
				<?php if( $this->canCreate ): ?>
					<?php echo $this->htmlLink(array(
						'route' => 'ynwiki_general',
						'action' => 'create',
						'parent_type' =>'ynbusinesspages_business',
						'subject_id' =>  $this->business->business_id,
						'business_id' => $this->subject()->getIdentity(),
					  ), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Space'), array(
						'class' => 'buttonlink'
					)) ?>
				<?php endif; ?>        
            </div>
        </div>  

		<!-- Content -->
		<?php if ($this->pages->getTotalItemCount()> 0) : ?>
		<ul class="ynwiki_browse" style="padding-top: 10px;">
			<?php foreach( $this->pages as $item ): 
				$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($item);
				?>
			<li>
				<div class='ynwiki_browse_photo'>
					<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
				</div>			
				
				<div class='ynwiki_browse_info'>
					<p class='ynwiki_browse_info_title'>
						<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
					</p>
					<p class='ynwiki_browse_info_date'>
						<?php echo $this->translate('Create by <b>%1$s</b> ', $this->htmlLink($owner->getHref(), $owner->getTitle(), array('target'=>'_top')));?>
						|
						<?php echo $this->timestamp($item->creation_date) ?>
						<?php $revision = $item->getLastUpdated();
						if($revision):  ?>
						|
						<?php $owner =  Engine_Api::_()->getItem('user', $revision->user_id);
							echo $this->translate(' Last updated by <b>%1$s</b> ',$this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle(), array('target'=>'_top')));?>
						<?php echo $this->timestamp($revision->creation_date) ?>
						(<?php echo $this->htmlLink(array(
							'action' => 'compare-versions',
							'pageId' => $item->page_id,
							'route' => 'ynwiki_general',
							'reset' => true,
							), $this->translate("view change"), array(
						)) ?>)
						<?php endif;?>
					</p>
										
					<p class='ynwiki_browse_info_blurb'>
						<?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
					</p>
				</div>

				<div class="ynbusinesspages-profile-module-option">
					<?php $canDeleteToBusiness = $this->business -> isAllowed('wiki_delete', null, $item);?>
					<?php if ($canDeleteToBusiness): ?>
						<?php echo $this->htmlLink(array(
                            'route' => 'ynbusinesspages_specific',
                            'action' => 'remove-item',
                            'item_id' => $item->getIdentity(),
                            'item_type' => 'ynwiki_page',
                            'item_label' => $this -> translate('page'),
                            'remove_action' => 'wiki_delete',
                            'business_id' => $this -> business->getIdentity(),
                        ), '<i class="fa fa-times"></i>'.$this->translate('Delete Page to Business'),
                        array('class'=>'buttonlink smoothbox')); ?>
					<?php endif; ?>
				</div>				
			</li>
			<?php endforeach; ?>
		</ul>
		<div class ="ynvideo_pages">
			<?php echo $this->paginationControl($this->pages, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		</div>
		<?php else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('You do not have any pages.');?>
			</span>
		</div>
		<?php endif; ?>	
		</div>
	</div>
</div>