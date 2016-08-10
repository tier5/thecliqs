<?php $counter2 = 0; ?>
<?php foreach ($this->items as $key => $items): ?>
<?php
	$counter2++;
	$class = $counter2 == 1 ? 'first' : ($counter2 == count($this->items) ? 'last' : 'middle');
?>

<div class="search-items-wrapper <?php echo $class; ?>">
	<div class="search-category-wrapper">
		<?php echo $this->translate($this->labels[$key]); ?>
	</div>
	<div class="search-items <?php echo $key; ?>">
		<?php $counter = 0; ?>
		<?php foreach ($items as $item): ?>
			<?php
				$counter++;
				$class = $counter == 1 ? 'first' : ($counter == $items->getTotalItemCount() ? 'last' : 'middle');
			?>
			<a href="javascript:void(0)" onclick="page_search.view('<?php echo $item['object']; ?>', <?php echo $item['object_id']; ?>)" class="search-item <?php echo $class; ?>">
				<span class="search-item-photo">
					<img src="<?php echo $item['photo_id']; ?>" width="34px" height="34px" />
				</span>
				<span class="search-item-info">
					<?php if ($item['title']): ?>
						<span class="search-item-title">
						 <?php echo Engine_String::substr($item['title'], 0, 28); ?>
						</span>
					<?php endif; ?>
					<?php if ($item['body']): ?>
						<span class="search-item-desc">
						 <?php echo Engine_String::substr($item['body'], 0, 50); ?>
						</span>
					<?php endif; ?>
				</span>
				<div class="clr"></div>
			</a>
		<?php endforeach; ?>
	</div>
	<div class="clr"></div>
</div>

<?php endforeach; ?>

<?php if ($this->tags->getTotalItemCount() > 0): ?>

<div class="search-items-wrapper search-items-wrapper-taggs">
	<div class="search-category-wrapper">
		<?php echo $this->translate('Tags'); ?>
	</div>
	<div class="search-items ">
		<?php $counter = 0; ?>
		<?php foreach ($this->tags as $item): ?>
			<?php
				$counter++;
				$class = $counter == 1 ? 'first' : ($counter == $this->tags->getTotalItemCount() ? 'last' : 'middle');
			?>
			<a href="javascript:void(0)" onclick="page_search.search_by_tag(<?php echo $item->tag_id; ?>)" class="search-item">
				<span class="search-item-info">
					<?php if ($item->text): ?>
						<span class="search-item-title">
						  <?php echo Engine_String::substr($item->text, 0, 30); ?>
						</span>
					<?php endif; ?>
				</span>
				<div class="clr"></div>
			</a>
		<?php endforeach; ?>
	</div>
	<div class="clr"></div>
</div>

<?php endif; ?>

<a href="javascript:void(0)" onclick="page_search.more();" class="search-more"><?php echo $this->translate("View All Results"); ?></a>