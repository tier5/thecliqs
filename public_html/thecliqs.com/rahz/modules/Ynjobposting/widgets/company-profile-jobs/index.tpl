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
</script>
<script type="text/javascript">
  en4.core.runonce.add(function(){
	<?php if (isset($_GET['view']) && $_GET['view'] == 'job') : ?>
	  tabContainerSwitch($$(".tab_layout_ynjobposting_company_profile_jobs")[0]);
	<?php endif; ?>
    <?php if( !$this->renderOne ): ?>
	    var anchor = $('profile_company_jobs').getParent();
	    $('profile_company_jobs_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
	    $('profile_company_jobs_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
	
	    $('profile_company_jobs_previous').removeEvents('click').addEvent('click', function(){
	      en4.core.request.send(new Request.HTML({
	        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
	        data : {
	          format : 'html',
	          subject : en4.core.subject.guid,
	          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
	        }
	      }), {
	        'element' : anchor
	      })
	    });

	    $('profile_company_jobs_next').removeEvents('click').addEvent('click', function(){
	      en4.core.request.send(new Request.HTML({
	        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
	        data : {
	          format : 'html',
	          subject : en4.core.subject.guid,
	          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
	        }
	      }), {
	        'element' : anchor
	      })
	    });
    <?php endif; ?>
  });
</script>
<?php $viewer = Engine_Api::_() -> user() -> getViewer();?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div id="profile_company_jobs" class="ynjobposting-browse-listings ynjobposting-browse-job-viewmode-list">
<ul class="ynjobposting-clearfix">
	<?php foreach($this->paginator as $job):?>
		<li class="ynjobposting-browse-listings-item">
			<div class="ynjobposting-browse-listings-item-content">
				<div class="ynjobposting-browse-listings-item-top">
					<div class="ynjobposting-browse-listings-item-title <?php if ($job -> featured == 1):?>ynjobposting-featured<?php endif;?>">
						<a href="<?php echo $job -> getHref();?>"><?php echo $job->title;?></a>
					</div>
					<div class="ynjobposting-browse-listings-item-company">
						<a href="<?php echo $job -> getCompany() -> getHref();?>"><?php echo $job -> getCompany() -> getTitle();?></a>
					</div>
				</div>

				<div class="ynjobposting-browse-listings-item-main">
					<div class="ynjobposting-browse-listings-item-working">
						<?php if ($job->working_place) :?>
						<i class="fa fa-map-marker"></i> <?php echo $job->working_place;?>
						<?php endif; ?>
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
								if(!empty($job->approved_date))
								{
									//$date = new Zend_Date(strtotime($job->approved_date));
									//echo $this->locale()->toDate($date);
									$time = strtotime($job->approved_date);
									echo date("M d", $time);
								}
							?>
						</span>
						<span class="ynjobposting-browse-listings-item-jobtype ynjobposting-type-<?php echo strtolower( $job->getJobType() );?>"><?php echo $job->getJobType();?></span>
					</div>
				</div>
			</div>
			<div class="ynjobposting-browse-listings-item-share">
				<?php if (!$job->isOwner() && $viewer->getIdentity() && !$job->hasApplied()) :?>
	                    <?php if (!$job->hasSaved()) : 
	                        $url = $this->url(array('module' => 'ynjobposting', 'controller' => 'jobs', 'action' => 'save', 'id' => $job->getIdentity()), 'ynjobposting_job', true);
	                    ?>
	                        <a href="javascript:void(0)" title='<?php echo $this -> translate('Save');?>' onclick="checkOpenPopup('<?php echo $url?>')">
	                            <i class="fa fa-floppy-o"></i>
	                        </a>
	                    <?php else: ?>
	                        <a href="javascript:void(0)" title='<?php echo $this -> translate('Saved');?>' class="disabled-link">
	                            <i class="fa fa-floppy-o"></i>
	                        </a>
	                    <?php endif; ?>
            	<?php endif; ?>  
				<?php $url = $this -> url(
					array('module' => 'activity', 
					'controller' => 'index', 
					'action' => 'share', 
					'type' => $job -> getType(), 
					'id' => $job -> getIdentity(),
					'format' => 'smoothbox')
					,'default', true);
	            ?>
				<a <?php echo $this -> translate('Share');?> href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $url?>')">
					<i class="fa fa-share-alt"></i>
				</a>
			</div>
		</li>
	<?php endforeach;?>
</ul>
</div>
<div>
      <div id="profile_company_jobs_previous" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
          'onclick' => '',
          'class' => 'buttonlink icon_previous'
        )); ?>
      </div>
      <div id="profile_company_jobs_next" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
          'onclick' => '',
          'class' => 'buttonlink_right icon_next'
        )); ?>
      </div>
</div>
	
<?php else:?>
 <div class="tip">
    <span>
      <?php echo $this->translate('Nothing to show');?>
    </span>
  </div>
<?php endif;?>