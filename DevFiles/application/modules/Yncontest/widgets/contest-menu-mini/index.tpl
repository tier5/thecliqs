<style type = "text/css">
.active {
	font-weight: bold;
}
</style>
<div class = "tabs yncontest_tabs">
<ul class="horizontal_menu clearfix">
	<li class="<?php echo ($this->active_menu == 'my_contest' ? "active" : "")?>">
		<a href="<?php echo $this->url(array("action"=>"index"), "yncontest_mycontest") ?>"><?php echo $this->translate("My Contests") ?></a>
	</li>
	<li class="<?php echo ($this->active_menu == 'favcontest' ? "active" : "")?>">
		<a href="<?php echo $this->url(array("action"=>"favcontest"), "yncontest_mycontest") ?>"><?php echo $this->translate("Favorite Contests") ?></a>
	</li>
	<li class="<?php echo ($this->active_menu == 'followcontest' ? "active" : "")?>">
		<a href="<?php echo $this->url(array("action"=>"followcontest"), "yncontest_mycontest") ?>"><?php echo $this->translate("Follow Contests") ?></a>
	</li>
	
   <li class="<?php echo ( $this->active_menu == 'statictis'  ? "active" : "")?>">
		<a href="<?php echo $this->url(array("action"=>"statictis"), "yncontest_members") ?>"><?php echo $this->translate("Contest Statistics") ?></a>
	</li>	
	
	<li class="last-child <?php echo (( $this->active_menu == 'transaction') ? "active" : "") ?>">
		<a href="<?php echo $this->url(array("action"=>"index"), "yncontest_transaction") ?>" class="<?php echo $this->active_menu;?>"><?php echo $this->translate("Manage Transactions") ?></a>
	</li>
</ul>
</div>

