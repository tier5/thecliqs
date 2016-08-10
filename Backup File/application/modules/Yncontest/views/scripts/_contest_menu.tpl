<?php if($this->contest):?>
		<div class="tabs yncontest_tabs">
			<ul class="horizontal_menu clearfix">
				<li class="<?php echo (($_SERVER['REQUEST_URI'] == $this->url(array(), "yncontest_mycontest")||$this->active_menu == 'edit-contest') ? "active" : "")?> ">
					<a href="<?php echo $this->url(array('action'=>'edit-contest', 'contest'=>$this->contest), "yncontest_mycontest") ?>"> <?php echo $this->translate("Basic Information") ?></a>
				</li >		
				<li class = "<?php echo (($_SERVER['REQUEST_URI'] == $this->url(array(), "yncontest_mysetting")||$this->active_menu == 'create-contest-setting') ? "active" : "")?>">
					<a href="<?php echo $this->url(array("action"=>"create-contest-setting", 'contest'=>$this->contest), "yncontest_mysetting") ?>"><?php echo $this->translate("Contest Settings") ?></a>
				</li>
			</ul>
		</div>
	
<?php endif;?>