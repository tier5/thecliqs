<div class="ynbusinesspages_promote_review">
		<div class ='ynbusinesspages_promote_photo_col_right'>
			<a target="_blank" href="<?php echo $this->business->getHref()?>"><?php echo $this->itemPhoto($this->business, 'thumb.profile') ?></a>
		</div>
		<?php if ($this->name == 1) : ?>
			<?php echo $this->htmlLink($this->business->getHref(), $this->string()->truncate($this->business->getTitle(), 28), array('title' => $this->string()->stripTags($this->business->getTitle()), 'target'=> '_blank', 'id' => 'promote_business_name', 'class' => 'ynbusinesspages_title')) ?>
		<?php endif;?>
		
		<?php if ($this->led == 1) : ?>
		<p class="ynbusinesspages_promote_owner_stat" id="promote_business_led">
			<?php echo $this->translate("By");?>
			<a target="_blank" href="<?php echo $this->business->getOwner()->getHref()?>"><?php echo $this->business->getOwner()->getTitle();?> </a>
		</p>
		<?php endif;?>
		
		<?php if ($this->description == 1) : ?>
			<p class="ynbusinesspages_promote_description" id="promote_business_description">
				<?php echo $this->string()->truncate($this->string()->stripTags($this->business->short_description), 115);?>
			</p>
		<?php endif;?>
	</div>