<h2><?php echo $this->translate('Private Page') ?></h2>
<div class="ynbusinesspages_warning_page">
	<p>
	  <?php echo $this->translate("You can't view this content. You can go back to your business page or logout of business account in oder to view this content.") ?>
	</p>
	<br />
	<a class='buttonlink icon_back' href='<?php echo $this -> business -> getHref()?>'>
	  <?php echo $this->translate('Back to Business') ?>
	</a>
	
	<a class='buttonlink icon_logoutbusiness' href='<?php echo $this -> url(array('action' => 'logout-business', 'return_url' => $this -> return_url), 'ynbusinesspages_general', true)?>'>
	  <?php echo $this->translate('Logout of Business') ?>
	</a>
</div>