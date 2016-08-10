<div class="ynjobposting-job-profile-info">
	<div class="ynjobposting-job-profile-info-item">
		<i class="fa fa-usd"></i>
		<div class="ynjobposting-job-profile-info-item-content">
			<div class="info_label"><?php echo $this->translate("Salary");?></div>
			<div class="ynjobposting-job-profile-info-item-salary">
				<?php echo $this->job->getSalary();?>
			</div>
		</div>
	</div>
	<div class="ynjobposting-job-profile-info-item">
	 	<i class="fa fa-briefcase"></i>
	 	<div class="ynjobposting-job-profile-info-item-content">
			<div class="info_label"><?php echo $this->translate("Job Level");?></div>
			<div><?php echo $this->job->getLevel(); ?></div>
		</div>
	</div>
	
	<?php if ($this->job->industry_id):?>
	<div class="ynjobposting-job-profile-info-item">
		<i class="fa fa-folder-open"></i>
		<div class="ynjobposting-job-profile-info-item-content">
			<div class="info_label"><?php echo $this->translate("Industry");?></div>
			<?php $industry = $this->job->getIndustry()?>
			<?php if (!is_null($industry)):?>
				<div><?php echo $industry->title; ?></div>
			<?php endif;?>
		</div>
	</div>
	<?php endif;?>
	
	<?php if ($this->job->working_place):?>
	<div class="ynjobposting-job-profile-info-item">
		<i class="fa fa-map-marker"></i>
		<div class="ynjobposting-job-profile-info-item-content">
			<div class="info_label"><?php echo $this->translate("Location");?></div>
			<div><?php echo $this->job->working_place; ?></div>
		</div>
	</div>
	<?php endif;?>
	
	<?php if ($this->job->language_prefer):?>
	<div class="ynjobposting-job-profile-info-item">
		<i class="fa fa-language"></i>
		<div class="ynjobposting-job-profile-info-item-content">
			<div class="info_label"><?php echo $this->translate("Preferred Language");?></div>
			<div><?php echo $this->job->language_prefer; ?></div>
		</div>
	</div>
	<?php endif;?>
	
	<div class="ynjobposting-job-profile-info-stats">
		<div>
		<?php echo $this->translate(array("<span>%s</span> View", "<span>%s</span> Views", $this->job->view_count), $this->job->view_count);?>
		</div>

		<div>
			<span>
				<?php echo date("d M", strtotime($this->job->expiration_date));?>
			</span>
			<?php if ($this -> job -> status == 'expired'):?>
				<?php echo $this->translate("EXPIRED");?>
			<?php else:?>
				<?php echo $this->translate("expires");?>
			<?php endif;?>
		</div>
	</div>
	
</div>