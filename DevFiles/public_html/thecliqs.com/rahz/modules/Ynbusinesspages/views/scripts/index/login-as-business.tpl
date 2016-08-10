<div class="ynbusinesspages_login_as_business">
	<h3><?php echo $this->translate('Login as Business'); ?></h3>
	<p><?php echo $this -> translate('LOGIN_AS_BUSINESS_DESCRIPTON');?></p>
    <p><?php echo $this->translate('Select "Switch" below to use this site as a Business and interact with others as that Business.');?></p>
	<div class="ynbusinesspages_business_lists">  	
      	<?php foreach ($this -> business as $item_business):
      		if($item_business->isAllowed('login_business')):?>
		     <div>
		     	 <a target="_top" href="<?php echo $item_business->getHref(); ?>">
				    <?php echo $this->itemPhoto($item_business, 'thumb.normal');?>
				 </a>
				 <a target="_top" href="<?php echo $item_business->getHref(); ?>">
				    <?php echo $item_business -> getTitle();?>
				 </a>
		        <button onclick="selectForSwitch('<?php echo $item_business -> getIdentity() ?>');" title="<?php echo $this->translate("Switch as this Business"); ?>"><?php echo $this->translate("Switch"); ?></button>
		     </div>
	      <?php endif; endforeach; ?>
    </div>
	<form action="" id="form_login_as_business" method="post">
		<input type="hidden" name="business_item" id="business_item" value="" />
		<input type="hidden" name="smoothbox" id="smoothbox" value="1" />
	</form>
	<a class="btn-close-login-as-bussiness" href="javascript:;" onclick='javascript:parent.Smoothbox.close()'> <?php echo $this->translate('Close') ?></a>
</div>
<script type="text/javascript">
  var hideItem=new Array();
  function selectForSwitch(id)
  { 
	  $('business_item').value = id;
	  $('form_login_as_business').submit();
  }
</script>
