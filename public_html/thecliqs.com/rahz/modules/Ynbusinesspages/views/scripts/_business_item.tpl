<li class="ynbusinesspages-widget-list-item">
	<div class="ynbusinesspages-widget-list-item-left">
		<?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan( $this->business ); ?>
	</div>
	<div class="ynbusinesspages-widget-list-item-content">
		<div class="ynbusinesspages-widget-list-item-title">
			<?php echo $this->htmlLink($this->business->getHref(), $this->business->name); ?>
		</div>
		
		<?php $category = $this->business->getMainCategory();?>
		<?php if ($category):?>
		<div class="ynbusinesspages-widget-list-item-category">
			<?php echo $this->htmlLink($category->getHref(), $category-> title);?>
		</div>
		<?php endif;?>
		
		<div class="ynbusinesspages-widget-list-item-location">
			<?php echo $this->business->getMainLocation();?>
		</div>
		
		<?php if ($this->filter == 'like'):?>
		<div class="ynbusinesspages-widget-list-item-info">
			<i class="fa fa-heart"></i> <?php echo $this->translate(array("%s like", "%s likes", $this->business->like_count), $this->business->like_count) ;?>	
		</div>
		<?php endif;?>
		
		<?php if ($this->filter == 'view'):?>
		<div class="ynbusinesspages-widget-list-item-info">
			<i class="fa fa-eye"></i> <?php echo $this->translate(array("%s view", "%s views", $this->business->view_count), $this->business->view_count) ;?>
		</div>
		<?php endif;?>
		
		<?php if ($this->filter == 'rating'):?>
		<div class="ynbusinesspages-widget-list-item-info">
			<i class="fa fa-pencil-square-o"></i> <?php echo $this->translate(array("%s review", "%s reviews", $this->business->review_count), $this->business->review_count) ;?>
		</div>
		<?php endif;?>
		
		<?php if ($this->filter == 'checkin'):?>
		<div class="ynbusinesspages-widget-list-item-info">
			<i class="fa fa-map-marker"></i> <?php echo $this->translate(array("%s check-in", "%s check-in", $this->business->checkin_count), $this->business->checkin_count) ;?>
		</div>
		<?php endif;?>
		
		<?php if ($this->filter == 'topic'):?>
		<div class="ynbusinesspages-widget-list-item-info">
			<i class="fa fa-comments-o"></i> <?php echo $this->translate(array("%s discussion", "%s discussions", $this->business->topic_count), $this->business->topic_count) ;?>
		</div>
		<?php endif;?>
	</div>
</li>