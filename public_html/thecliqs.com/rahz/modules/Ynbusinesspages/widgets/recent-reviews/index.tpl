<ul class="ynbusinesspages-list-recent-review">
<?php foreach ($this->paginator as $review):?>
	<li>
		<div class="ynbusinesspages-list-recent-review">
			<?php $business = $review->getBusiness();?>		
			<i class="fa fa-pencil-square-o"></i> <?php echo $this->translate("Review for %1s", $this->htmlLink($business->getHref(), $business->name)); ?>
		</div>
		<div class="review-note">
			<div>
				<?php echo $review->renderRating();?>
			</div>
			<div class="review-body">
				<?php echo $this->string()->truncate($this->string()->stripTags($review -> body), 100);?>
			</div>
		</div>
		<div class="review-owner">
			<?php $user = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($review);?>
			<span><?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'));?></span>
			<span><?php echo $this->htmlLink($user->getHref(),$user->getTitle());?></span>
		</div>
	</li>
<?php endforeach;?>
</ul>