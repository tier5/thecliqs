
<div class="ynjobposting-browse-listings ynjobposting-browse-job-viewmode-grid">
    <div class="ynjobposting-browse-job-viewmode-grid">
        <!-- Content -->
        <?php if( $this->paginator->getTotalItemCount() > 0 ): 
        $business = $this->business;?>
        <ul class="ynbusinesspages_job ynjobposting-clearfix">           
            <?php foreach ($this->paginator as $job): ?>
            <li class="ynjobposting-browse-listings-item">
                <div class="ynjobposting-browse-listings-item-image">
                    <div class="ynjobposting-browse-listings-item-photo">
                        <?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($job); ?>
                    </div>
                    <?php if($job -> featured == 1) :?>
                        <span class="ynjobposting-item-featured"><?php echo $this -> translate('featured');?></span>
                    <?php endif;?>
                </div>
                <div class="ynjobposting-browse-listings-item-content">
                    <div class="ynjobposting-browse-listings-item-top">
                        <div class="ynjobposting-browse-listings-item-title <?php if($job -> featured == 1) :?>
                        ynjobposting-featured<?php endif;?>">
                            <a href="<?php echo $job -> getHref();?>"><?php echo $job->title;?></a>
                        </div>
                        <div class="ynjobposting-browse-listings-item-company">
                            <a href="<?php echo $job -> getCompany() -> getHref();?>"><?php echo $job -> getCompany() -> getTitle();?></a>
                        </div>
                        <div class="ynjobposting-browse-listings-item-industry">
                        <?php $industry = $job->getIndustry();
                        echo ($industry) ? $industry->getTitle() : $this->translate('Unknown industry.'); ?>                         
                        </div>
                    </div>
                    <div class="ynjobposting-browse-listings-item-main">
                    <div class="ynjobposting-browse-listings-item-working">
                        <?php if ($job->working_place) :?>
                        <i class="fa fa-map-marker"></i> <?php echo $job->working_place;?>
                        <?php endif;?>
                    </div>
                    <div class="ynjobposting-browse-listings-item-skill">
                        <i class="fa fa-briefcase"></i> <?php echo $job->getLevel(); ?>
                    </div>
                </div>
    
                <div class="ynjobposting-browse-listings-item-footer">
                    <div class="ynjobposting-browse-listings-item-salary">
                        <?php echo $job->getSalary();?>
                    </div>              
                    <div class="ynjobposting-browse-listings-item-type">
                        <span>
                            <?php 
                                $expirationDate = $job->expiration_date;
                                if (!is_null($expirationDate)) {
                                    $expirationDate = new Zend_Date(strtotime($expirationDate));
                                    $expirationDate->setTimezone($this->timezone);
                                    echo $this->locale()->toDate($expirationDate);
                                }
                            ?>  
                        </span>
                        <span class="ynjobposting-browse-listings-item-jobtype ynjobposting-type-<?php echo strtolower( $job->getJobType() );?>"><?php echo $job->getJobType();?></span>
                    </div>
                </div>
                </div>
            </li>       
            <?php endforeach; ?>             
        </ul>  
        <?php endif; ?>
    </div>
</div>
  