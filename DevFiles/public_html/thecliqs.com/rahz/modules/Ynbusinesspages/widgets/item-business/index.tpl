<ul id="ynbusinesspages-business-items">
    <?php foreach ($this->businesses as $business) : ?>
    	<li class="ynbusinesspages-widget-list-item">
			<div class="ynbusinesspages-widget-list-item-left">
				<?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan( $business, 'thumb.profile' ); ?>
			</div>
			<div class="ynbusinesspages-widget-list-item-content">
				<div class="item-description"><?php echo $this->translate('This %s is belong to the', $this->short_type).' '.$this->translate(array('business_count', 'businesses', count($this->businesses)), count($this->businesses))?></div>
				<div class="ynbusinesspages-widget-list-item-title">
					<?php echo $this->htmlLink($business->getHref(), $business->getTitle())?>
				</div>
			</div>
		</li>    
    <?php endforeach;?>
</ul>
