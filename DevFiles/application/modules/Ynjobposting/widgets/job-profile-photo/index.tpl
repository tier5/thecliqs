<div class="ynjobposting-job-profile-photo">
	<?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($this->company); ?>		
</div>

<div class="ynjobposting-job-profile-name">
    <?php echo $this->htmlLink($this->company->getHref(), $this->company->getTitle())?>
</div>