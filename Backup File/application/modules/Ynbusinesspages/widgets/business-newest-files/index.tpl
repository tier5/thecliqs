<ul>
<?php 
	foreach ($this->subFolders as $folder):
	?>
		<li class="ynfs_item">
			<div class="ynfs_name_column">
				<div class="ynfs_folder_icon ynfs_icon"></div>
				<a href="<?php echo Engine_Api::_() -> ynbusinesspages() ->getFolderHref(array('parent_type' => $this->parentType, 'parent_id' => $this->parentId), $folder)?>"
					title="<?php echo $folder->title?>">
					<?php echo $this->string()->truncate($folder->title, 30)?>
				</a>
			</div>
			<div class="ynfs_type_column">
				<?php echo $this->translate('Folder')?>
			</div>
			<div class="ynfs_owner_column">
				<?php
					$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($folder);
				?>
				<a href="<?php echo $owner->getHref()?>" title="<?php echo $this->string()->stripTags($owner->getTitle())?>">
					<?php echo $this->string()->truncate($owner->getTitle(), 30)?>
				</a>
			</div>
			<div class="ynfs_modifieddate_column">
				<?php echo $this->timestamp($folder->modified_date)?>
			</div>
		</li>
	<?php endforeach;
?>
</ul>