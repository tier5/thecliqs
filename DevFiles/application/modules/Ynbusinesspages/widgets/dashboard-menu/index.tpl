<?php 
	$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $this->subject()->getIdentity());
	$viewer = Engine_Api::_() -> user() -> getViewer();
	$package = $business -> getPackage();
?>
<ul class="ynbusinesspages-dashboard-menu">
    <?php if($business->isAllowed('view')) :?>
    <li>
        <a href="<?php echo $business->getHref()?>"><?php echo $this -> translate("View Business");?></a>
    </li>
    <?php endif;?>
	<li <?php echo ($this->active == 'statistics') ? 'class="active"' : '';?>>
		<a href="<?php echo $this->url(array('action' => 'statistics', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_dashboard'); ?>"><?php echo $this -> translate("Statistics");?></a>
	</li>
	<?php if($business->isAllowed('edit')) :?>
		<li <?php echo ($this->active == 'edit' && $this -> controller == 'business') ? 'class="active"' : '';?>>
			<a href="<?php echo $this->url(array('action' => 'edit', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_specific'); ?>"><?php echo $this -> translate("Edit Info");?></a>
		</li>
	<?php endif;?>
	<?php if($package -> getIdentity() && $business->isAllowed('manage_cover')) :?>
		<li <?php echo ($this->active == 'cover') ? 'class="active"' : '';?>>
			<a href="<?php echo $this->url(array('action' => 'cover', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_dashboard'); ?>"><?php echo $this -> translate("Cover Photos");?></a>
		</li>
	<?php endif;?>
	<?php if($package -> getIdentity() && $package -> allow_owner_manage_page && $business->isAllowed('manage_page')) :?>
	<li <?php echo ($this->active == 'index') ? 'class="active"' : '';?>>
		<a href="<?php echo $this->url(array('business_id' => $this->subject()->getIdentity()),'ynbusinesspages_dashboard_page'); ?>"><?php echo $this -> translate("Manage Page");?></a>
	</li>
	<?php endif;?>
	<?php if($package -> getIdentity() && $package -> allow_owner_add_contactform && $business->isAllowed('edit')) :?>
	<li <?php echo ($this->active == 'edit' && $this -> controller == 'contact') ? 'class="active"' : '';?>>
		<a href="<?php echo $this->url(array('action' => 'edit', 'id' => $this->subject()->getIdentity()),'ynbusinesspages_contact'); ?>"><?php echo $this -> translate("Manage Contact Form");?></a>
	</li>
	<?php endif;?>
	<?php if($business->isAllowed('manage_role')) :?>
		<li <?php echo ($this->active == 'manage-role') ? 'class="active"' : '';?>>
			<a href="<?php echo $this->url(array('action' => 'manage-role', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_dashboard'); ?>"><?php echo $this -> translate("Manage Member Roles");?></a>
		</li>
	<?php endif;?>
	<?php if($business->isAllowed('manage_rolesetting')) :?>
		<li <?php echo ($this->active == 'role-setting') ? 'class="active"' : '';?>>
			<a href="<?php echo $this->url(array('action' => 'role-setting', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_dashboard'); ?>"><?php echo $this -> translate("Member Role Settings");?></a>
		</li>
	<?php endif;?>
	<?php if($business->isAllowed('manage_announcement')) :?>
		<li <?php echo ($this->active == 'manage' && $this -> controller == 'announcement') ? 'class="active"' : '';?>>
			<a href="<?php echo $this->url(array('action' => 'manage', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_announcement'); ?>"><?php echo $this -> translate("Manage Announcements");?></a>
		</li>
	<?php endif;?>
	<?php if($business->isAllowed('manage_module')) :?>
		<li <?php echo ($this->active == 'module') ? 'class="active"' : '';?>>
			<a href="<?php echo $this->url(array('action' => 'module', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_dashboard'); ?>"><?php echo $this -> translate("Manage Modules");?></a>
		</li>
	<?php endif;?>
	<?php if($package -> getIdentity() && $business->isAllowed('change_theme')) :?>
		<li <?php echo ($this->active == 'theme') ? 'class="active"' : '';?>>
			<a href="<?php echo $this->url(array('action' => 'theme', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_dashboard'); ?>"><?php echo $this -> translate("Business Themes");?></a>
		</li>
	<?php endif;?>
	<?php if($business->isAllowed('update_package')) :?>
	<li <?php echo ($this->active == 'package') ? 'class="active"' : '';?>>
		<a href="<?php echo $this->url(array('action' => 'package', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_dashboard'); ?>"><?php echo $this -> translate("Packages");?></a>
	</li>
	<?php endif;?>
	<?php if($package -> getIdentity() && $business -> approved && $business->isAllowed('feature_business')) :?>
		<li <?php echo ($this->active == 'feature') ? 'class="active"' : '';?>>
			<a href="<?php echo $this->url(array('action' => 'feature', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_dashboard'); ?>"><?php echo $this -> translate("Feature Business");?></a>
		</li>
	<?php endif;?>
	
	<?php if(!($this -> subject() -> is_claimed) && $business -> isOwner($viewer)) :?>
		<li <?php echo ($this->active == 'transfer') ? 'class="active"' : '';?>>
			<a href="<?php echo $this->url(array('action' => 'transfer', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_specific'); ?>"><?php echo $this -> translate("Transfer Owner");?></a>
		</li>
	<?php endif;?>
	
	<?php 
	if($business->isAllowed('login_business')):
		$business_session = new Zend_Session_Namespace('ynbusinesspages_business');
		$businessId = $business_session -> businessId;
		if(!$businessId):?>
			<li>
				<a onclick="selectForSwitch('<?php echo $this->subject() -> getIdentity() ?>');" href="javascript:;"><?php echo $this -> translate("Login As Business");?></a>
			</li>
			<form action="<?php echo $this->url(array('action' => 'login-as-business', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_general'); ?>" id="form_login_as_business" method="post">
				<input type="hidden" name="business_item" id="business_item" value="" />
			</form>
			<script type="text/javascript">
			  var hideItem=new Array();
			  function selectForSwitch(id)
			  { 
				  $('business_item').value = id;
				  $('form_login_as_business').submit();
			  }
			</script>
		<?php else:?>
			<li>
				<a href="<?php echo $this->url(array('action' => 'logout-business', 'business_id' => $this->subject()->getIdentity()),'ynbusinesspages_general'); ?>"><?php echo $this -> translate("Logout of Business");?></a>
			</li>
	<?php endif; endif;?>
</ul>