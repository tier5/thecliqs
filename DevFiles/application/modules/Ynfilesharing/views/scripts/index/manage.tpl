<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
?>

<?php if ($this->canCreate) : ?>
	<div class="ynfs_block">
		<?php
			echo $this->htmlLink(
				$this->url(
					array(
						'controller' => 'folder', 
						'action' => 'create',
						'parent_type' => $this->parentType,
						'parent_id' => $this->parentId
					), 
					'ynfilesharing_general', 
					true
				), 
				$this->translate('Create a new folder'),
				array('class' => 'buttonlink ynfs_folder_add_icon')); 
		?>
	</div>
	<div class="ynfs_block" style="font-weight: bold; font-size: 11px;">
		<?php 
		if($this -> maxSizeKB && $this -> maxSizeKB > 0)
			echo $this -> translate("%s MB of %s MB used",$this -> totalUploaded, $this -> maxSizeKB);
		else
			echo $this -> translate("%s MB of Unlimited",$this -> totalUploaded);
		?>
	</div>
<?php endif;?>

<?php if (!empty($this->messages)) : ?>
	<ul class="<?php echo empty($this->error)?'ynfs_notices':'ynfs_fail_notices'?>">
		<?php foreach ($this->messages as $mess) : ?>
			<li><?php echo $mess?></li>
		<?php endforeach;?>
	</ul>
<?php endif?>

<?php 
	echo $this->partial(
		'_browse_folders.tpl', 
		'ynfilesharing', 
		array(
			'subFolders' => $this->subFolders, 
			'foldersPermissions' => $this->foldersPermissions, 
			'files' => $this->files,
			'parentType' => $this->parentType,
			'parentId' => $this->parentId,
			'canCreate' => $this->canCreate
		)
	);
?>