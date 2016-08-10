<!-- Header -->
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
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('search'))
	    {
	      new OverText($('search'), 
	      {
	        poll: true,
	        pollInterval: 500,
	        positionOptions: {
	          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          offset: {
	            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
	            y: 2
	          }
	        }
	      });
	    }
	 });
</script>

<div class="generic_layout_container layout_main">
	<div class="generic_layout_container layout_middle">
		<div class="generic_layout_container">

<div class="ynbusinesspages-profile-module-header">
	<?php echo $this->form->render($this);?>
</div>

<div class="ynbusinesspages-profile-module-header">
    <!-- Menu Bar -->
    <div class="ynbusinesspages-profile-header-right">
        <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
        'class' => 'buttonlink'
        )) ?>

        <?php if($this->canCreate): ?>
		<?php
			echo $this->htmlLink(
				$this->url(
					array(
						'controller' => 'folder', 
						'action' => 'create',
						'parent_type' => $this->parentType,
						'parent_id' => $this->parentId,
						'business_id' => $this->parentId,
					), 
					'ynfilesharing_general', 
					true
				), 
				'<i class="fa fa-plus-square"></i>'.$this->translate('Create a new folder'),
				array('class' => 'buttonlink')); 
		?>			
		<?php endif;?>
    </div>      
	<div class="ynbusinesspages-profile-header-content">
		<span class="ynbusinesspages-numeric"><?php echo $this -> totalUploaded; ?></span>
		<?php 
		if($this -> maxSizeKB && $this -> maxSizeKB > 0)
			echo $this -> translate("MB of %s MB used", $this -> maxSizeKB);
		else
			echo $this -> translate("MB of Unlimited");
		?>
	</div>
</div>        

<!-- Content -->
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
		'ynbusinesspages', 
		array(
			'subFolders' => $this->subFolders, 
			'foldersPermissions' => $this->foldersPermissions, 
			'files' => $this->files,
			'parentType' => $this->parentType,
			'parentId' => $this->parentId,
			'canCreate' => $this->canCreate,
			'canDeleteRemove' => $this->canDeleteRemove,
		)
	);
?>
</div>
</div>
</div>