<?php foreach($this -> paginator as $job) :?>
	<div class="interesting-job-item ynjobposting-job-related-item ynjobposting-clearfix">
        <div class="ynjobposting-job-related-item-photo">
            <?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($job); ?>
        </div>
        <div class="ynjobposting-job-related-item-info">
            <div class="ynjobposting-job-related-item-title" title="<?php echo $job->getTitle(); ?>">
            	<?php $title = $this->string()->truncate($this->string()->stripTags($job->getTitle()), 20);?>
                <?php echo $this->htmlLink($job->getHref(), $title)?>
            </div>

            <?php $company = $job->getCompany();
            if ($company) : ?>
            <div class="ynjobposting-job-related-item-industry" title="<?php echo $company->getTitle();?>">
            	<?php $companyName = $this->string()->truncate($this->string()->stripTags($company->getTitle()), 20);?>
                <?php echo $this->htmlLink($company->getHref(), $companyName)?>
            </div>
            <?php endif; ?>

            <?php if ($job -> working_place) { ?>
            <div class="ynjobposting-job-related-item-workplace">
                <i class="fa fa-map-marker"></i>
                <a href="#"><?php echo $job -> working_place;?></a>
            </div>          
            <?php } ?>
        </div>
    </div>
<?php endforeach;?>
