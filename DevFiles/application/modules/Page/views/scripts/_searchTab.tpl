<?php if (count($this->items)): ?>

<?php $counter2 = 0; ?>
<?php foreach ($this->items as $key => $items): ?>
<?php
	$counter2++;
	$class = $counter2 == 1 ? 'first' : ($counter2 == count($this->items) ? 'last' : 'middle');
?>

<div class="search-tab-wrapper <?php echo $class; ?>">
	<div class="search-tab-category-wrapper">
		<h4><?php echo $this->translate($this->labels[$key]); ?></h4>
	</div>
	<div class="search-tab-items <?php echo $key; ?>">
		<?php $counter = 0; ?>
		<?php foreach ($items as $item): ?>
			<?php
				$counter++;
				$class = $counter == 1 ? 'first' : ($counter == $items->getTotalItemCount() ? 'last' : 'middle');
			?>
			<a href="javascript:void(0)" onclick="page_search.view('<?php echo $item['object']; ?>', <?php echo $item['object_id']; ?>);" class="search-tab-item <?php echo $class; ?>">
				<div class="search-tab-item-photo">
					<img src="<?php echo $item['photo_id']; ?>" width="34px" height="34px" />
				</div>
				<div class="search-tab-item-info">
					<?php if ($item['title']): ?>
						<span class="search-item-link">
							<?php echo strip_tags($item['title']); ?>
						</span>
					<?php endif; ?>
					<?php if ($item['body']): ?>
						<div class="search-tab-item-desc">
							<?php echo Engine_String::substr($item['body'], 0, 250); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="clr"></div>
			</a>
		<?php endforeach; ?>
	</div>
	<div class="clr"></div>
</div>

<?php endforeach; ?>

<?php else: ?>
	<ul class='form-errors'><li><?php echo $this->translate('Wrong arguments.'); ?></li></ul>
<?php endif; ?>