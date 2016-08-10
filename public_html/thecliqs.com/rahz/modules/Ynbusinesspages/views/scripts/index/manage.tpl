<?php if (count($this->paginator)) 
{
	$total = $this->paginator->getTotalItemCount();
	echo '<span class="ynbusinesspages_result_count">'.$total.'</span>';
	echo $this->translate(array('business', 'businesses', $total),$total);
}?>
<?php if( count($this->paginator) ): ?>
<div id="ynbusinesspages-browse-listings" class="ynbusinesspages-browse-company-viewmode-list">
	<ul>
	<?php foreach ($this->paginator as $business) :?>
		<li>
			<div class="ynbusinesspages-company-item">
				<div class="ynbusinesspages-company-item-image">
					<?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan($business); ?>
				</div>

				<div class="ynbusinesspages-company-item-owner-action">
					<?php
						Engine_Api::_()->core()->clearSubject();
						Engine_Api::_()->core()->setSubject($business);
						
						$menu = new Ynbusinesspages_Plugin_Menus();
						$aDashboardButton = $menu -> onMenuInitialize_YnbusinesspagesBusinessDashBoard();
						$aOpenCloseButton = $menu -> onMenuInitialize_YnbusinesspagesOpenCloseBusiness();
						$aDeleteButton = $menu -> onMenuInitialize_YnbusinesspagesDeleteBusiness();
						$aMakePaymentButton = $menu -> onMenuInitialize_YnbusinesspagesMakePaymentBusiness();
						$aFetureButton = $menu -> onMenuInitialize_YnbusinesspagesFeatureBusiness();
						$aMakeClaimPaymentButton = $menu -> onMenuInitialize_YnbusinesspagesMakePaymentClaimBusiness();
						$aEditButton = $menu -> onMenuInitialize_YnbusinesspagesBusinessEdit();
					?>
					
					<!-- for claim business -->
					<?php if($aMakeClaimPaymentButton) :?>
					<span class="ynbusinesspages-claim-message"><?php echo $aMakeClaimPaymentButton['message'];?></span>
					<a class='<?php if(isset($aMakeClaimPaymentButton['smoothbox'])) echo 'smoothbox'; ?>' href="<?php echo $this -> url($aMakeClaimPaymentButton['params'], $aMakeClaimPaymentButton['route'], array()); ?>" > 
						<i class = "<?php if (!empty($aMakeClaimPaymentButton['class']))	echo $aMakeClaimPaymentButton['class'];?>"></i> <?php echo $this -> translate($aMakeClaimPaymentButton['label']) ?>
					</a>
					<?php endif;?>
					
					<?php if($aEditButton) :?>
					<!-- edit  -->
					<a class = "<?php
						if (!empty($aEditButton['class']))
							echo $aEditButton['class'];
					?>" href="<?php echo $this -> url($aEditButton['params'], $aEditButton['route'], array()); ?>" > 
						<i class="fa fa-pencil-square-o"></i> <?php echo $this -> translate($aEditButton['label']) ?>
					</a>
					<?php endif;?>
					
					<?php if($aDashboardButton) :?>
					<!-- dashboard  -->
					<a class = "<?php
						if (!empty($aDashboardButton['class']))
							echo $aDashboardButton['class'];
					?>" href="<?php echo $this -> url($aDashboardButton['params'], $aDashboardButton['route'], array()); ?>" > 
						<i class="fa fa-tachometer"></i> <?php echo $this -> translate($aDashboardButton['label']) ?>
					</a>
					<?php endif;?>
					
					<?php if($aOpenCloseButton) :?>
					<!-- close/publish  -->
					<a class = "<?php
						if (!empty($aOpenCloseButton['class']))
							echo $aOpenCloseButton['class'];
					?>" href="<?php echo $this -> url($aOpenCloseButton['params'], $aOpenCloseButton['route'], array()); ?>" > 
						<i class="fa fa-times-circle-o"></i> <?php echo $this -> translate($aOpenCloseButton['label']) ?>
					</a>
					<?php endif;?>
					
					<?php if($aDeleteButton) :?>
					<!-- delete  -->
					<a class = "<?php
						if (!empty($aDeleteButton['class']))
							echo $aDeleteButton['class'];
					?>" href="<?php echo $this -> url($aDeleteButton['params'], $aDeleteButton['route'], array()); ?>" > 
						<i class="fa fa-trash-o"></i> <?php echo $this -> translate($aDeleteButton['label']) ?>
					</a>
					<?php endif;?>

					<?php if($aMakePaymentButton) :?>
					<!-- sponsor -->
					<a class = "<?php
						if (!empty($aMakePaymentButton['class']))
							echo $aMakePaymentButton['class'];
						?>" href="<?php echo $this -> url($aMakePaymentButton['params'], $aMakePaymentButton['route'], array()); ?>" > 
						<i class="fa fa-money"></i> <?php echo $this -> translate($aMakePaymentButton['label']) ?>
					</a>
					<?php endif;?>
					
					<?php if($aFetureButton) :?>
					<!-- feature -->
					<a class = "<?php
						if (!empty($aFetureButton['class']))
							echo $aFetureButton['class'];
						?>" href="<?php echo $this -> url($aFetureButton['params'], $aFetureButton['route'], array()); ?>" > 
						<i class="fa fa-star"></i> <?php echo $this -> translate($aFetureButton['label']) ?>
					</a>
					<?php endif;?>

				</div>
				
				<div class="ynbusinesspages-company-item-content">
					<div class="ynbusinesspages-company-item-name"><a href='<?php echo $business -> getHref() ?>'><?php echo $business -> name;?></a></div>
					<div class="ynbusinesspages-company-item-statitics">
						<?php echo $this -> translate('Reviews') ;?>: <b><?php echo $business -> getReviewCount() ;?></b>
						| <?php echo $this -> translate('Rating') ;?>: <?php echo Engine_Api::_() -> ynbusinesspages() -> renderBusinessRating($business -> getIdentity(), false);?>
						| <?php echo $this -> translate('Members') ;?>: <b><?php echo $business -> getMemberCount() ;?></b>
						| <?php echo $this -> translate('Followers') ;?>: <b><?php echo $business -> getFollowerCount() ;?></b>
						
					</div>
					
					<?php $package = $business -> getPackage();?>
					<?php 
						$expirationDateObj = null;
						$lastPaymentDateObj = null;
						$approvedDateObj = null;
			        	if (!is_null($business->expiration_date) && !empty($business->expiration_date) && $business->expiration_date) 
			        	{
			        		$expirationDateObj = new Zend_Date(strtotime($business->expiration_date));	
			        	}
						if (!is_null($business->last_payment_date) && !empty($business->last_payment_date) && $business->last_payment_date) 
			        	{
			        		$lastPaymentDateObj = new Zend_Date(strtotime($business->last_payment_date));	
			        	}
						if (!is_null($business->approved_date) && !empty($business->approved_date) && $business->approved_date) 
			        	{
			        		$approvedDateObj = new Zend_Date(strtotime($business->approved_date));	
			        	}
			        	if( $this->viewer && $this->viewer->getIdentity() ) {
							$tz = $this->viewer->timezone;
							if (!is_null($expirationDateObj))
							{
								$expirationDateObj->setTimezone($tz);
							}
							if (!is_null($lastPaymentDateObj))
							{
								$lastPaymentDateObj->setTimezone($tz);
							}
							if (!is_null($approvedDateObj))
							{
								$approvedDateObj->setTimezone($tz);
							}
				        }
					?>
					<div class="ynbusinesspages-company-item-date-exp">
						<span class="ynbusinesspages-grey"><?php echo $this -> translate('Expire') ;?>:</span>
						<?php if ($business->never_expire) :?>
						<b><?php echo $this->translate('never expire')?></b>
						<?php else :?>
						<b><?php echo (!is_null($expirationDateObj)) ? date('M d Y', $expirationDateObj -> getTimestamp())  : ''; ?></b>
						<?php if(!empty($expirationDateObj)) :?>
						(<i><?php echo $this -> translate('Approved At') ;?>: <?php echo (!is_null($approvedDateObj)) ?  date('M d Y', $approvedDateObj -> getTimestamp()) : ''; ?></i>)
						<?php endif;?>
						<?php endif;?>
					</div>

					<div><span class="ynbusinesspages-grey"><?php echo $this -> translate('Status') ;?>:</span> <span class="ynbusinesspages-company-item-manage-status"><?php echo $this -> translate($business -> status) ?></span></div>
					<div class="ynbusinesspages-company-item-package-exp"><span class="ynbusinesspages-grey"><?php echo $this -> translate('Package') ;?>:</span> <span><?php if($package -> getIdentity()) echo $this -> translate($package -> getTitle());?></span></div>
					<div class="ynbusinesspages-company-item-last-payment"><span class="ynbusinesspages-grey"><?php echo $this -> translate('Last Payment') ;?>:</span> <b><?php echo (!is_null($lastPaymentDateObj)) ? date('M d Y', $lastPaymentDateObj -> getTimestamp()): ''; ?></b></div>
					<div class="ynbusinesspages-company-item-feature-exp">
						<span class="ynbusinesspages-grey"><?php echo $this -> translate('Feature Expiration') ;?>:</span> 
						<?php if($business -> featured) :?>
							<?php $featureRow = Engine_Api::_() -> getDbTable('features', 'ynbusinesspages') -> getFeatureRowByBusinessId($business -> getIdentity()) ?>
							<?php if($featureRow) :?>
								<?php
									$featureDateObj = null;
									if (!is_null($featureRow->expiration_date) && !empty($featureRow->expiration_date) && $featureRow->expiration_date) 
						        	{
						        		$featureDateObj = new Zend_Date(strtotime($featureRow->expiration_date));	
						        	}
						        	if( $this->viewer && $this->viewer->getIdentity() ) {
										$tz = $this->viewer->timezone;
										if (!is_null($featureDateObj))
										{
											$featureDateObj->setTimezone($tz);
										}
							        }
								?>
									<?php if($featureDateObj) :?>
										<b><?php echo date('M d Y', $featureDateObj -> getTimestamp())?></b>
									<?php elseif($business -> featured == 1):?>
										<b><?php echo $this -> translate('Unspecified')?></b>
									<?php else :?>
									
									<?php endif;?>
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
	        'query' => $this->formValues,
	    )); ?>
	</div>
</div>	
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no businesses.') ?>
    </span>
  </div>
<?php endif; ?>