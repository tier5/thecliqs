<h3><?php echo $this->translate("My Companies")?></h3>

<?php if( count($this->paginator) ): ?>
<h4>
<?php
	    $total = $this->paginator->getTotalItemCount();
	    echo $this->translate(array('Total %s company', 'Total %s companies', $total),$total);
?>
</h4>
<div id="ynjobposting-browse-listings" class="ynjobposting-browse-company-viewmode-list">
	<ul>
	<?php foreach ($this->paginator as $company) :?>
		<li>
			<div class="ynjobposting-company-item">
				<div class="ynjobposting-company-item-image">
					<?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($company); ?>
					<?php if (in_array($company->company_id, $this->sponsorIds)):?>
						<span class="ynjobposting-item-featured"><?php echo $this -> translate('Featured');?></span>
					<?php endif;?>
				</div>
				<div class="ynjobposting-company-item-owner-action">
					<?php
						Engine_Api::_()->core()->clearSubject();
						Engine_Api::_()->core()->setSubject($company);
						$menu = new Ynjobposting_Plugin_Company_Menus();
						$aSponsorButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanySponsor();
						$aCloseButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyClose();
						$aDeleteButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyDelete();
						$aEditButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyEdit();
						$aViewApplicationsButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyViewApplications();
						$aManagePostedJobButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyManagePostedJob();
						$aEditSubmissionButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyEditSubmissionForm();
					?>
					
					<?php if($aEditButton) :?>
					<!-- edit -->
					<a class = "<?php
						if (!empty($aEditButton['class']))
							echo $aEditButton['class'];
						?>" href="<?php echo $this -> url($aEditButton['params'], $aEditButton['route'], array()); ?>" > 
						<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynjobposting/externals/images/icon-edit.png" /> <?php echo $this -> translate($aEditButton['label']) ?>
					</a>
					<?php endif;?>
					
					<?php if($aCloseButton) :?>
					<!-- close/publish  -->
					<a class = "<?php
						if (!empty($aCloseButton['class']))
							echo $aCloseButton['class'];
					?>" href="<?php echo $this -> url($aCloseButton['params'], $aCloseButton['route'], array()); ?>" > 
						<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynjobposting/externals/images/icon-lock.png" /> <?php echo $this -> translate($aCloseButton['label']) ?>
					</a>
					<?php endif;?>
					
					<?php if($aDeleteButton) :?>
					<!-- delete  -->
					<a class = "<?php
						if (!empty($aDeleteButton['class']))
							echo $aDeleteButton['class'];
					?>" href="<?php echo $this -> url($aDeleteButton['params'], $aDeleteButton['route'], array()); ?>" > 
						<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynjobposting/externals/images/icon-delete.png" /> <?php echo $this -> translate($aDeleteButton['label']) ?>
					</a>
					<?php endif;?>

					<?php if($aSponsorButton) :?>
					<!-- sponsor -->
					<a class = "<?php
						if (!empty($aSponsorButton['class']))
							echo $aSponsorButton['class'];
						?>" href="<?php echo $this -> url($aSponsorButton['params'], $aSponsorButton['route'], array()); ?>" > 
						<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynjobposting/externals/images/icon-sponsor.png" /> <?php echo $this -> translate($aSponsorButton['label']) ?>
					</a>
					<?php endif;?>
					
					<?php if ($aViewApplicationsButton) :?>
						<a class = "<?php if(!empty($aViewApplicationsButton['class'])) echo $aViewApplicationsButton['class'];?>" href="<?php echo $this->url($aViewApplicationsButton['params'], $aViewApplicationsButton['route'], true);?>" title="<?php echo $this -> translate($aViewApplicationsButton['label'])?>">                        
                        	<i class="fa fa-paperclip"></i>
                        	<?php echo $this -> translate($aViewApplicationsButton['label'])?>
                   	 	</a>
					<?php endif;?>
					
					<?php if ($aManagePostedJobButton) :?>
						<a class = "<?php if(!empty($aManagePostedJobButton['class'])) echo $aManagePostedJobButton['class'];?>" href="<?php echo $this->url($aManagePostedJobButton['params'], $aManagePostedJobButton['route'], true);?>" title="<?php echo $this -> translate($aManagePostedJobButton['label'])?>">
                        	<i class="fa fa-briefcase"></i>
                        	<?php echo $this -> translate($aManagePostedJobButton['label'])?>
                    	</a>
					<?php endif;?>
					
					<?php if ($aEditSubmissionButton) :?>
						<a class = "<?php if(!empty($aEditSubmissionButton['class'])) echo $aEditSubmissionButton['class'];?>" href="<?php echo $this->url($aEditSubmissionButton['params'], $aEditSubmissionButton['route'], true);?>" title="<?php echo $this -> translate($aEditSubmissionButton['label'])?>">
                        	<i class="fa fa-pencil-square-o"></i>
                        	<?php echo $this -> translate($aEditSubmissionButton['label'])?>
                    	</a>
					<?php endif;?>
				</div>
				
				<div class="ynjobposting-company-item-content">
					<div class="ynjobposting-company-item-name"><a href='<?php echo $company -> getHref() ?>'><?php echo $company -> name;?></a></div>
					<?php 
						$creationDateObject = new Zend_Date(strtotime($company->creation_date));
						if( $this->viewer && $this->viewer->getIdentity() ) {
							$tz = $this->viewer->timezone;
							if (!is_null($creationDateObject))
							{
								$creationDateObject->setTimezone($tz);
							}
						}
					?>
					<div><?php echo $this -> translate('Posted date') ;?>: <b><?php echo $this->locale()->toDate($creationDateObject)?></b></div>
					<div><?php echo $this -> translate('Status') ;?>: <span class="ynjobposting-company-item-manage-status"><?php echo $company -> status ?></span></div>
					<div><?php echo $this -> translate('Published Jobs') ;?>: <b><?php echo $company -> countJobWithStatus('published');?></b></div>
					<div><?php echo $this -> translate('Ended Jobs') ;?>: <b><?php echo $company -> countJobWithStatus('ended');?></b></div>
					<div><?php echo $this -> translate('Expired Jobs') ;?>: <b><?php echo $company -> countJobWithStatus('expired');?></b></div>
					<div><?php echo $this -> translate('Denied Jobs') ;?>: <b><?php echo $company -> countJobWithStatus('denied');?></b></div>
					<div><?php echo $this -> translate('Sponsor Expiration date') ;?>: 
						<?php if (in_array($company->company_id, $this->sponsorIds)):?>
						<?php 
							$sponsorDateObject = null;
							$tableSponsor = Engine_Api::_() -> getDbTable('sponsors', 'ynjobposting');
							$sponsorRow = $tableSponsor -> getSponsorRowByCompanyId($company -> getIdentity());
							if(!empty($sponsorRow->expiration_date) && !is_null($sponsorRow->expiration_date))
							{
								$sponsorDateObject = new Zend_Date(strtotime($sponsorRow->expiration_date));
							}
							if( $this->viewer && $this->viewer->getIdentity() ) {
								$tz = $this->viewer->timezone;
								if (!is_null($sponsorDateObject))
								{
									$sponsorDateObject->setTimezone($tz);
								}
							}
						?>
							<?php if($sponsorDateObject) :?>
							<b><?php echo $this->locale()->toDate($sponsorDateObject)?></b>
							<?php elseif($company -> sponsored == 1):?>
								<b><?php echo $this -> translate('Unspecified')?></b>
							<?php else :?>
							
							<?php endif;?>
						<?php endif;?>
					</div>
				</div>
			</div>
		</li>	
	<?php endforeach;?>
	<?php Engine_Api::_()->core()->clearSubject();?>
	</ul>
	<div>
	    <?php echo $this->paginationControl($this->paginator, null, null, array(
	        'pageAsQuery' => true,
	    )); ?>
	</div>
</div>	
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no companies.') ?>
    </span>
  </div>
<?php endif; ?>