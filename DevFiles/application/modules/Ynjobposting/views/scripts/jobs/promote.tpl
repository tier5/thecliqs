<div class="ynjobposting_promote_wrapper">
	<div class="ynjobposting_promote_code">
		<h3><?php echo $this->translate("Job Box Code")?></h3>
		<textarea readonly="readonly" class="ynjobposting_box_code" id="box_code"><iframe src="<?php echo Engine_Api::_()->ynjobposting()->getCurrentHost().$this->url(array('action' => 'badge', 'id' => $this->job->getIdentity(), 'status' => 111), 'ynjobposting_job'); ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:245px;" allowTransparency="true"></iframe></textarea>
		<h3><?php echo $this->translate("Options to show")?>:</h3>
		<input checked="true" type="checkbox" onchange="changeName(this)" onclick="changeName(this)" /> <?php echo $this->translate("Job Title")?> <br />
		<input checked="true" type="checkbox" onchange="changeCandidate(this)" onclick="changeCandidate(this)" /> <?php echo $this->translate("Candidate")?> <br />
		<input checked="true" type="checkbox" onchange="changeCompanyName(this)" onclick="changeCompanyName(this)" /> <?php echo $this->translate("Company Name")?>
	</div>

	<div class="ynjobposting-browse-listings-item ynjobposting_review">
		<div class="ynjobposting-browse-listings-item-image">
			<div class="ynjobposting-browse-listings-item-photo">
				<?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($this->company); ?>
			</div>						
		</div>
		<div class="ynjobposting-browse-listings-item-content">
			<div class="ynjobposting-browse-listings-item-top">
				<div class="ynjobposting-browse-listings-item-title">
					<?php echo $this->htmlLink($this->job->getHref(), $this->string()->truncate($this->job->getTitle(), 28), array('title' => $this->string()->stripTags($this->job->getTitle()), 'target'=> '_blank', 'id' => 'promote_job_name', 'class' => 'ynjobposting_title')) ?>
				</div>
				<div class="ynjobposting-browse-listings-item-company">
					<span class="ynjobposting_owner_stat" id="promote_job_company">
						<a target="_blank" href="<?php echo $this->company->getHref()?>"><?php echo $this->company->getTitle();?> </a>
					</span>
				</div>
				<div class="ynjobposting_owner_stat" id="promote_job_candidate">
					<i class="fa fa-briefcase"></i>
					<?php echo $this->translate(array('\%s candidate','\%s candidates',$this->job->candidate_count),$this->job->candidate_count); ?>
				</div>
			</div>

			<div class="ynjobposting-browse-listings-item-main">			
				<span class="ynjobposting_description">
					<?php echo $this->string()->truncate($this->string()->stripTags($this->job->description), 115);?>
				</span>
			</div>			
		</div>		
	</div>
	<div class="close-button">
	    <button onclick="parent.Smoothbox.close();"><?php echo $this->translate('Close')?></button>
	</div>
</div>

<script type="text/javascript">
    var name = '1';
    var candidate = '1';
    var company = '1';
    var status = '111';
    
	var changeName = function(obj)
	{
		if($('promote_job_name') !== null && $('promote_job_name') !== undefined)
		{
			if(obj.checked == false)
			{
				$('promote_job_name').hide();
				name = '0';
			}
			else
			{
				$('promote_job_name').show();
				name = '1';
			}
		}
		status = name + candidate + company;
		var html = '<iframe src="<?php echo $this->url(array('action' => 'badge', 'id' => $this->job->getIdentity()), 'ynjobposting_job'); ?>/status/' + status + '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true"></iframe>';
		$('box_code').value = html;
	};
	
	var changeCandidate = function(obj)
	{
		if($('promote_job_candidate') !== null && $('promote_job_candidate') !== undefined)
		{
			if(obj.checked == false)
			{
				$('promote_job_candidate').hide();
				attending = '0';
			}
			else
			{
				$('promote_job_candidate').show();
				attending = '1';
			}
		}
		status = name + candidate + company;
		var html = '<iframe src="<?php echo $this->url(array('action' => 'badge', 'id' => $this->job->getIdentity()), 'ynjobposting_job'); ?>/status/' + status + '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true"></iframe>';
		$('box_code').value = html;
	};

	var changeCompanyName = function(obj)
	{
		if($('promote_job_company') !== null && $('promote_job_company') !== undefined)
		{
			if(obj.checked == false)
			{
				$('promote_job_company').hide();
				led = '0';
			}
			else
			{
				$('promote_job_company').show();
				led = '1';
			}
		}
		status = name + candidate + company;
		var html = '<iframe src="<?php echo $this->url(array('action' => 'badge', 'id' => $this->job->getIdentity()), 'ynjobposting_job'); ?>/status/' + status + '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true"></iframe>';
		$('box_code').value = html;
	};
</script>