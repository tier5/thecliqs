<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
?>
<?php if ((empty($this->subFolders) || (count($this->subFolders) == 0)) 
	&& (empty($this->files) || count($this->files) == 0)): ?>
	<div class="tip">
		<span>
			<?php if (isset($this->params) && array_key_exists('search', $this->params)) : ?>
				<?php echo $this->translate('There is no folders and files founded.'); ?>
			<?php else :?>
				<?php if (isset($this->currentFolder)) : ?>
					<?php echo $this->translate('This folder is empty.')?>
				<?php else:?>
					<?php 
						if (isset($this->canCreate) && $this->canCreate) {
							echo $this->translate(
								'There is no folders and files. Please <a href="%s">create</a> one!',
								$this->url(
									array(
										'controller' => 'folder',
										'action' => 'create',
										'parent_type' => $this->parentType,
										'parent_id' => $this->parentId
									),
									'ynfilesharing_general',
									true
								)
							);
						} else {
							echo $this->translate('There is no folders and files.');
						}
					?>
				<?php endif;?>
					
			<?php endif;?>
		</span>
	</div>
<?php else :?>
	<?php
		if (isset($this->foldersPermissions)) {
			$this->headScript()->appendScript(
				'window.addEvent("domready", function() {'
				. 'en4.ynfilesharing.setFoldersPermissions(' . json_encode($this->foldersPermissions) . ')'
				. '});'
			);
		}
	
		if (isset($this->parentType) && isset($this->parentId)) 
		{
			$obj = array('parentId' => $this->parentId, 'parentType' => $this->parentType);
			$this->headScript()->appendScript(
				'window.addEvent("domready", function() {' 
				.	'en4.ynfilesharing.setOptions(' . json_encode($obj) . ')'
				. '});'
			);
		}
	?>
	<?php if (!$this->isViewMore) : ?>
	<div id="ynfs_control_browse" class="ynfs_browse <?php echo (isset($this->foldersPermissions))?'ynfs_control':''?>">
		<ul class="ynfs_browse_ul_list">
	<?php endif;?>
			<?php if (!$this->isViewMore) : ?>
				<li class="ynfs_browse_title">
					<span class="ynfs_check_column">
						<?php if (isset($this->foldersPermissions)) :?>
							<input type="checkbox" class="ynfs_checkall" />
						<?php endif;?>
					</span>
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
						<?php 
							echo $this->translate("Views") . "/" . $this->translate("Downloads")
						?>
					</span>
				</li>
				<li class="ynfs_browse_control">
					<span class="ynfs_check_column">
						<?php if (isset($this->foldersPermissions)) :?>
							<input type="checkbox" class="ynfs_checkall" />
						<?php endif;?>
					</span>
					<div class="ynfs_name_column">
						<?php echo $this->translate('Name')?>
					</div>
					<div class="ynfs_control_column">
	
					</div>
				</li>
			<?php endif;?>
	
			<?php
			if (isset($this->subFolders)):
				foreach ($this->subFolders as $folder) :
			?>
				<li class="ynfs_item"
					folderId="<?php echo $folder->getIdentity()?>"
					parentId="<?php echo $folder->parent_id?>"
					parentType="<?php echo $folder->parent_type?>">
					<div class="ynfs_check_column">
						<?php if (isset($this->foldersPermissions)) :?>
							<input type="checkbox" name="folderIds[]" value="<?php echo $folder->getIdentity()?>" />
						<?php endif;?>					
					</div>
					<div class="ynfs_name_column">
						<div class="ynfs_folder_icon ynfs_icon"></div>
						<a href="<?php echo $folder->getHref(array('parent_type' => $this->parentType, 'parent_id' => $this->parentId))?>"
							title="<?php echo $folder->title?>">
							<?php echo $this->string()->truncate($folder->title, 30)?>
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
					<?php if ($folder->share_code != NULL || $folder->share_code != '') :?>
					<div class="ynfs_share_image" >
						<a class="smoothbox" href="<?php echo $this->url(array(
								'controller' => 'link',
								'action' => 'view',
								'object_type' => 'folder',
								'object_id' => $folder->getIdentity(),
							), 
							'ynfilesharing_general', 
							true)?>">
							<div class="ynfs_file_share_icon"></div>
						</a>
					</div>
					<?php endif; ?>
				</li>
			<?php endforeach; endif; ?>
			<?php foreach ($this->files as $file) : ?>
				<li class="ynfs_item" 
					fileId="<?php echo $file->getIdentity()?>" 
					currentFolerId="<?php echo (isset($this->currentFolder))?$this->currentFolder->getIdentity():'' ?>" size="<?php echo $file->size ?>"
					parentId="<?php echo $file->parent_id?>"
					parentType="<?php echo $file->parent_type?>"
				>
					<div class="ynfs_check_column">
						<?php if (isset($this->foldersPermissions)) :?>
							<input type="checkbox" name="fileIds[]" value="<?php echo $file->getIdentity()?>" />
						<?php endif; ?>					
					</div>
					<div class="ynfs_name_column">
						<?php
							$file_img_url = $this->baseUrl() . "/application/modules/Ynfilesharing/externals/images/file_types/" . $file->getFileIcon();
						?>
						<div class="ynfs_icon ynfs_file_default" style="background-image: url(<?php echo $file_img_url?>);"></div>
						<a href="<?php echo $file->getHref()?>"
							title="<?php echo $file->name?>">
							<?php echo $this->string()->truncate($file->name, 30)?>
						</a>
					</div>
					<div class="ynfs_type_column">
						<?php 
							echo $file->ext . '&nbsp';
						?>
					</div>
					<div class="ynfs_owner_column">
						<?php
							$parent = $file->getOwner();
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
					<div class="ynfs_download_ctrl">
							<a title="<?php echo $this->translate('Download')?>"
								href="<?php echo $this->url(array('action' => 'download', 'file_id' => $file->getIdentity()), 'ynfilesharing_file_specific')?>">
								<div class="ynfs_file_download_icon"></div>
							</a>						
					</div>
					<?php if ($file->share_code != NULL || $file->share_code != '') :?>
					<div class="ynfs_share_image" >
						<a class="smoothbox" href="<?php echo $this->url(array(
								'controller' => 'link',
								'action' => 'view',
								'object_type' => 'file',
								'object_id' => $file->getIdentity(),
							), 
							'ynfilesharing_general', 
							true)?>">
							<div class="ynfs_file_share_icon"></div>
						</a>
					</div>
					<?php endif; ?>
				</li>
			<?php endforeach;?>
	<?php if (!$this->isViewMore) : ?>
		</ul>
	</div>
	<?php endif;?>
<?php endif;?>