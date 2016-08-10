<?php
	$this->headScript()-> appendScript('jQuery.noConflict();'); 
?>
<script type="text/javascript">
	en4.core.runonce.add(function(){
		var anchor = $('ynbusinesspages_blog').getParent();
		$('ynbusinesspages_blog_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
		$('ynbusinesspages_blog_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

		$('ynbusinesspages_blog_previous').removeEvents('click').addEvent('click', function(){
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

		$('ynbusinesspages_blog_next').removeEvents('click').addEvent('click', function(){
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
				'controller' => 'blog',
				'action' => 'list',
				'business_id' => $this->business->getIdentity(),
				'parent_type' => 'ynbusinesspages_business',
				'tab' => $this->identity,
			), '<i class="fa fa-list"></i>'.$this->translate('View all Blog Entries'), array(
				'class' => 'buttonlink'
			))
			?>
		<?php endif; ?>

		<?php if ($this->canCreate):?>
			<?php echo $this->htmlLink(array(
				'route' => 'blog_general',
				'controller' => 'index',
				'action' => 'create',
				'business_id' => $this->business->getIdentity(),
				'parent_type' => 'ynbusinesspages_business',
			), '<i class="fa fa-plus-square"></i>'.$this->translate('Create Blog Entry'), array(
				'class' => 'buttonlink'
			))
			?>
		<?php endif; ?>
	</div>      

	<div class="ynbusinesspages-profile-header-content">
		<?php if( $this->paginator->getTotalItemCount() > 0 ): 
			$business = $this->business;?>
			<span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
			<?php echo $this-> translate(array("ynbusiness_blog", "Blog entries", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
		<?php endif; ?>
	</div>
</div>


<div id="ynbusinesspages_blog">

	<!-- Content -->
	<?php if( $this->paginator->getTotalItemCount() > 0 ): 
	$business = $this->business;?>
	<ul class="ynbusinesspages_blog">           
		<?php foreach ($this->paginator as $blog): 
			$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($blog);?>
		<li>
			<div class="wrap_col3">
				<div class="wrap_col_left">
					<div class="ynblog_entrylist_entry_date">
						<?php 
						$creation_date = new Zend_Date(strtotime($blog->creation_date)); 
						$creation_date->setTimezone($this->timezone);
						?>
						<div class="day">
							<?php echo $creation_date->get(Zend_Date::DAY)?>
						</div>
						<div class="month">
						<?php echo $creation_date->get(Zend_Date::MONTH_NAME_SHORT)?>
						</div>
						<div class="year">
						<?php echo $creation_date->get(Zend_Date::YEAR)?>
						</div>
					</div>
				</div>
				<div class="wrap_col_center">
					<div class="yn_title"><?php echo $this->htmlLink($blog->getHref(), $blog->getTitle()) ?></div>
					<div class="post_by"><?php echo $this->translate('by');?> <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()) ?></div>
					<div class="ynblog_entrylist_entry_body"><?php echo $this->string()->truncate($this->string()->stripTags($blog->body), 300) ?></div>
				</div>
			</div>
		</li>       
		<?php endforeach; ?>             
	</ul>  
	
	<div class="ynbusinesspages-paginator">
		<div id="ynbusinesspages_blog_previous" class="paginator_previous">
			<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
			  'onclick' => '',
			  'class' => 'buttonlink icon_previous'
			)); ?>
		</div>
		<div id="ynbusinesspages_blog_next" class="paginator_next">
			<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
			  'onclick' => '',
			  'class' => 'buttonlink_right icon_next'
			)); ?>
		</div>
	</div>
	
	<?php else: ?>
	<div class="tip">
		<span>
			 <?php echo $this->translate('No blog entries have been created.');?>
		</span>
	</div>
	<?php endif; ?>
</div>