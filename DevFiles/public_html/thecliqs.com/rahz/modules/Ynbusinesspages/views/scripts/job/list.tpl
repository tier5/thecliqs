<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->business->__toString();
				echo $this->translate('&#187; Jobs');
			?>
		</h2>
	</div>
</div>
<div class="generic_layout_container layout_main ynbusinesspages_list ynjobposting-browse-listings ynjobposting-browse-job-viewmode-list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="job_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	<div class="generic_layout_container layout_middle ynjobposting-browse-job-viewmode-grid">

        <div class="ynbusinesspages-profile-module-header">
            <!-- Menu Bar -->
            <div class="ynbusinesspages-profile-header-right">
                <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
                'class' => 'buttonlink'
                )) ?>
                <?php if ($this->canImport):?>
                    <?php echo $this->htmlLink(array(
                        'route' => 'ynbusinesspages_extended',
                        'controller' => 'job',
                        'action' => 'import',
                        'business_id' => $this->business->getIdentity(),
                    ), '<i class="fa fa-plus-square"></i>'.$this->translate('Import Jobs'), array(
                        'class' => 'buttonlink smoothbox'
                    ))
                    ?>
                <?php endif; ?>                
            </div>      
            <?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
            <div class="ynbusinesspages-profile-header-content">
                <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span>
                <?php echo $this-> translate(array("ynbusiness_job", "Jobs", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
            </div>
            <?php endif; ?>
        </div>  
		
		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): $business = $this->business;?>
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
                <div class="ynbusinesspages-profile-module-option">
                    <?php 
                    $canRemove = $business->isAllowed('job_delete', null, $job);
                    $canDelete = $job->isDeletable();
                    $canEdit = $job->isEditable();
                    ?>
                    <?php if ($canRemove || $canDelete || $canEdit): ?>
                    <?php if ($canEdit): ?>
                        <?php echo $this->htmlLink(
                        array(
                            'action' => 'edit',
                            'controller' => 'jobs',
                            'id' => $job->getIdentity(),
                            'route' => 'ynjobposting_job',
                            'reset' => true,
                            'business_id' => $this->subject()->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit'), array(
                            'class' => 'buttonlink',
                        ))
                        ?>
                    <?php endif; ?>
                    <?php if ($canDelete): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynjobposting_job',
                            'controller' => 'jobs',
                            'action' => 'delete',
                            'id' => $job->getIdentity(),
                            'business_id' => $business->getIdentity(),
                            'parent_type' => 'ynbusinesspages_business',
                        ),
                        '<i class="fa fa-trash-o"></i>'.$this->translate('Delete'),
                        array('class'=>'buttonlink smoothbox'))
                      ?>
                    <?php endif; ?>
                    <?php if ($canRemove): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynbusinesspages_specific',
                            'action' => 'remove-item',
                            'item_id' => $job->getIdentity(),
                            'item_type' => 'ynjobposting_job',
                            'item_label' => 'Job',
                            'remove_action' => 'job_delete',
                            'business_id' => $business->getIdentity(),
                        ),
                        '<i class="fa fa-times"></i>'.$this->translate('Delete Job To Business'),
                        array('class'=>'buttonlink smoothbox'))
                        ?>
                    <?php endif; ?>  
                    <?php endif; ?>
                </div>
            </li>       
            <?php endforeach; ?>             
        </ul>  
		<?php if( $this->paginator->count() > 0 ): ?>
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		<?php endif; ?>
		<?php else: ?>
		<div class="tip">
			<span>
			  <?php echo $this->translate('No jobs have been added.');?>
			</span>
		</div>
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('search'))
	    {
	      new OverText($('search'), 
	      {
	        poll: true,
	        pollInterval: 500,
	        positionOptions: {
	          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          offset: {
	            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
	            y: 2
	          }
	        }
	      });
	    }
	 });
</script>
  