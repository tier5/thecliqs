<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div class="ynjobposting-browse-listings-header ynjobposting-clearfix">
	<?php $total = $this->paginator->getTotalItemCount();?>
	<h2><?php echo $this-> translate(array("<span>%s</span> Company", "<span>%s</span> Companies", $total), $total);?></h2>

	<div id="ynjobposting-company-mode-view" class="ynjobposting-company-mode-view">
		<?php if(in_array('list', $this -> mode_enabled)):?>
			<span class="ynjobposting-viewmode-list" rel="ynjobposting-browse-company-viewmode-list"></span>
		<?php endif;?>
		<?php if(in_array('grid', $this -> mode_enabled)):?>
			<span class="ynjobposting-viewmode-grid" rel="ynjobposting-browse-company-viewmode-grid"></span>
		<?php endif;?>	
		<?php if(in_array('map', $this -> mode_enabled)):?>
			<span class="ynjobposting-viewmode-maps" rel="ynjobposting-browse-company-viewmode-maps"></span>
		<?php endif;?>	
	</div>
</div>

<div id="ynjobposting-browse-listings">
	<ul>
		<?php foreach($this->paginator as $company) :?>
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
						<a href="<?php echo $company->getHref();?>">
						<?php echo $company->name;?>
						</a>
					</div>

					<div class="ynjobposting-company-item-subline">
						<span class="ynjobposting-company-item-location">
							<i class="fa fa-map-marker"></i>
							<?php echo $company->location;?>
						</span>

						<span class="ynjobposting-company-item-size">
							<i class="fa fa-users"></i>
							<?php echo $company -> getSize(); ?>
						</span>

						<span class="ynjobposting-company-item-follower">
							<i class="fa fa-arrow-right"></i>
							<?php 
							$followerCount = $company->countFollower();
							echo $this->translate(array("%s follower", "%s followers", $followerCount), $followerCount); 
							?>
						</span>
						
						<span>
							<?php $jobCount = count($company->getJobs(true));?>
							<i class="fa fa-briefcase"></i>
                    		<?php echo $this->translate(array("%s job", "%s jobs", $jobCount), $jobCount);?>
						</span>
					</div>

					<div class="ynjobposting-company-item-industry">
						<i class="fa fa-folder-open"></i>
						<?php $list_industries = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting') -> getIndustriesByCompanyId($company -> getIdentity());?>
			        	<?php $i = 1; foreach($list_industries as $industry_row) :?>
			        		<?php $industry = Engine_Api::_() -> getItem('ynjobposting_industry', $industry_row -> industry_id); ?>
			        		<?php if($industry) :?>
				        		<a href='<?php echo $industry -> getHref(); ?>'><?php echo $industry -> title; ?></a>
				        		<?php if($i < count($list_industries)) :?>
				        			|
				        		<?php endif;?>
			        		<?php endif;?>
			        	<?php $i++; endforeach;?>
					</div>
						
					<div class="ynjobposting-company-item-viewjobs">
						<a href="<?php echo $company->getHref() . "?view=job";?>">
							<button><?php echo $this->translate("View All Jobs");?></button>
						</a>
					</div>
				</div>				
			</div>
		</li>
		<?php endforeach;?>
	</ul>
	<div id="ynjobposting-browse-company-maps" class="ynjobposting-browse-company-maps">
		<iframe id='map-view-iframe' style="max-height: 500px;"></iframe>
	</div>
</div>
<?php else: ?>
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no companies found yet.') ?>
		</span>
    </div>
<?php endif; ?>

<div id='paginator'>
	<?php if( $this->paginator->count() > 1 ): ?>
	     <?php echo $this->paginationControl($this->paginator, null, null, array(
	            'pageAsQuery' => true,
	            'query' => $this->formValues,
	          )); ?>
	<?php endif; ?>
</div>


<script type="text/javascript">
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
    
    function setMapMode(){
       	var html =  "<?php echo $this->url(array('action'=>'display-map-view', 'type' => 'company', 'ids' => $this->companyIds), 'ynjobposting_general') ?>";
       	if (document.getElementById('map-view-iframe')) document.getElementById('map-view-iframe').dispose();
		var iframe = new IFrame({
			id : 'map-view-iframe',
			src: html,
			styles: {			       
				'height': '500px',
				'width' : '100%'
			},
		});
		
       	if ($('ynjobposting-browse-company-maps')) iframe.inject( $('ynjobposting-browse-company-maps') );
		if (document.getElementById('map-view-iframe')) document.getElementById('map-view-iframe').style.display = 'block';
		//document.getElementById('paginator').style.display = 'none';
    }

 	// Get cookie
	var myCookieViewMode = getCookie('ynjobposting-company-viewmode-cookie');
	if ( myCookieViewMode == '') 
	{
		myCookieViewMode = '<?php echo $this -> class_mode;?>';
	}
	if ( myCookieViewMode == '') 
	{
		myCookieViewMode = 'ynjobposting-browse-company-viewmode-list';
	}
	if ($$('.ynjobposting-company-mode-view').length) $$('.ynjobposting-company-mode-view')[0].addClass( myCookieViewMode );
	if ($('ynjobposting-browse-listings')) $('ynjobposting-browse-listings').addClass( myCookieViewMode );

	// render MapView
	if ( myCookieViewMode == 'ynjobposting-browse-company-viewmode-maps') {
		setMapMode();
	}

	// Set click viewMode
	$$('.ynjobposting-company-mode-view > span').addEvent('click', function(){
		var viewmode = this.get('rel'),
			browse_content = $('ynjobposting-browse-listings'),
			header_mode = $$('.ynjobposting-company-mode-view')[0];

		setCookie('ynjobposting-company-viewmode-cookie', viewmode, 1);

		header_mode
			.removeClass('ynjobposting-browse-company-viewmode-list')
			.removeClass('ynjobposting-browse-company-viewmode-grid')
			.removeClass('ynjobposting-browse-company-viewmode-maps');

		browse_content
			.removeClass('ynjobposting-browse-company-viewmode-list')
			.removeClass('ynjobposting-browse-company-viewmode-grid')
			.removeClass('ynjobposting-browse-company-viewmode-maps');

		header_mode.addClass( viewmode );
		browse_content.addClass( viewmode );

		// render MapView
		if ( viewmode == 'ynjobposting-browse-company-viewmode-maps') {
			setMapMode();
		} else {
			//document.getElementById('paginator').style.display = 'block';
		}
	});

	$$('.ynjobposting-item-more-option > span.ynjobposting-item-more-btn').addEvent('click', function() {
		this.getParent('.ynjobposting-item-more-option').toggleClass('ynjobposting-item-show-option');
	});
 </script>   