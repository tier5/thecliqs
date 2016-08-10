<?php $submissionForm = $this -> submissionForm;?>

<?php if ($submissionForm->form_title):?>
	<h3><?php echo $submissionForm->form_title; ?></h3>
<?php endif;?>

<?php if ($submissionForm->form_description):?>
	<div class="ynjobposting-apply-description"><?php echo $submissionForm->form_description; ?></div>
<?php endif;?>

<div class="ynjobposting-apply-info clearfix">
    <?php if ($submissionForm->show_company_logo):?>
    	<div class="ynjobposting-apply-image">
    		<?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($this->company, 'thumb.profile'); ?>
    	</div>
    <?php endif;?>

    <div class="ynjobposting-apply-content">
        <?php if ($submissionForm->show_job_title):?>
        	<div class="ynjobposting-apply-info-title">
        		<?php echo $this -> htmlLink($this->job->getHref(), $this->job->getTitle());?>
        	</div>
        <?php endif;?>

        <?php if ($submissionForm->show_job_title):?>
        	<div class="ynjobposting-apply-info-company">
        		<?php echo $this -> htmlLink($this->company->getHref(), $this->company->getTitle());?>
        	</div>
        <?php endif;?>

        <?php if ($submissionForm->show_job_location && $this->job->working_place):?>
        	<div class="ynjobposting-apply-working">
                <i class="fa fa-map-marker"></i>
        		<?php echo $this->job->working_place;?>
        	</div>
        <?php endif;?>
    </div>
</div>

<div class='global_form'>
  <?php echo $this->form->render($this) ?>
</div>

<script type="text/javascript">
	
    window.addEvent('domready', function() {
    	
    	<?php if (Engine_Api::_()->hasModuleBootstrap('ynresume') && Engine_Api::_() -> ynresume() -> getResumeByUserId()) :?>
	    	
	    	<?php if(isset($this -> posts) && isset($this -> posts['resume'])) :?>
	    		<?php if($this -> posts['resume']):?>
	    			$('upload_files-element').setStyle('display', 'none');
	    		<?php else:?>
	    			$('upload_files-element').setStyle('display', 'block');
	    		<?php endif;?>
	    	<?php endif;?>
	    	
	    	$('resume').addEvent('change', function(){
	    		var checked = this.get('checked');
	    		if(checked) {
	    			$('upload_files-element').setStyle('display', 'none');
	    		} else {
	    			$('upload_files-element').setStyle('display', 'block');
	    		}
	    	});
    	<?php endif;?>
    	
        //event when choose resume video type
        $$('input[name="resume_video"]').addEvent('click', function() {
            if (this.value == 1) {
                $('video_link-wrapper').removeClass('hidden');
                if ($('video_id-wrapper')) {
                    $('video_id-wrapper').addClass('hidden');
                }
                if ($('no_video-wrapper')) {
                    
                    $('no_video-wrapper').addClass('hidden');
                }
            }
            else {
                $('video_link-wrapper').addClass('hidden');
                if ($('no_video-wrapper')) {
                    $('no_video-wrapper').removeClass('hidden');
                }
                else  if ($('video_id-wrapper')) {
                    $('video_id-wrapper').removeClass('hidden');
                }
            }    
        });
    });
    
    //default video type
    if ($('video_id-wrapper')) {
        $('video_id-wrapper').addClass('hidden');
    }
    if ($('no_video-wrapper')) {
        $('no_video-wrapper').addClass('hidden');
    }
    
    //add click event on refresh video
    if ($('refresh-video')) {
        $('refresh-video').addEvent('click', function(){
            var url = '<?php echo $this->url(array('controller' => 'jobs','action' => 'get-videos'), 'ynjobposting_job', true) ?>';
            var request = new Request.JSON({
                url : url,
                onSuccess : function(responseJSON) {
                    var videos = responseJSON.json;
                    var select = $('video_id');
                    Object.keys(videos).forEach(function (key) {
                       var option = new Element('option', {
                           value: key,
                           text: videos[key],
                        });
                        select.grab(option);
                    });
                    if (select.getChildren().length > 0) {
                        $('video_id-wrapper').removeClass('hidden');
                        $('no_video-wrapper').destroy();
                    }
                }
            });
            request.send();
        });
    }
   
    //add onsubmit event on form
    if ($('apply_job_form')) {
        $('apply_job_form').addEvent('submit', function() {
        	
            var hasUploadFiles = ($('upload_files').value == '') ? false : true;
            var hasVideo = false;
            if ($('resume_video-wrapper')) {
                var value = $$('input[name="resume_video"]:checked')[0].value;
                if (value == 1) {
                    hasVideo = ($('video_link').value == '') ? false : true;
                }
                else if (value == 2) {
                    hasVideo = ($('video_id').value == '') ? false : true;
                } 
            }
            
            var checked = true;
            <?php if (Engine_Api::_()->hasModuleBootstrap('ynresume') && Engine_Api::_() -> ynresume() -> getResumeByUserId()) :?>
	    		 checked = $('resume').get('checked');
	    	<?php endif;?>
            
            if (!hasUploadFiles && !hasVideo && !checked) {
                var div = new Element('div', {
                   'class': 'apply-confirm', 
                });
                var h3 = new Element('h3', {
                    text: '<?php echo $this->translate('WARNING')?>'
                })
                var p = new Element('p', {
                    text: '<?php echo $this->translate('You do not upload any file for your application. Do you want to process anyway.')?>'
                })
                var button = new Element('button', {
                   text: '<?php echo $this->translate('Apply')?>',
                   onclick: 'parent.Smoothbox.close();$("apply_job_form").submit();',
                });
                var cancel = new Element('button', {
                    text: '<?php echo $this->translate('Cancel')?>',
                    onclick: 'parent.Smoothbox.close()',
                });
                div.grab(h3);
                div.grab(p);
                div.grab(button);
                div.grab(cancel);
                Smoothbox.open(div);
                return false;
            }    
        });
    }
</script>
