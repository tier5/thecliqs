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
						$aMakePaymentButton = $menu -> onMenuInitialize_YnbusinesspagesMakePaymentClaimBusiness();
					?>
					<?php if($aMakePaymentButton) :?>
					<span class="ynbusinesspages-claim-message"><?php echo $aMakePaymentButton['message'];?></span>
					<a class='<?php if(isset($aMakePaymentButton['smoothbox'])) echo 'smoothbox'; ?>' href="<?php echo $this -> url($aMakePaymentButton['params'], $aMakePaymentButton['route'], array()); ?>" > 
						<i class = "<?php if (!empty($aMakePaymentButton['class']))	echo $aMakePaymentButton['class'];?>"></i> <?php echo $this -> translate($aMakePaymentButton['label']) ?>
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