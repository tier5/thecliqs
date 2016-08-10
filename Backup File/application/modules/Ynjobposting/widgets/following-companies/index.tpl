<div id="ynjobposting-browse-listings" class="ynjobposting-browse-company-viewmode-list">
	<ul>
	<?php foreach ($this->companies as $company):?>
		<?php if($company -> status == 'published') :?>
		<li>
			<div class="ynjobposting-company-item">
				<div class="ynjobposting-company-item-image">
					<?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($company); ?>
					<?php if (in_array($company->company_id, $this->sponsorIds)):?>
						<span class="ynjobposting-item-featured"><?php echo $this -> translate('Featured');?></span>
					<?php endif;?>
				</div>
				
				<div class="ynjobposting-company-item-content">
					<div class="ynjobposting-company-item-name <?php if (in_array($company->company_id, $this->sponsorIds)):?>ynjobposting-featured<?php endif;?>">
						<a href='<?php echo $company -> getHref() ?>'><?php echo $company -> name;?></a>
					</div>

					<div class="ynjobposting-company-item-subline">
						<span class="ynjobposting-company-item-size">
							<i class="fa fa-users"></i>
							<?php echo $company -> from_employee; ?> - <?php echo $company -> to_employee; ?> <?php echo $this -> translate('employees');?>
						</span>

						<span class="ynjobposting-company-item-follower">
							<i class="fa fa-arrow-right"></i>
							<?php echo $this->translate(array('%s follower', '%s followers', $company -> countFollower()),$company -> countFollower()); ?>
						</span>
					</div>
				</div>
			</div>
		</li>
		<?php endif;?>
	<?php endforeach;?>
	</ul>
</div>