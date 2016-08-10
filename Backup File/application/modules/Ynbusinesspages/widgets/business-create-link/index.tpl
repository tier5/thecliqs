<?php
	$menu = new Ynbusinesspages_Plugin_Menus();
	$createButton = $menu -> onMenuInitialize_YnbusinesspagesMainCreateBusiness();
?>

<?php if($createButton) :?>
	<a href="<?php echo $this -> url(array('action' => 'create'), 'ynbusinesspages_general' ,true);?>"><button><?php echo $this -> translate('Create New Business');?></button></a>
<?php endif;?>

