<link href="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynjobposting/externals/styles/flexslider.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynjobposting/externals/scripts/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynjobposting/externals/scripts/jquery.flexslider.js"></script>

<div id="featured-jobs" class="flexslider ynjobposting-clearfix">
    <ul class="slides">
    <?php foreach ($this->jobs as $job): ?>
    <li class="ynjobposting-featured-job-item">
        
        <div class="company-photo">
            <?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($job, 'thumb.profile'); ?>
        </div>
        
        <div class="job-title">
            <?php echo $this->htmlLink($job->getHref(), $job->getTitle())?>
        </div>
        
        <?php $company = $job->getCompany(); ?>
        <div class="company-name">
            <?php echo $this->htmlLink($company->getHref(), $company->getTitle())?>
        </div>
        
        <div class="job-location">
            <i class="fa fa-map-marker"></i>
            <?php echo $job->working_place?>
        </div>
        
        <div class="job-level">
            <i class="fa fa-briefcase"></i>
            <?php echo $job->getLevel()?>
        </div>
        
        <div class="ynjobposting-featured-job-item-footer">
            <div class="job-salary">
                <?php echo $job->getSalary()?>
            </div>
            
            <div class="job-type ynjobposting-browse-listings-item-jobtype ynjobposting-type-<?php echo strtolower($job->getJobType())?>">
                <?php echo $job->getJobType()?>
            </div>        
        </div>        

        <div class="ynjobposting-featured-job-item-hover">
            <h5><?php echo $this->translate('Job Description')?></h5>
            <div class="job-description">
                <?php echo strip_tags($job->description) ?>
            </div>

            <div class="ynjobposting-featured-job-item-footer">
                <div class="job-view">
                     <?php echo $this->htmlLink($job->getHref(), '<i class="fa fa-search"></i> '.$this->translate('View'))?>
                </div>

                <?php if (!$job->isOwner() && $this->viewer->getIdentity() && !$job->hasApplied() && !$job->hasSaved()) :?>
                    <?php $url = $this->url(array('module' => 'ynjobposting', 'controller' => 'jobs', 'action' => 'save', 'id' => $job->getIdentity()), 'ynjobposting_job', true); ?>
                    <div class="job-save">
                        <a href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $url?>')">
                            <i class="fa fa-floppy-o"></i>
                            <?php echo $this->translate('Save') ?>
                        </a>
                    </div>
                <?php endif; ?>    
            </div>           
        </div>
    </li>
    <?php endforeach; ?>
    </ul>
</div>

<script type="text/javascript">
    //check open popup
    function checkOpenPopup(url) {
        if(window.innerWidth <= 480) {
            Smoothbox.open(url, {autoResize : true, width: 300});
        }
        else {
            Smoothbox.open(url);
        }
    }

    jQuery.noConflict(); 
    jQuery(window).load(function() {
        var flex_width = jQuery('#featured-jobs').width();

        flex_width = Math.floor(flex_width / (Math.floor(flex_width/200)));
        
        jQuery('#featured-jobs').flexslider({
            animation: "slide",
            animationLoop: false,
            itemWidth: flex_width,
            controlNav: false, 
        });
    });
</script>
