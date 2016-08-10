<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndonation
 * @author     YouNet Company
 */
?>
<div class="ynfs_block">
	<?php
		if ($this->canCreate) {
			echo $this->htmlLink(
				$this->url(
					array(
						'controller' => 'folder', 
						'action' => 'create', 
						'parent_type' => $this->subject->getType(),
						'parent_id' => $this->subject->getIdentity()
					), 
					'ynfilesharing_general', 
					true
				), 
				$this->translate('Create a new folder'),
				array('class' => 'buttonlink ynfs_folder_add_icon'));
		} 
	?>
</div>
<?php if (count($this->folders)) : ?>
<div class="ynfs_block">
	<ul class="ynfs_browse_folders_thumbnail">
		
			<?php foreach ($this->folders as $folder) : ?>
				<li class="ynfs_folder">
					<div class="ynfs_folder_icon ynfs_icon"></div>
					<a href="<?php echo $folder->getHref(array('parent_id' => $folder->parent_id, 'parent_type' => $folder->parent_type))?>" 
						title="<?php echo $folder->title?>">
						<?php echo $this->string()->truncate($folder->title, 10)?>
					</a>
				</li>
			<?php endforeach;?>
		
	</ul>
</div>
<?php else :?>
	<?php echo $this->translate('There is no folders and files.'); ?>			
<?php endif;?>