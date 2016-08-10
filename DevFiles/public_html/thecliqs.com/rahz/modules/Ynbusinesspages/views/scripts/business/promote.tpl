<div class="ynbusinesspages_promote_wrapper">
	<div class="ynbusinesspages_promote_code">
		<h3><?php echo $this->translate("Business Box Code")?></h3>
		<textarea readonly="readonly" class="ynbusinesspages_promote_box_code" id="box_code"><iframe src="<?php echo Engine_Api::_()->ynbusinesspages()->getCurrentHost() . $this->url(array('action' => 'business-badge', 'business_id' => $this->business->getIdentity(), 'status' => 111), 'ynbusinesspages_general'); ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:245px;" allowTransparency="true"></iframe>
		</textarea>
		<h3><?php echo $this->translate("Options to show")?>:</h3>
		<input checked="true" type="checkbox" onchange="changeName(this)" onclick="changeName(this)" /> <?php echo $this->translate("Business Name")?> <br />
		<input checked="true" type="checkbox" onchange="changeDescription(this)" onclick="changeDescription(this)" /> <?php echo $this->translate("Business Short Description")?> <br />
		<input checked="true" type="checkbox" onchange="changeLedName(this)" onclick="changeLedName(this)" /> <?php echo $this->translate("Led Name")?>
	</div>

	<div class="ynbusinesspages_promote_review">
		<div class ='ynbusinesspages_promote_photo_col_right'>
			<a target="_blank" href="<?php echo $this->business->getHref()?>"><?php echo $this->itemPhoto($this->business, 'thumb.profile') ?></a>
		</div>
		<?php echo $this->htmlLink($this->business->getHref(), $this->string()->truncate($this->business->getTitle(), 28), array('title' => $this->string()->stripTags($this->business->getTitle()), 'target'=> '_blank', 'id' => 'promote_business_name', 'class' => 'ynbusinesspages_title')) ?>
		<p class="ynbusinesspages_promote_owner_stat" id="promote_business_led">
			<?php echo $this->translate("By");?>
			<a target="_blank" href="<?php echo $this->business->getOwner()->getHref()?>"><?php echo $this->business->getOwner()->getTitle();?> </a>
		</p>
		
		<p class="ynbusinesspages_promote_description" id="promote_business_description">
			<?php echo $this->string()->truncate($this->string()->stripTags($this->business->short_description), 115);?>
		</p>
	</div>
</div>


<script type="text/javascript">
    var name = '1';
    var description = '1';
    var led = '1';
    var status = '111';
    
	var changeName = function(obj)
	{
		if($('promote_business_name') !== null && $('promote_business_name') !== undefined)
		{
			if(obj.checked == false)
			{
				$('promote_business_name').hide();
				name = '0';
			}
			else
			{
				$('promote_business_name').show();
				name = '1';
			}
		}
		status = name + description + led;
		var html = '<iframe src="<?php echo Engine_Api::_()->ynbusinesspages()->getCurrentHost() . $this->url(array('action' => 'business-badge', 'business_id' => $this->business->getIdentity()), 'ynbusinesspages_general'); ?>/status/' + status + '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true"></iframe>';
		$('box_code').value = html;
	};

	var changeDescription = function(obj)
	{
		if($('promote_business_description') !== null && $('promote_business_description') !== undefined)
		{
			if(obj.checked == false)
			{
				$('promote_business_description').hide();
				description = '0';
			}
			else
			{
				$('promote_business_description').show();
				description = '1';
			}
		}
		status = name + description + led;
		var html = '<iframe src="<?php echo Engine_Api::_()->ynbusinesspages()->getCurrentHost() . $this->url(array('action' => 'business-badge', 'business_id' => $this->business->getIdentity()), 'ynbusinesspages_general'); ?>/status/' + status + '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true"></iframe>';
		$('box_code').value = html;
	};
	
	var changeLedName = function(obj)
	{
		if($('promote_business_led') !== null && $('promote_business_led') !== undefined)
		{
			if(obj.checked == false)
			{
				$('promote_business_led').hide();
				led = '0';
			}
			else
			{
				$('promote_business_led').show();
				led = '1';
			}
		}
		status = name + description + led;
		var html = '<iframe src="<?php echo Engine_Api::_()->ynbusinesspages()->getCurrentHost() . $this->url(array('action' => 'business-badge', 'business_id' => $this->business->getIdentity()), 'ynbusinesspages_general'); ?>/status/' + status + '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true"></iframe>';
		$('box_code').value = html;
	};
</script>