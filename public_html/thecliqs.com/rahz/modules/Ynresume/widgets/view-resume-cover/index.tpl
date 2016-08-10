<?php 
	$resume = Engine_Api::_() -> getItem('ynresume_resume', $this->subject()->getIdentity()); 
	$viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $theme = $resume->theme;
?>
<?php if (!Engine_Api::_()->ynresume()->isMobile()) : ?>
<style type="text/css">
	/*** style 1 ***/
	#ynresume_cover_wrapper.ynresume-detail-cover-theme_1 .ynresume-cover-description .ynresume-cover-description-position,
    #ynresume_cover_wrapper.ynresume-detail-cover-theme_1 .ynresume-cover-description .ynresume-cover-description-title {
    	color : <?php echo $settings->getSetting( 'ynresume_'.$theme.'_general_info_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;	
    }

    /*** style 2 ***/
    #ynresume_cover_wrapper.ynresume-detail-cover-theme_2 .ynresume-cover-description .ynresume-cover-description-title {
		background-color: <?php echo $settings->getSetting( 'ynresume_'.$theme.'_general_info_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
	}

	#ynresume_cover_wrapper.ynresume-detail-cover-theme_2 .ynresume-cover-description .ynresume-cover-description-position {
    	color : <?php echo $settings->getSetting( 'ynresume_'.$theme.'_general_info_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;	
    }

    /*** style 3 ***/
    #ynresume_cover_wrapper.ynresume-detail-cover-theme_3 .ynresume-cover-description .ynresume-cover-description-position,
    #ynresume_cover_wrapper.ynresume-detail-cover-theme_3 .ynresume-cover-description .ynresume-cover-description-title {
    	color : <?php echo $settings->getSetting( 'ynresume_'.$theme.'_general_info_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
    }

    /*** style 4 ***/
    #ynresume_cover_wrapper.ynresume-detail-cover-theme_4 .ynresume-cover-description .ynresume-cover-description-position,
    #ynresume_cover_wrapper.ynresume-detail-cover-theme_4 .ynresume-cover-description .ynresume-cover-description-title {
    	color : <?php echo $settings->getSetting( 'ynresume_'.$theme.'_general_info_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
    }

    #ynresume_cover_wrapper.ynresume-detail-cover-theme_4 .ynresume-cover-photo a {
    	border-color: <?php echo $settings->getSetting( 'ynresume_'.$theme.'_general_info_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
    }
</style>
<?php endif; ?>

<div id="ynresume_cover_wrapper" class="ynresume-detail-cover-<?php echo $resume->theme; ?>">
	<div id="cover_photo" class="ynresume-cover-photo">
		<?php echo Engine_Api::_()->ynresume()->getPhotoSpan($resume, 'thumb.main'); ?>
	</div>

	<div class="ynresume-cover-content">	
		<div class="ynresume-cover-description">
			<?php if(!empty($resume)):?>  

				<div class="ynresume-cover-description-title">
					<?php echo $resume -> name;?>
				</div>
				
				<!-- BADGE -->
				<?php $badge = $resume -> getBadge();?>
				<?php if(!empty($badge)) :?>
					<?php $title = $badge -> getTitle();?>	
					<div title="<?php echo $this -> translate($title);?>" class="ynresume-cover-description-badge">
						<?php echo $this -> itemPhoto($badge);?>
					</div>
				<?php endif;?>
				
				<div class="ynresume-cover-description-position">
					<?php echo $resume -> headline;?>
				</div>

				<div class="ynresume-cover-description-subline">
					<?php if(!empty($resume -> location)):?>
					<span class="ynresume-cover-location1"><i class="fa fa-map-marker"></i> <?php echo $resume -> location;?></span>
					<?php endif;?>
					
					<?php $industry = $resume -> getIndustry();?>			
					<?php if(!empty($industry)):?>				
						<span>
							<i class="fa fa-folder-open"></i>
							<?php echo $this -> htmlLink($resume -> getIndustry() -> getHref(), $resume -> getIndustry() -> getTitle());?>
						</span>
					<?php endif;?>	
				</div>
				
				
				<div class="ynresume-cover-description-subline">				

					<?php
						$tableExperiences = Engine_Api::_() -> getDbTable('experiences', 'ynresume');
						$currentExperiences = $tableExperiences -> getExperiencesByResumeId($resume -> getIdentity(), true, 3);
						if(count($currentExperiences) > 0)
						{
							$business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
							$experiences = array();
							foreach ($currentExperiences as $experience){
								$business = null; 
			                    if ($experience->business_id) {
			                        $business = ($business_enable) ? Engine_Api::_()->getItem('ynbusinesspages_business', $experience->business_id) : null;
			                    }
								if ($business && !$business->deleted) {
									$experiences[] = $business;
								}else{
									$experiences[] = $experience -> company;
								}
							}
							echo '<div class="ynresume-cover-description-info"><label>'.$this -> translate('Current').'</label><span>'.implode(", ", $experiences).'</span></div>';
						}
					?>

					<!-- Location -->
					<?php if(!empty($industry)):?>				
						<div class="ynresume-cover-description-info ynresume-cover-location2"><i class="fa fa-map-marker"></i><label><?php echo $this -> translate('Location');?></label><span><?php echo $resume -> location;?></span></div>						
					<?php endif;?>
					
					<?php
						$previousExperiences = $tableExperiences -> getExperiencesByResumeId($resume -> getIdentity(), false, 3);
						if(count($previousExperiences) > 0)
						{
							$business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
							$experiences_arr = array();
							foreach ($previousExperiences as $experience){
								$business = null; 
			                    if ($experience->business_id) {
			                        $business = ($business_enable) ? Engine_Api::_()->getItem('ynbusinesspages_business', $experience->business_id) : null;
			                    }
								if ($business && !$business->deleted) {
									$experiences_arr[] = $business;
								}else{
									$experiences_arr[] = $experience -> company;
								}
							}
							echo '<div class="ynresume-cover-description-info"><label>'.$this->translate('Previous').'</label><span>'.implode(", ", $experiences_arr).'</span></div>';
						}
					?>	

					<!-- education -->		
					<?php
						$tableEducations = Engine_Api::_() -> getDbTable('educations', 'ynresume');
						$educations = $tableEducations -> getEducationsByResumeId($resume -> getIdentity(), 3);
						if(count($educations) > 0)
						{
							$educations_arr = array();
							foreach ($educations as $education){
								$educations_arr[] = $education -> title;
							}
							echo '<div class="ynresume-cover-description-info"><i class="fa fa-graduation-cap"></i><label>'.$this -> translate('Education').'</label><span>'.implode(", ", $educations_arr).'</span></div>';
						}
					?>								
				</div>
				
				<!-- button -->
				<?php if($viewer -> isSelf($resume -> getOwner())):?>
					<a href="<?php echo $this -> url(array('action' => 'manage'), 'ynresume_general', true) ?>" class="button bold"><?php echo $this -> translate('Edit');?></a>
				<?php endif;?>
				<a class="button bold" href="<?php echo $resume -> getOwner() -> getHref();?>"><?php echo $this -> translate('View Profile') ;?></a>
				<?php if($viewer -> getIdentity() && !$viewer -> isSelf($resume -> getOwner())):?>
				<a class="button bold" href="<?php echo $this -> url(array('controller' => 'resume', 'action' => 'export-pdf', 'resume_id' => $resume->getIdentity()), 'ynresume_extended');?>"><?php echo $this -> translate('Save to PDF');?></a>
				<a class="button bold ynresume_favourite_<?php echo $resume -> getIdentity();?>" onclick="favouriteResume('<?php echo $resume -> getIdentity() ;?>');" href="javascript:;"><?php echo ($resume -> hasFavourited()) ? $this -> translate('Unfavourite') : $this -> translate('Favourite');?></a>
				<a class="button bold" id="ynresume_save_<?php echo $resume -> getIdentity();?>" onclick="saveResume('<?php echo $resume -> getIdentity() ;?>');" href="javascript:;"><?php echo ($resume -> hasSaved()) ? $this -> translate('Saved') : $this -> translate('Save');?></a>
				<a class='button bold smoothbox' href="<?php echo $this->url(array('action' => 'compose-message', 'to' => $resume -> getOwner() -> getIdentity()), 'ynresume_general', true);?>"><?php echo $this -> translate('Message');?></a>
				<?php if($resume -> hasSkill()):?>
					<a class="button bold" onclick="focusSkill(); return false;"><?php echo $this -> translate('Endorse') ;?></a>
				<?php endif;?>
				<?php 
				$owner = $resume -> getOwner();
				if ($owner->membership()->isMember($viewer)) :
				?>
					<a class="button bold" href="<?php echo $this -> url(array('action' => 'give', 'receiver_id' => $resume -> getOwner() -> getIdentity()), 'ynresume_recommend', true);?>"><?php echo $this -> translate('Recommend') ;?></a>
				<?php endif;?>
				<?php endif;?>
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynresume_addthis_pubid', 'younet');?>" async="async"></script>
				<div class="addthis_sharing_toolbox"></div>
			<?php endif;?>
		</div>
	</div>
</div>

<script type="text/javascript">
	<?php if($resume -> hasSkill()):?>
		Element.implement({
		        setFocus: function(index) {
		            this.setAttribute('tabIndex',index || 0);
		            this.focus();
		        }
		    });
		window.addEvent('domready', function() {
			
		    <?php if($this -> endorse) :?>
		    	focusSkill();
		    <?php endif;?>
		});
	<?php endif;?>

	function focusSkill()
	{
		if($('sections-content-item_skill'))
			$('sections-content-item_skill').setFocus();
	}
	
	<?php if($viewer -> getIdentity() &&  !$viewer -> isSelf($resume -> getOwner())):?>
		function saveResume(id)
		{
			new Request.JSON({
		        url: '<?php echo $this->url(array('action' => 'save'), 'ynresume_general', true); ?>',
		        method: 'post',
		        data : {
		        	format: 'json',
		            'id' : id
		        },
		        onComplete: function(responseJSON, responseText) {
		            if (responseJSON.save == '1')
		            {
		            	$("ynresume_save_"+id).set("html", '<?php echo $this -> translate("Saved");?>');
		            }
		            else
		            {
		            	$("ynresume_save_"+id).set("html", '<?php echo $this -> translate("Save");?>');
		            }
		            
		        }
		    }).send();
		}
	
		function favouriteResume(id)
		{
			new Request.JSON({
		        url: '<?php echo $this->url(array('action' => 'favourite'), 'ynresume_general', true); ?>',
		        method: 'post',
		        data : {
		        	format: 'json',
		            'id' : id
		        },
		        onComplete: function(responseJSON, responseText) {
		            if (responseJSON.save == '1')
		            {
		            	$$(".ynresume_favourite_"+id).each(function(el) {
		            		var icon = (el.hasClass('button')) ? '' : '<i class="fa fa-star-o"></i>';
		            		el.set("html", icon+'<?php echo $this -> translate("Unfavourite");?>');
		            	});
		            }
		            else
		            {
		            	$$(".ynresume_favourite_"+id).each(function(el) {
		            		var icon = (el.hasClass('button')) ? '' : '<i class="fa fa-star-o"></i>';
		            		el.set("html", icon+'<?php echo $this -> translate("Favourite");?>');
		            	});
		            }
		            
		        }
		    }).send();
		}
	<?php endif;?>
</script>