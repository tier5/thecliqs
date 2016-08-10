<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
?>

<?php if (!empty($this->messages)) : ?>
	<ul class="<?php echo !($this->error)?'ynfs_notices':'ynfs_fail_notices'?>">
		<?php foreach ($this->messages as $mess) : ?>
			<li><?php echo $mess?></li>
		<?php endforeach;?>
	</ul>
<?php endif?>

<div class="ynfs_tags ynfs_block">
	<span class="ynfs_text_header">
		<?php echo $this->translate('Tags') ?>
	</span>
	<?php
		if (count($this->folderTags)) {
			$tags = array();
			foreach ($this->folderTags as $tag) {
				$t = $tag->getTag();
				$text = $t->text;
				if (!empty($text)) {
					$href = $this->url(array('action' => 'index'), 'ynfilesharing_general', true) . '?tag=' . $t->tag_id . '&type=folder';
					$html = "<a href='$href'>" . $text . "</a>";
					array_push($tags, $html);
				}
			}
			echo $this->fluentList($tags);
		} 
	?>
</div>

<div class="ynfs_owner ynfs_block">
	<span class="ynfs_text_header">
		<?php echo $this->translate('Owner') ?>
	</span>
	<?php
		echo $this->folder->getParent(); 
	?>
</div>

<?php
/**
 * Load _browse_folder.tpl
 */
?>


<div id="ynfs_control_browse" class="ynfs_browse">
	<ul class="ynfs_browse_ul_list">
			<li class="ynfs_browse_title">
				<span class="ynfs_name_column">
					<?php echo $this->translate('Name')?>
				</span>
				<span class="ynfs_type_column">
					<?php echo $this->translate('Type')?>
				</span>
				<span class="ynfs_owner_column">
					<?php echo $this->translate('Owner')?>
				</span>
				<span class="ynfs_modifieddate_column">
					<?php echo $this->translate('Modified Date')?>
				</span>
				<span class="ynfs_view_download_column">
					<?php echo $this->translate("Views/Downloads")?>
				</span>
			</li>
			<li class="ynfs_browse_control">
				<div class="ynfs_name_column">
					<?php echo $this->translate('Name')?>
				</div>
				<div class="ynfs_control_column">

				</div>
			</li>

		<?php
		if (isset($this->subFolders)):
			foreach ($this->subFolders as $folder) :
		?>
			<li class="ynfs_item"
				folderId="<?php echo $folder->getIdentity()?>"
				parentId="<?php echo $folder->parent_id?>"
				parentType="<?php echo $folder->parent_type?>">
				<div class="ynfs_name_column">
					<div class="ynfs_folder_icon ynfs_icon"></div>
					<a href="<?php echo $this->base_url . 
					$this->url(
						array(
							'object_type' => 'folder', 
							'object_id' => $folder->getIdentity(),
							'code' => $this->code), 
						'ynfilesharing_share_view', 
						true
						)?>" 
						title="<?php echo $folder->title?>">
						<?php echo $this->string()->truncate($folder->title, 150)?>
					</a>
				</div>
				<div class="ynfs_type_column">
					<?php echo $this->translate('Folder')?>
				</div>
				<div class="ynfs_owner_column">
					<?php
						$parent = $folder->getParent();
					?>
					<a href="<?php echo $parent->getHref()?>" title="<?php echo $this->string()->stripTags($parent->getTitle())?>">
						<?php echo $this->string()->truncate($parent->getTitle(), 30)?>
					</a>
				</div>
				<div class="ynfs_modifieddate_column">
					<?php echo $this->timestamp($folder->modified_date)?>
				</div>
				<div class="ynfs_view_download_column">
					<?php echo $this->locale()->toNumber($folder->view_count)?>
				</div>
			</li>
		<?php endforeach; endif; ?>
		<?php foreach ($this->files as $file) : ?>
			<li class="ynfs_item" fileId="<?php echo $file->getIdentity()?>" 
				currentFolerId="<?php echo (isset($this->currentFolder))?$this->currentFolder->getIdentity():'' ?>" size="<?php echo $file->size ?>"
				parentId="<?php echo $file->parent_id?>"
				parentType="<?php echo $file->parent_type?>"
			>
				<div class="ynfs_name_column">
					<?php
						$file_img_url = $this->baseUrl() . "/application/modules/Ynfilesharing/externals/images/file_types/" . $file->getFileIcon();
					?>
					<div class="ynfs_icon ynfs_file_default" style="background-image: url(<?php echo $file_img_url?>);"></div>
					<a href="<?php echo $this->base_url . 
							$this->url(
								array(
									'object_type' => 'file', 
									'object_id' => $file->getIdentity(),
									'code' => $this->code), 
								'ynfilesharing_share_view', 
								true
								)?>"
						title="<?php echo $file->name?>">
						<?php echo $this->string()->truncate($file->name, 30)?>
					</a>
				</div>
				<div class="ynfs_type_column">
					<?php echo $file->ext?>
				</div>
				<div class="ynfs_owner_column">
					<?php
						$parent = $file->getParent();
					?>
					<a href="<?php echo $parent->getHref()?>" title="<?php echo $this->string()->stripTags($parent->getTitle())?>">
						<?php echo $this->string()->truncate($parent->getTitle(), 30)?>
					</a>
				</div>
				<div class="ynfs_modifieddate_column">
					<?php echo $this->timestamp($file->creation_date)?>
				</div>
				<div class="ynfs_view_download_column">
					<?php echo $this->locale()->toNumber($file->view_count)?>
					/
					<?php echo $this->locale()->toNumber($file->download_count)?>

				</div>
			</li>
		<?php endforeach;?>
	</ul>
</div>

