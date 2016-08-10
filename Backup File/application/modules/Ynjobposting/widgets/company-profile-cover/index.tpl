<?php
$coverPhotoUrl = "";
if ($this->company->cover_photo)
{
	$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->company->cover_photo)->current();
	$coverPhotoUrl = $coverFile->map();
}
?>

<?php $session = new Zend_Session_Namespace('mobile');
if(!$session -> mobile){?>

<div class="ynjobposting-company-widget-profile-cover">
	<?php
		$photoUrl = $this->company->getPhotoUrl();
		$companyPhotoUrl = ($photoUrl)
			? ($photoUrl)
			: $this->layout()->staticBaseUrl . 'application/modules/Ynjobposting/externals/images/no_image.png';
	?>
    <?php if ($coverPhotoUrl!="") : ?>
    <div class="ynjobposting-company-profile-cover-picture">
        <span class="profile-cover-picture-span" style="background-image: url(<?php echo $coverPhotoUrl; ?>);"></span>
    </div>
    <?php else : ?>
    <div class="ynjobposting-company-profile-cover-picture">
        <span class="profile-cover-picture-span" style="background-image: url('application/modules/Ynjobposting/externals/images/company_default_cover.jpg');"></span>
    </div>
    <?php endif; ?>

    <div class="ynjobposting-company-profile-cover-avatar">
        <span style="background-image: url(<?php echo $companyPhotoUrl; ?>);"></span>
    </div>

    <div class="ynjobposting-company-detail-info">
        <div class="info-top ynclearfix">
            <div class="ynjobposting-company-detail-action">
            	<!-- report -->
                 <?php if($this->aReportButton):?>
					<a title='<?php echo $this -> translate('Report')?>' class = "<?php if(!empty($this->aReportButton['class'])) echo $this->aReportButton['class'];?>" href="<?php echo $this->url($this->aReportButton['params'], $this->aReportButton['route'], array());?>" >
						<i class="fa fa-exclamation-circle"></i>
					</a>
                <?php endif;?>
            
            	<!-- share -->
                 <?php if($this->aShareButton):?>
					<a title='<?php echo $this -> translate('Share')?>' class = "<?php if(!empty($this->aShareButton['class'])) echo $this->aShareButton['class'];?>" href="<?php echo $this->url($this->aShareButton['params'], $this->aShareButton['route'], array());?>" >
						<i class="fa fa-share-alt"></i>
					</a>
                <?php endif;?>

                <!-- manage posted job -->
                 <?php if($this->aManagePostedJobButton):?>
                    <a class = "<?php if(!empty($this->aManagePostedJobButton['class'])) echo $this->aManagePostedJobButton['class'];?>" href="<?php echo $this->url($this->aManagePostedJobButton['params'], $this->aManagePostedJobButton['route'], true);?>" title="<?php echo $this -> translate($this->aManagePostedJobButton['label'])?>">
                        <i class="fa fa-briefcase"></i>
                    </a>
                <?php endif;?>

                <!-- view applications -->
                 <?php if($this->aViewApplicationsButton):?>
                    <a class = "<?php if(!empty($this->aViewApplicationsButton['class'])) echo $this->aViewApplicationsButton['class'];?>" href="<?php echo $this->url($this->aViewApplicationsButton['params'], $this->aViewApplicationsButton['route'], true);?>" title="<?php echo $this -> translate($this->aViewApplicationsButton['label'])?>">                        
                        <i class="fa fa-paperclip"></i>
                    </a>
                <?php endif;?>

                <!-- follow -->
                <?php if($this->aFollowButton):?>
                    <a class = "<?php if(!empty($this->aFollowButton['class'])) echo $this->aFollowButton['class'];?> <?php echo $this -> translate($this->aFollowButton['label'])?>" href="<?php echo $this->url($this->aFollowButton['params'], $this->aFollowButton['route'], array());?>" title="<?php echo $this -> translate($this->aFollowButton['label'])?>">
                        <i class="fa fa-arrow-right"></i>
                    </a>
                <?php endif;?>
                
                <!-- contact -->
                <?php if($this->aContactButton):?>
                    <a class = "<?php if(!empty($this->aContactButton['class'])) echo $this->aContactButton['class'];?>" href="<?php echo $this -> aContactButton['href']?>" title="<?php echo $this -> translate($this->aContactButton['label'])?>">
                        <i class="fa fa-envelope"></i>
                    </a>
                <?php endif;?>
				   
				<?php $canDetailSettings = ($this->aSponsorButton || $this->aEditButton || $this->aEditSubmissionFormButton || $this->aCloseButton || $this->aDeleteButton)?>
                <?php if ($this->viewer()->getIdentity() && $canDetailSettings): ?>
                	<div id="ynjobposting-company_widget_cover_settings"><i class="fa fa-cog"></i></div>
                <?php endif;?>
            </div>            
            <div class="ynjobposting-company-detail-main">
                <div class="ynjobposting-company-detail-name"><?php echo $this->company->name;?></div>
                <div class="ynjobposting-company-detail-job-count">
                	<i class="fa fa-briefcase"></i>
                	<?php echo $this->translate(array("%s job", "%s jobs", $this->number_of_jobs), $this->number_of_jobs);?>
                </div>
                <?php if($this->company->location):?>
                <div class="ynjobposting-company-detail-location">
                	<i class="fa fa-map-marker"></i>
                	<?php echo $this->company->location;?>
                </div>
                <?php endif;?>
            </div>
            
            <div class="ynjobposting-company-detail-setting">                
                <!-- sponsor -->
                 <?php if($this->aSponsorButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aSponsorButton['class'])) echo $this->aSponsorButton['class'];?>" href="<?php echo $this->url($this->aSponsorButton['params'], $this->aSponsorButton['route'], array());?>" >
						<?php echo $this -> translate($this->aSponsorButton['label'])?>
					</a>
				</div>
                <?php endif;?>
                
                 <!-- edit info -->
                 <?php if($this->aEditButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aEditButton['class'])) echo $this->aEditButton['class'];?>" href="<?php echo $this->url($this->aEditButton['params'], $this->aEditButton['route'], array());?>" >
						<?php echo $this -> translate($this->aEditButton['label'])?>
					</a>
				</div>
                <?php endif;?>
                
                <!-- edit submission form -->
                 <?php if($this->aEditSubmissionFormButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aEditSubmissionFormButton['class'])) echo $this->aEditSubmissionFormButton['class'];?>" href="<?php echo $this->url($this->aEditSubmissionFormButton['params'], $this->aEditSubmissionFormButton['route'], true);?>" >
						<?php echo $this -> translate($this->aEditSubmissionFormButton['label'])?>
					</a>
				</div>
                <?php endif;?>
                
                <!-- close/publish  -->
                 <?php if($this->aCloseButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aCloseButton['class'])) echo $this->aCloseButton['class'];?>" href="<?php echo $this->url($this->aCloseButton['params'], $this->aCloseButton['route'], array());?>" >
						<?php echo $this -> translate($this->aCloseButton['label'])?>
					</a>
				</div>
                <?php endif;?>
                
                <!-- delete  -->
                 <?php if($this->aDeleteButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aDeleteButton['class'])) echo $this->aDeleteButton['class'];?>" href="<?php echo $this->url($this->aDeleteButton['params'], $this->aDeleteButton['route'], array());?>" >
						<?php echo $this -> translate($this->aDeleteButton['label'])?>
					</a>
				</div>
                <?php endif;?>
                
            </div>
        </div>
    </div>
</div>

<div class="ynjobposting-description" style="margin-top: 15px;">
<?php echo $this->company->description; ?>
</div>

<script type="text/javascript">
	$$('#ynjobposting-company_widget_cover_settings').addEvent('click', function(){
		$$('.ynjobposting-company-detail-setting')[0].toggle();
	});
</script>

<?php } else { ?>

<div class="ynjobposting-company-widget-profile-cover">
    <?php
        $photoUrl = $this->company->getPhotoUrl();
        $companyPhotoUrl = ($photoUrl)
            ? ($photoUrl)
            : $this->layout()->staticBaseUrl . 'application/modules/Ynjobposting/externals/images/no_image.png';
    ?>
    <?php if ($coverPhotoUrl!="") : ?>
    <div class="ynjobposting-company-profile-cover-picture">
        <span class="profile-cover-picture-span" style="background-image: url(<?php echo $coverPhotoUrl; ?>);"></span>
    </div>
    <?php else : ?>
    <div class="ynjobposting-company-profile-cover-picture">
        <span class="profile-cover-picture-span" style="background-image: url('application/modules/Ynjobposting/externals/images/company_default_cover.jpg');"></span>
    </div>
    <?php endif; ?>

    <div class="ynjobposting-company-profile-cover-avatar">
        <span style="background-image: url(<?php echo $companyPhotoUrl; ?>);"></span>
    </div>

    <div class="ynjobposting-company-detail-info">
        <div class="info-top ynclearfix">                        
            <div class="ynjobposting-company-detail-main">
                <div class="ynjobposting-company-detail-name"><?php echo $this->company->name;?></div>
                <div class="ynjobposting-company-detail-job-count">
                    <i class="fa fa-briefcase"></i>
                    <?php echo $this->translate(array("%s job", "%s jobs", $this->number_of_jobs), $this->number_of_jobs);?>
                </div>
                <?php if($this->company->location):?>
                <div class="ynjobposting-company-detail-location">
                    <i class="fa fa-map-marker"></i>
                    <?php echo $this->company->location;?>
                </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>

<div class="ynjobposting-description" style="margin-top: 15px;">
<?php echo $this->company->description; ?>
</div>

<div class="ynjobposting-company-detail-action">
    <!-- report -->
     <?php if($this->aReportButton):?>
        <a title='<?php echo $this -> translate('Report')?>' class = "<?php if(!empty($this->aReportButton['class'])) echo $this->aReportButton['class'];?>" href="<?php echo $this->url($this->aReportButton['params'], $this->aReportButton['route'], array());?>" >
            <?php echo $this -> translate('Report')?>
        </a>
    <?php endif;?>

    <!-- share -->
     <?php if($this->aShareButton):?>
        <a title='<?php echo $this -> translate('Share')?>' class = "<?php if(!empty($this->aShareButton['class'])) echo $this->aShareButton['class'];?>" href="<?php echo $this->url($this->aShareButton['params'], $this->aShareButton['route'], array());?>" >
            <?php echo $this -> translate('Share')?>
        </a>
    <?php endif;?>
    
    <?php $canDetailSettings = ($this->aFollowButton || $this->aContactButton || $this->aSponsorButton || $this->aEditButton || $this->aEditSubmissionFormButton || $this->aViewApplicationsButton || $this->aManagePostedJobButton || $this->aCloseButton || $this->aDeleteButton)?>   
    <?php if ($this->viewer()->getIdentity() && $canDetailSettings): ?>
        <div id="ynjobposting-company_widget_cover_settings">
            <?php echo $this -> translate('More')?>
            <i class="fa fa-chevron-circle-down"></i>
        </div>
    <?php endif;?>
</div>

<div class="ynjobposting-company-detail-setting">
    <!-- follow -->
     <?php if($this->aFollowButton):?>
    <div class="">
        <a class = "<?php if(!empty($this->aFollowButton['class'])) echo $this->aFollowButton['class'];?>" href="<?php echo $this->url($this->aFollowButton['params'], $this->aFollowButton['route'], array());?>" >
            <?php echo $this -> translate($this->aFollowButton['label'])?>
        </a>
    </div>
    <?php endif;?>
    
    <!-- contact -->
     <?php if($this->aContactButton):?>
    <div class="">
        <a class = "<?php if(!empty($this->aContactButton['class'])) echo $this->aContactButton['class'];?>" href="<?php echo $this -> aContactButton['href']?>">
            <?php echo $this -> translate($this->aContactButton['label'])?>
        </a>
    </div>
    <?php endif;?>
    
    <!-- sponsor -->
     <?php if($this->aSponsorButton):?>
    <div class="">
        <a class = "<?php if(!empty($this->aSponsorButton['class'])) echo $this->aSponsorButton['class'];?>" href="<?php echo $this->url($this->aSponsorButton['params'], $this->aSponsorButton['route'], array());?>" >
            <?php echo $this -> translate($this->aSponsorButton['label'])?>
        </a>
    </div>
    <?php endif;?>
    
     <!-- edit info -->
     <?php if($this->aEditButton):?>
    <div class="">
        <a class = "<?php if(!empty($this->aEditButton['class'])) echo $this->aEditButton['class'];?>" href="<?php echo $this->url($this->aEditButton['params'], $this->aEditButton['route'], array());?>" >
            <?php echo $this -> translate($this->aEditButton['label'])?>
        </a>
    </div>
    <?php endif;?>
    
    <!-- edit submission form -->
     <?php if($this->aEditSubmissionFormButton):?>
    <div class="">
        <a class = "<?php if(!empty($this->aEditSubmissionFormButton['class'])) echo $this->aEditSubmissionFormButton['class'];?>" href="<?php echo $this->url($this->aEditSubmissionFormButton['params'], $this->aEditSubmissionFormButton['route'], array());?>" >
            <?php echo $this -> translate($this->aEditSubmissionFormButton['label'])?>
        </a>
    </div>
    <?php endif;?>
    
    <!-- view applications -->
     <?php if($this->aViewApplicationsButton):?>
    <div class="">
        <a class = "<?php if(!empty($this->aViewApplicationsButton['class'])) echo $this->aViewApplicationsButton['class'];?>" href="<?php echo $this->url($this->aViewApplicationsButton['params'], $this->aViewApplicationsButton['route'], array());?>" >
            <?php echo $this -> translate($this->aViewApplicationsButton['label'])?>
        </a>
    </div>
    <?php endif;?>
    
    <!-- manage posted job -->
     <?php if($this->aManagePostedJobButton):?>
    <div class="">
        <a class = "<?php if(!empty($this->aManagePostedJobButton['class'])) echo $this->aManagePostedJobButton['class'];?>" href="<?php echo $this->url($this->aManagePostedJobButton['params'], $this->aManagePostedJobButton['route'], array());?>" >
            <?php echo $this -> translate($this->aManagePostedJobButton['label'])?>
        </a>
    </div>
    <?php endif;?>
    
    <!-- close/publish  -->
     <?php if($this->aCloseButton):?>
    <div class="">
        <a class = "<?php if(!empty($this->aCloseButton['class'])) echo $this->aCloseButton['class'];?>" href="<?php echo $this->url($this->aCloseButton['params'], $this->aCloseButton['route'], array());?>" >
            <?php echo $this -> translate($this->aCloseButton['label'])?>
        </a>
    </div>
    <?php endif;?>
    
    <!-- delete  -->
     <?php if($this->aDeleteButton):?>
    <div class="">
        <a class = "<?php if(!empty($this->aDeleteButton['class'])) echo $this->aDeleteButton['class'];?>" href="<?php echo $this->url($this->aDeleteButton['params'], $this->aDeleteButton['route'], array());?>" >
            <?php echo $this -> translate($this->aDeleteButton['label'])?>
        </a>
    </div>
    <?php endif;?>
    
</div>

<script type="text/javascript">
    $$('#ynjobposting-company_widget_cover_settings').addEvent('click', function(){
        $$('.ynjobposting-company-detail-setting')[0].toggle();
    });
</script>

<?php } ?>