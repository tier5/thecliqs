<ul class="ynbusinesspages_manage_menu">
	<li class="ynbusinesspages_my <?php if($this -> action == 'manage') echo "active"?>">
		<a href="<?php echo $this -> url(array('action' => 'manage'), 'ynbusinesspages_general', true)?>">
			<i class="fa fa-building"></i>
			<?php echo $this -> translate("My Businesses")?>
		</a>
	</li>
	<li class="ynbusinesspages_myclaim <?php if($this -> action == 'manage-claim') echo "active"?>">
		<a href="<?php echo $this -> url(array('action' => 'manage-claim'), 'ynbusinesspages_general', true)?>">
			<i class="fa fa-paper-plane"></i>
			<?php echo $this -> translate("My Claiming Businesses")?>
		</a>
	</li>
	<li class="ynbusinesspages_myfavourite <?php if($this -> action == 'manage-favourite') echo "active"?>">
		<a href="<?php echo $this -> url(array('action' => 'manage-favourite'), 'ynbusinesspages_general', true)?>">
			<i class="fa fa-bookmark"></i>
			<?php echo $this -> translate("My Favourite Businesses")?>
		</a>
	</li>
	<li class="ynbusinesspages_myfollow <?php if($this -> action == 'manage-follow') echo "active"?>">
		<a href="<?php echo $this -> url(array('action' => 'manage-follow'), 'ynbusinesspages_general', true)?>">
			<i class="fa fa-arrow-right"></i>
			<?php echo $this -> translate("My Following Businesses")?>
		</a>
	</li>
</ul>