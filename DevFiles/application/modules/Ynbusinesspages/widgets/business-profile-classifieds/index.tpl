<?php
	$this->headScript()-> appendScript('jQuery.noConflict();');
	$this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); 
?>
<script type="text/javascript">
	en4.core.runonce.add(function(){
		var anchor = $('ynbusinesspages_classified').getParent();
		$('ynbusinesspages_classified_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
		$('ynbusinesspages_classified_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

		$('ynbusinesspages_classified_previous').removeEvents('click').addEvent('click', function(){
			en4.core.request.send(new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
				data : {
					format : 'html',
					subject : en4.core.subject.guid,
					page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
				}
			}), {
				'element' : anchor
			})
		});

		$('ynbusinesspages_classified_next').removeEvents('click').addEvent('click', function(){
			en4.core.request.send(new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
				data : {
					format : 'html',
					subject : en4.core.subject.guid,
					page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
				}
			}), {
				'element' : anchor
			})
		});
	});
</script>

<div class="ynbusinesspages-profile-module-header">
	<!-- Menu Bar -->
	<div class="ynbusinesspages-profile-header-right">
		 <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
			<?php echo $this->htmlLink(array(
				'route' => 'ynbusinesspages_extended',
				'controller' => 'classified',
				'action' => 'list',
				'business_id' => $this->business->getIdentity(),
				'parent_type' => 'ynbusinesspages_business',
				'tab' => $this->identity,
			), '<i class="fa fa-list"></i>'.$this->translate('View all Listings'), array(
				'class' => 'buttonlink'
			))
			?>
		<?php endif; ?>
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
		<?php if( $this->paginator->getTotalItemCount() > 0 ): 
			$business = $this->business;?>
			<span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
			<?php echo $this-> translate(array("ynbusiness_classified", "Classified listings", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
		<?php endif; ?>
	</div>
</div>


<div class="ynbusinesspages_list" id="ynbusinesspages_classified">

	<!-- Content -->
	<?php if( $this->paginator->getTotalItemCount() > 0 ): 
	$business = $this->business;?>
	
	<ul class="ynbusinesspages_classified classifieds_browse">           
		<?php foreach ($this->paginator as $classified): 
			$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($classified);?>
		<li>
            <div class="classifieds_browse_photo">
                <?php echo $this->htmlLink($classified->getHref(), $this->itemPhoto($classified, 'thumb.normal')) ?>
            </div>
            <div class="classifieds_browse_info">
                <div class="classifieds_browse_info_title">
                    <h3><?php echo $this->htmlLink($classified->getHref(), $classified->getTitle()) ?></h3>
	   				<?php if( $classified->closed ): ?>
						<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Classified/externals/images/close.png'/>
					<?php endif;?>
                </div>
	            <div class="classifieds_browse_info_date">
	                <?php echo $this->timestamp(strtotime($classified->creation_date)) ?>
					- <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()) ?>
				</div>
	            <div class="classifieds_browse_info_blurb">
	                <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($classified)?>
					<?php echo $this->fieldValueLoop($classified, $fieldStructure) ?>
	   			</div>
                <div class="classifieds_browse_body">
                    <?php echo $this->string()->truncate($this->string()->stripTags($classified->body), 300) ?>
                </div>
            </div>
		</li>       
		<?php endforeach; ?>             
	</ul>  
	
	<div class="ynbusinesspages-paginator">
		<div id="ynbusinesspages_classified_previous" class="paginator_previous">
			<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
			  'onclick' => '',
			  'class' => 'buttonlink icon_previous'
			)); ?>
		</div>
		<div id="ynbusinesspages_classified_next" class="paginator_next">
			<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
			  'onclick' => '',
			  'class' => 'buttonlink_right icon_next'
			)); ?>
		</div>
	</div>
	
	<?php else: ?>
	<div class="tip">
		<span>
			 <?php echo $this->translate('No classifieds have been created.');?>
		</span>
	</div>
	<?php endif; ?>
</div>