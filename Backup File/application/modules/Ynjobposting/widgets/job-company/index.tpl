<div  id="ynjobposting_job_company" class="ynjobposting-job-related">
<?php foreach ($this->paginator as $job):?>
    <?php $job = Engine_Api::_()->getItem('ynjobposting_job', $job['job_id']);?>
    <div class="interesting-job-item ynjobposting-job-related-item ynjobposting-clearfix">
        <div class="ynjobposting-job-related-item-photo">
            <?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($job); ?>
        </div>
        <div class="ynjobposting-job-related-item-info">
            <div class="ynjobposting-job-related-item-title">
                <?php echo $this->htmlLink($job->getHref(), $job->getTitle())?>
            </div>

            <div class="ynjobposting-job-related-item-industry">
                <?php $industry = $job -> getIndustry(); ?>
				<?php echo $industry -> title;?>
            </div>

            <?php if ($job -> working_place) { ?>
            <div class="ynjobposting-job-related-item-workplace">
                <i class="fa fa-map-marker"></i>
                <a href="#"><?php echo $job -> working_place;?></a>
            </div>          
            <?php } ?>
        </div>
    </div>
<?php endforeach;?>
</div>
<?php if ($this->paginator->getTotalItemCount() > $this->limit ):?>
	<?php echo $this->htmlLink($this->company->getHref(), $this->translate("View more"));?>
<?php endif;?>