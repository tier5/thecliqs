<?php
	$this -> idName = 'job-listing';
	$this -> idNidPrefixame = 'JobListing';
	$this -> widgetId = $this->identity;
?>
<script type="text/javascript">
    //check open popup
    function checkOpenPopup(url) {
        if(window.innerWidth <= 480) {
            Smoothbox.open(url, {autoResize : true, width: 300});
        }
        else {
            Smoothbox.open(url);
        }
    }
</script>
<script type="text/javascript">
window.addEvent('domready', function() {
    function setCookie(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	function getCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1);
	        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
	    }
	    return "";
	}
    
    function setMapMode<?php echo $this->idPrefix;?>(){
       	var html =  "<?php echo $this->url(array('action'=>'display-map-view', 'type' => 'job', 'ids' => $this->jobIds), 'ynjobposting_general') ?>";
       	if (document.getElementById('map-view-iframe-<?php echo $this->idName;?>')) document.getElementById('map-view-iframe-<?php echo $this->idName;?>').dispose();
		var iframe = new IFrame({
			id : 'map-view-iframe-<?php echo $this->idName;?>',
			src: html,
			styles: {			       
				'height': '500px',
				'width' : '100%'
			},
		});
		
       	if ($('ynjobposting-browse-job-maps-<?php echo $this->idName;?>')) iframe.inject( $('ynjobposting-browse-job-maps-<?php echo $this->idName;?>') );
		if (document.getElementById('map-view-iframe-<?php echo $this->idName;?>')) document.getElementById('map-view-iframe-<?php echo $this->idName;?>').style.display = 'block';
		//document.getElementById('paginator').style.display = 'none';
    }

 	// Get cookie
	var myCookieViewMode = getCookie('ynjobposting-job-viewmode-cookie-<?php echo $this -> idName; ?>');
	if ( myCookieViewMode == '') 
	{
		myCookieViewMode = '<?php echo $this -> class_mode;?>';
	}
	if ( myCookieViewMode == '') 
	{
		myCookieViewMode = 'ynjobposting-browse-job-viewmode-list';
	}
	//$$('.ynjobposting-job-mode-view')[0].addClass( myCookieViewMode );
	if ($('ynjobposting-job-mode-view-<?php echo $this -> idName; ?>')) $('ynjobposting-job-mode-view-<?php echo $this -> idName; ?>').addClass( myCookieViewMode );
	if ($('ynjobposting-browse-listings-<?php echo $this -> idName; ?>')) $('ynjobposting-browse-listings-<?php echo $this -> idName; ?>').addClass( myCookieViewMode );

	// render MapView
	if ( myCookieViewMode == 'ynjobposting-browse-job-viewmode-maps') {
		setMapMode<?php echo $this->idPrefix;?>();
	}

	// Set click viewMode
	$$('#ynjobposting-job-mode-view-<?php echo $this -> idName;?> > span').addEvent('click', function(){
		var viewmode = this.get('rel'),
			browse_content = $('ynjobposting-browse-listings-<?php echo $this -> idName; ?>'),
			//header_mode = $$('.ynjobposting-job-mode-view')[0];
			header_mode = $('ynjobposting-job-mode-view-<?php echo $this -> idName; ?>');

		setCookie('ynjobposting-job-viewmode-cookie-<?php echo $this -> idName; ?>', viewmode, 1);

		header_mode
			.removeClass('ynjobposting-browse-job-viewmode-list')
			.removeClass('ynjobposting-browse-job-viewmode-grid')
			.removeClass('ynjobposting-browse-job-viewmode-maps');

		browse_content
			.removeClass('ynjobposting-browse-job-viewmode-list')
			.removeClass('ynjobposting-browse-job-viewmode-grid')
			.removeClass('ynjobposting-browse-job-viewmode-maps');

		header_mode.addClass( viewmode );
		browse_content.addClass( viewmode );

		// render MapView
		if ( viewmode == 'ynjobposting-browse-job-viewmode-maps') {
			setMapMode<?php echo $this->idPrefix;?>();
		} else {
			//document.getElementById('paginator').style.display = 'block';
		}
	});
});


</script>
<?php $viewer = Engine_Api::_() -> user() -> getViewer();?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div class="ynjobposting-browse-listings-header clearfix">
    
    <?php $total = $this->paginator->getTotalItemCount();?>
    <h2><?php echo $this-> translate(array("<span>%s</span> Job", "<span>%s</span> Jobs", $total), $total);?></h2>
	<div id="ynjobposting-job-mode-view-<?php echo $this->idName;?>" class="ynjobposting-job-mode-view">
		<?php if(in_array('list', $this -> mode_enabled)):?>
			<span class="ynjobposting-viewmode-list" rel="ynjobposting-browse-job-viewmode-list"></span>
		<?php endif;?>
		<?php if(in_array('grid', $this -> mode_enabled)):?>
			<span class="ynjobposting-viewmode-grid" rel="ynjobposting-browse-job-viewmode-grid"></span>
		<?php endif;?>	
		<?php if(in_array('map', $this -> mode_enabled)):?>
			<span class="ynjobposting-viewmode-maps" rel="ynjobposting-browse-job-viewmode-maps"></span>
		<?php endif;?>	
	</div>
</div>
<div id="ynjobposting-browse-listings-<?php echo $this -> idName; ?>" class="ynjobposting-browse-listings">
	<ul class="ynjobposting-clearfix">
		<?php foreach($this->paginator as $job) :?>
		<li class="ynjobposting-browse-listings-item">
			<div class="ynjobposting-browse-listings-item-image">
				<div class="ynjobposting-browse-listings-item-photo">
					<?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($job); ?>
				</div>
				<?php if($job -> featured == 1) :?>
					<span class="ynjobposting-item-featured"><?php echo $this -> translate('featured');?></span>
				<?php endif;?>
				<div class="ynjobposting-browse-listings-item-share">
					<?php if (!$job->isOwner() && $viewer->getIdentity() && !$job->hasApplied()) :?>
	                    <?php if (!$job->hasSaved()) : 
	                        $url = $this->url(array('module' => 'ynjobposting', 'controller' => 'jobs', 'action' => 'save', 'id' => $job->getIdentity()), 'ynjobposting_job', true);
	                    ?>
	                        <a href="javascript:void(0)" title='<?php echo $this -> translate('Save');?>' onclick="checkOpenPopup('<?php echo $url?>')">
	                            <i class="fa fa-floppy-o"></i>
	                        </a>
	                    <?php else: ?>
	                        <a href="javascript:void(0)" title='<?php echo $this -> translate('Saved');?>' class="disabled-link">
	                            <i class="fa fa-floppy-o"></i>
	                        </a>
	                    <?php endif; ?>
                	<?php endif; ?>    
					
					<?php $url = $this -> url(
						array('module' => 'activity', 
						'controller' => 'index', 
						'action' => 'share', 
						'type' => $job -> getType(), 
						'id' => $job -> getIdentity(),
						'format' => 'smoothbox')
						,'default', true);
	            	?>
					<a href="javascript:void(0)" title='<?php echo $this -> translate('Share');?>' onclick="checkOpenPopup('<?php echo $url?>')">
						<i class="fa fa-share-alt"></i>
					</a>
				</div>
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
			<div class="ynjobposting-browse-listings-item-share">
				<?php if (!$job->isOwner() && $viewer->getIdentity() && !$job->hasApplied()) :?>
	                    <?php if (!$job->hasSaved()) : 
	                        $url = $this->url(array('module' => 'ynjobposting', 'controller' => 'jobs', 'action' => 'save', 'id' => $job->getIdentity()), 'ynjobposting_job', true);
	                    ?>
	                        <a href="javascript:void(0)" title='<?php echo $this -> translate('Save');?>' onclick="checkOpenPopup('<?php echo $url?>')">
	                            <i class="fa fa-floppy-o"></i>
	                        </a>
	                    <?php else: ?>
	                        <a href="javascript:void(0)" title='<?php echo $this -> translate('Saved');?>' class="disabled-link">
	                            <i class="fa fa-floppy-o"></i>
	                        </a>
	                    <?php endif; ?>
            	<?php endif; ?>  
				<?php $url = $this -> url(
					array('module' => 'activity', 
					'controller' => 'index', 
					'action' => 'share', 
					'type' => $job -> getType(), 
					'id' => $job -> getIdentity(),
					'format' => 'smoothbox')
					,'default', true);
	            ?>
				<a href="javascript:void(0)" title='<?php echo $this -> translate('Share');?>' onclick="checkOpenPopup('<?php echo $url?>')">
					<i class="fa fa-share-alt"></i>
				</a>
			</div>
		</li>
		<?php endforeach;?>
	</ul>
	<div id="ynjobposting-browse-job-maps-<?php echo $this->idName;?>" class="ynjobposting-browse-job-maps">
		<iframe id='map-view-iframe-<?php echo $this->idName;?>' style="max-height: 500px;"></iframe>
	</div>
</div>

<div id='paginator'>
	<?php if( $this->paginator->count() > 1 ): ?>
	     <?php echo $this->paginationControl($this->paginator, null, null, array(
	            'pageAsQuery' => true,
	            'query' => $this->formValues,
	          )); ?>
	<?php endif; ?>
</div>

<?php else: ?>
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no jobs found yet.') ?>
		</span>
    </div>
<?php endif; ?>
  