<h3><?php echo $this->translate("My Following Companies")?></h3>

<?php if( count($this->paginator) ): ?>
<div id="ynjobposting-browse-listings" class="ynjobposting-browse-company-viewmode-list">
	<ul>
	<?php foreach ($this->paginator as $row) :?>
		<?php $company = Engine_Api::_() -> getItem('ynjobposting_company', $row->company_id);?>
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

					<div class="ynjobposting-company-item-industry">
						<i class="fa fa-folder-open"></i>
						<!-- industries -->
						<?php $industries = $company -> getIndustries() ?>
						<?php foreach($industries as $industry) :?>
							<?php $i = 0;  ?>
				            <?php if($industry) :?>
								<?php foreach($industry->getBreadCrumNode() as $node): ?>
									<?php if($node -> industry_id != 1) :?>
									<?php if($i != 0) :?>
										&raquo;	
									<?php endif;?>
					        			<?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
					        		<?php endif; ?>
							<?php endforeach; ?>
							<?php if($industry -> parent_id != 0 && $industry -> parent_id  != 1) :?>
								&raquo;	
							<?php endif;?>
							<?php echo $this->htmlLink($industry->getHref(), $industry->title); ?>
							<?php endif;?>
						<?php endforeach;?>
					</div>
						
					<div class="ynjobposting-company-item-viewjobs">
						<?php
						Engine_Api::_()->core()->clearSubject();
						Engine_Api::_()->core()->setSubject($company);
						$menu = new Ynjobposting_Plugin_Company_Menus();
						$aFollowButton = $menu -> onMenuInitialize_YnjobpostingProfileCompanyFollow();
						if ($aFollowButton) :
						?>
						<a class = "<?php if(!empty($aFollowButton['class'])) echo $aFollowButton['class'];?>" href="<?php echo $this->url($aFollowButton['params'], $aFollowButton['route'], array());?>" >
							<button><?php echo $this -> translate($aFollowButton['label'])?></button>
						</a>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</li>
		<?php endif;?>
	<?php endforeach;?>
	<?php Engine_Api::_()->core()->clearSubject();?>
	</ul>
</div>
		
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s following company', 'Total %s following companies', $total),$total);
    echo '</p>';
}?>

<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
    )); ?>
</div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no following companies.') ?>
    </span>
  </div>
<?php endif; ?>