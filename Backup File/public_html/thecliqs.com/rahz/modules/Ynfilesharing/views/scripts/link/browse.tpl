<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
?>

<div class="headline">
    <h2>
        <?php echo $this->translate('File Sharing');?>
    </h2>
    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation)
                ->render();
        ?>
    </div>
</div>

<?php if (!((empty($this->sharedFolders) || (count($this->sharedFolders) == 0)) 
	&& (empty($this->sharedFiles) || count($this->sharedFiles) == 0))): ?>
	<div id="ynfs_control_browse" class="ynfs_browse">
		<ul class="ynfs_browse_ul_list">
				<li class="ynfs_browse_title">
					<span class="ynfs_name_column">
						<?php echo $this->translate('Name')?>
					</span>
					<span class="ynfs_type_column">
						<?php echo $this->translate('Type')?>
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
			if (isset($this->sharedFolders)):
				foreach ($this->sharedFolders as $folder) :
			?>
				<li class="ynfs_item"
					folderId="<?php echo $folder->getIdentity()?>"
					parentId="<?php echo $folder->parent_id?>"
					parentType="<?php echo $folder->parent_type?>">
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
					<div class="ynfs_modifieddate_column">
						<?php echo $this->timestamp($folder->modified_date)?>
					</div>
					<div class="ynfs_view_download_column_share">
						<?php echo $this->locale()->toNumber($folder->view_count)?>
					</div>
					<div class="ynfs_view_remove_link_column">
						<a class="smoothbox" href="<?php echo 
							$this->url(array(
								'controller' => 'link',
								'action' => 'delete',
								'object_type' => 'folders',
								'code' => $folder->share_code
							), 
							'ynfilesharing_general', 
							true)?>"><?php echo $this->translate("Remove")?></a>
					</div>
				</li>
			<?php endforeach; endif; ?>
			<?php foreach ($this->sharedFiles as $file) : ?>
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
						<a href="<?php echo $file->getHref()?>"
							title="<?php echo $file->name?>">
							<?php echo $this->string()->truncate($file->name, 30)?>
						</a>
					</div>
					<div class="ynfs_type_column">
						<?php echo $file->ext?>
					</div>
					<div class="ynfs_modifieddate_column">
						<?php echo $this->timestamp($file->creation_date)?>
					</div>
					<div class="ynfs_view_download_column_share">
						<?php echo $this->locale()->toNumber($file->view_count)?>
						/
						<?php echo $this->locale()->toNumber($file->download_count)?>
					</div>
					<div class="ynfs_view_remove_link_column">
						<a class="smoothbox" href="<?php echo 
							$this->url(array(
								'controller' => 'link',
								'action' => 'delete',
								'object_type' => 'files',
								'code' => $file->share_code
							), 
							'ynfilesharing_general', 
							true)?>"><?php echo $this->translate("Remove")?></a>
					</div>
					
				</li>
			<?php endforeach;?>
		</ul>
	</div>
<?php else : ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There is no shared folders or shared files.'); ?>
		</span>
	</div>		
<?php endif;?>