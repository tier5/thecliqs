<?php
	$menu = new Ynultimatevideo_Plugin_Menus();
	$createButton = $menu -> onMenuInitialize_YnultimatevideoMainCreatePlaylist();
?>

<?php if($createButton) :?>
	<a href="<?php echo $this -> url(array('action' => 'create-playlist'), 'ynultimatevideo_general' ,true);?>"><button><?php echo $this -> translate('Create New Playlist');?></button></a>
<?php endif;?>

