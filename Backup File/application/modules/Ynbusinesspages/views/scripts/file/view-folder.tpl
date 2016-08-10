<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynbusinesspages
 * @author     YouNet Company
 */
?>
<div class="generic_layout_container layout_top">
  <div class="generic_layout_container layout_middle">
    <div class="headline">
<h2>
    <?php echo $this->business->__toString()." ";
          echo $this->translate('&#187; Files');
    ?>
</h2>
</div>
</div>
</div>

<div class="generic_layout_container layout_main">
	<div class="generic_layout_container layout_middle">
		<div class="generic_layout_container">
<div class="ynbusinesspages-profile-module-header">
    <!-- Menu Bar -->
    <div class="ynbusinesspages-profile-header-right">
	    <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity()), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
	    'class' => 'buttonlink'
	  )) ?>
	 	<?php echo $this->htmlLink(array('route' => 'ynbusinesspages_extended', 'controller'=>'file', 'action'=>'list', 'business_id' => $this->business->getIdentity()), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to File Folders'), array(
	    'class' => 'buttonlink'
	  )) ?>
    </div>      

</div>  

<!-- Menu Bar -->
<div class="ynfs_block ynfs_controls">
	<?php if ($this->canCreate): ?>	
		<?php
			echo $this->htmlLink(
				$this->url(
					array(
						'controller' => 'folder', 
						'action' => 'create', 
						'parent_folder_id' => $this->folder->getIdentity(),
						'parent_type' => $this->parentType,
						'parent_id' => $this->parentId,
						'business_id' => $this->parentId,
						'slug' => $this->folder->getSlug(),
						'view_folder' => 'true'
					), 
					'ynfilesharing_general', 
					true), 
				$this->translate('Create a new folder'),
				array('class' => 'buttonlink ynfs_folder_add_icon')); 
		?>
	<?php endif;?>
	
	
	
		<?php if ($this->canUpload) : ?>
		<a href="javascript:void(0);" class="buttonlink ynfs_file_add_icon" onclick="$('uploadForm').show();">
			<?php echo $this->translate('Upload File') ?>
		</a>
		<?php endif;?>
		
	
		
	<?php if ($this->canEdit) : ?>
		<a href="<?php 
			echo $this->url(array('action' => 'edit', 'folder_id' => $this->folder->getIdentity(), 'parent_type' => $this->parentType, 'parent_id' => $this->parentId, 'business_id' => $this->parentId), 
				'ynfilesharing_folder_specific', true)?>" 
			class="buttonlink ynfs_control_edit">
			<?php echo $this->translate('Edit') ?>
		</a>
	<?php endif;?>
	
	<?php if ($this->canEditPerm) : ?>
		<a href="<?php 
			echo $this->url(array('action' => 'edit-perm', 'folder_id' => $this->folder->getIdentity(), 'parent_type' => $this->parentType, 'parent_id' => $this->parentId, 'business_id' => $this->parentId), 
				'ynfilesharing_folder_specific', true)?>" 
			class="buttonlink ynfs_control_edit_permission">
			<?php echo $this->translate('Edit Permission') ?>
		</a>
	<?php endif;?>
	
	<?php if ($this->canDeleteRemove) : ?>
		<a href="<?php 
			echo $this->url(
				array(
					'action' => 'delete', 
					'folder_id' => $this->folder->getIdentity(), 
					'parent_id' => $this->parentId, 
					'parent_type' => $this->parentType,
					'business_id' => $this->parentId,
					'case' => 'folder'
				), 
				'ynfilesharing_folder_specific', 
				true);
			?>" 
			class="buttonlink smoothbox ynfs_control_delete">
			<?php echo $this->translate('Delete') ?>
		</a>
	<?php endif;?>
	
	<?php if ($this->canEdit) : ?>
		<a href="<?php 
			echo $this->url(
				array('action' => 'move', 'folder_id' => $this->folder->getIdentity(), 'parent_id' => $this->parentId, 'parent_type' => $this->parentType, 'format' => 'smoothbox'), 
				'ynfilesharing_general', 
				true);
			?>" 
			class="smoothbox buttonlink ynfs_control_move">
			<?php echo $this->translate('Move') ?>
		</a>
	<?php endif;?>
</div>

<?php if (!empty($this->messages)) : ?>
	<ul class="<?php echo !($this->error)?'ynfs_notices':'ynfs_fail_notices'?>">
		<?php foreach ($this->messages as $mess) : ?>
			<li><?php echo $mess?></li>
		<?php endforeach;?>
	</ul>
<?php endif?>

<form class="global_form" method="post" 
	action="<?php echo $this->url(array('action'=>'upload', 'folder_id' => $this->folder->getIdentity(), 'parent_type' => $this->parentType, 'parent_id' => $this->parentId), 'ynfilesharing_folder_specific', true); ?>" 
	enctype="multipart/form-data" id="uploadForm" style="display: none;">
	<div>
		<div>
			<ul class="form-errors" style="display: none;">
				<li></li>
			</ul>
			
			<div class="formRow">
				<div id="file-wrapper" class="form-wrapper">
					<div id="file-label" class="form-label" style="width: 104px;">
						<label for="file" class="required"><?php echo $this->translate('Upload file(s)') ?></label>
					</div>
					<div id="file-element" class="form-element">
						<input type="file" id="file" name="file[]" multiple />
						<br />
						<p style="font-style: italic; font-size: smaller;">
							<?php echo $this->translate("Use this control if your browser doesn't support Drap and Drop") ?>
						</p>
					</div>
				</div>
			</div>
			
			<div id="ynfs_upload">
			</div>
			
			<input type="hidden" name="file_total" id="file_total" value="<?php echo $this->fileTotal ?>" />
			<input type="hidden" name="max_file_total" id="max_file_total" value="<?php echo $this->maxFileTotal ?>" />
			
			<input type="hidden" name="total_size_per_user" id="total_size_per_user" value="<?php echo $this->totalSizePerUser ?>" />
			<input type="hidden" name="max_total_size_per_user" id="max_total_size_per_user" value="<?php echo $this->maxTotalSizePerUser ?>" />
			
			<div id="submit-wrapper" class="form-wrapper">
				<div id="submit-label" class="form-label" style="width: 104px;">&nbsp;</div>
				<div id="submit-element" class="form-element">
					<button name="upload" id="upload" type="submit">
						<?php echo $this->translate('Start Upload') ?>
					</button>
					<button name="cancel" id="cancel" type="button" onclick="en4.ynfilesharing.cancelUploadFile()">
						<?php echo $this->translate('Cancel') ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</form>
<div class="ynfs_owner ynfs_block">
	<span class="ynfs_text_header">
		<?php echo $this->translate('Owner') ?>
	</span>
	<?php
		echo $this->folder->getOwner(); 
	?>
</div>

<div class="ynfs_folder_navigation ynfs_block">
	<ul>
		<li>
		<?php
			 $parentFolders = $this->folder->getParentFolders();
			 array_push($parentFolders, $this->folder);
			 $topFolder = $parentFolders[0];
			 ?>
			 
			 <?php if ( ($topFolder->parent_id == $this->parentId) && ($topFolder->parent_type == $this->parentType) ): ?>
			 	<a href="<?php echo $this->url(
					array('controller'=>'file', 'action'=>'list', 'business_id' => $this->business->getIdentity()), 
					'ynbusinesspages_extended', 
					true)?>">
					<?php echo $this->translate('Business Folders')?>
				</a>
			 <?php else: ?>
			 	<?php 
			 		$viewer = Engine_Api::_()->user()->getViewer();
			 	?>
			 	<?php if ($viewer->getIdentity()) :?>
				 	<a href="<?php echo $this->url(array('parent_type' => $this->parentType, 'parent_id' => $this->parentId), 'ynfilesharing_general', true) ?>">
						<?php echo $this->translate('Public Folders')?>
					</a>
				<?php else : ?>
					<a href="<?php echo $this->url(array(), 'ynfilesharing_general', true) ?>">
						<?php echo $this->translate('Public Folders')?>
					</a>
				<?php endif;?>
			<?php endif; ?>
		</li>
		<?php foreach ($parentFolders as $folder) : ?>
			<li>
				<img class="ynfs_next_arrow" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynfilesharing/externals/images/next.png" />
				<a href="<?php echo Engine_Api::_() -> ynbusinesspages() ->getFolderHref(array('parent_id' => $this->parentId, 'parent_type' => $this->parentType), $folder)?>">
					<?php echo $folder->title?>
				</a>
			</li>
		<?php endforeach;?>
		
	</ul>
</div>

<?php 
	echo $this->partial('_browse_folders.tpl', 'ynbusinesspages', 
		array(
			'subFolders' => $this->subFolders, 
			'foldersPermissions' => $this->foldersPermissions, 
			'files' => $this->files, 
			'currentFolder' => $this->folder,
			'parentType' => $this->parentType,
			'parentId' => $this->parentId,
			'canCreate' => $this->canCreate,
			'canDownload' => $this->canDownload,
			'canDeleteRemove' => $this->canDeleteRemove
		)
	);

	$this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynfilesharing/externals/scripts/Request.File.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynfilesharing/externals/scripts/Form.MultipleFileInput.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynfilesharing/externals/scripts/Form.Upload.js');
?>

<script type="text/javascript">
	var upload;
	window.addEvent('domready', function(){
		// Create the file uploader
		upload = new Form.Upload('file', {
		    dropMsg: "Drop files here",
		    onComplete: function(response){
				result = JSON.decode(response);
				msg = "";
				if (result.error == "0" && result.message != "")
					msg = result.message + "\r\n";
				
				if ((typeof result.wrong_format !== "undefined") && (result.wrong_format != "")) {
					files_array = result.wrong_format.split("|");
					for (i=0; i<files_array.length-1; i++) {
						msg += files_array[i] + " - wrong file extension\n";
				  	}
				}
				if ((typeof result.wrong_size !== "undefined") && (result.wrong_size != "")) {
					files_array = result.wrong_size.split("|");
					for (i=0; i<files_array.length-1; i++) {
						msg += files_array[i] + " - wrong file size\n";
				  	}
				}
				alert(msg);
				window.location = window.location;
		    },
		    dropZone: $("global_wrapper")
		});
	});
</script>
</div>
</div>
</div>