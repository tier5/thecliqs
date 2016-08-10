<div class="ynjobposting-browse-listings  ynjobposting-browse-job-viewmode-list">
	<ul class="ynjobposting-clearfix">
	<?php foreach($this->jobs as $job) :?>
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
							if(!empty($job->approved_date))
							{
								$date = new Zend_Date(strtotime($job->approved_date));
								echo $this->locale()->toDate($date);
							}
							//echo $date->toString('y-MM-dd');
						?>
					</span>
					<span class="ynjobposting-browse-listings-item-jobtype ynjobposting-type-<?php echo strtolower( $job->getJobType() );?>"><?php echo $job->getJobType();?></span>
				</div>
			</div>
		</div>
	</li>
	<?php endforeach;?>
</ul>
</div>