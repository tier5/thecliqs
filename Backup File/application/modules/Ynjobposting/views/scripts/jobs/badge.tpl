<div class="ynjobposting_promote_wrapper">
    <div class="ynjobposting-browse-listings-item ynjobposting_review">
        <div class="ynjobposting-browse-listings-item-image">
            <div class="ynjobposting-browse-listings-item-photo">
                <?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($this->company); ?>
            </div>                      
        </div>
        <div class="ynjobposting-browse-listings-item-content">
            <div class="ynjobposting-browse-listings-item-top">
		    <?php if ($this->name == 1) : ?>
                <div class="ynjobposting-browse-listings-item-title">
                    <?php echo $this->htmlLink($this->job->getHref(), $this->string()->truncate($this->job->getTitle(), 28), array('title' => $this->string()->stripTags($this->job->getTitle()), 'target'=> '_blank', 'id' => 'promote_job_name', 'class' => 'ynjobposting_title')) ?>
                </div>
            <?php endif;?>
		    <?php if ($this->company_name == 1) : ?>
		        <div class="ynjobposting-browse-listings-item-company">
                    <span class="ynjobposting_owner_stat" id="promote_job_company">
                        <a target="_blank" href="<?php echo $this->company->getHref()?>"><?php echo $this->company->getTitle();?> </a>
                    </span>
                </div>        
		    <?php endif;?>
		    <?php if ($this->candidate == 1) : ?>
        		<div class="ynjobposting_owner_stat" id="promote_job_candidate">
                    <i class="fa fa-briefcase"></i>
                    <?php echo $this->translate(array('\%s candidate','\%s candidates',$this->job->candidate_count),$this->job->candidate_count); ?>
                </div>
		    <?php endif;?>
    		</div>

            <div class="ynjobposting-browse-listings-item-main">            
                <span class="ynjobposting_description">
                    <?php echo $this->string()->truncate($this->string()->stripTags($this->job->description), 115);?>
                </span>
	       </div>            
        </div>
	</div>
</div>