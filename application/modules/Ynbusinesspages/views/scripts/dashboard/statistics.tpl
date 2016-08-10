<?php
$this->headScript()
       ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/moo.flot.js');
?>

<?php $business = $this -> business;?>
<?php $package = $business -> getPackage() ;?>
<div class="ynbusinesspages-dashboard-statistic-top ynbusinesspages-clearfix">
	<div class="ynbusinesspages-dashboard-statistic-image">
		<?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan($business); ?>
	</div>

	<div class="ynbusinesspages-dashboard-statistic-content">
		<div class="ynbusinesspages-dashboard-statistic-title"><?php echo $this -> htmlLink($business -> getHref(), $business -> getTitle());?></div>

		<?php
			$menu = new Ynbusinesspages_Plugin_Menus();
			$aOpenCloseButton = $menu -> onMenuInitialize_YnbusinesspagesOpenCloseBusiness();
			$aDeleteButton = $menu -> onMenuInitialize_YnbusinesspagesDeleteBusiness();
		?>

		<?php 
			$expirationDateObj = null;
			$lastPaymentDateObj = null;
			$approvedDateObj = null;
			$featureDateObject = null;
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
			$featureTable = Engine_Api::_() -> getDbTable('features', 'ynbusinesspages');
			$featureRow = $featureTable -> getFeatureRowByBusinessId($business -> getIdentity());
			if($business -> featured == 1 && !empty($featureRow->expiration_date) && !is_null($featureRow->expiration_date))
			{
				$featureDateObject = new Zend_Date(strtotime($featureRow->expiration_date));
			}
			if( isset($this->viewer) && $this->viewer->getIdentity() ) {
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
				if (!is_null($featureDateObject))
				{
					$featureDateObject->setTimezone($tz);
				}
		    }
		?>
		<div>
			<?php echo $this -> translate('Expire') ;?>: 
			<b class="red"><?php echo (!is_null($expirationDateObj)) ? date('M d Y', $expirationDateObj -> getTimestamp())  : ''; ?></b>
			<?php if(!empty($expirationDateObj)) :?>
			(<i><?php echo $this -> translate('Approved At') ;?>: <?php echo (!is_null($approvedDateObj)) ?  date('M d Y', $approvedDateObj -> getTimestamp()) : ''; ?></i>)
			<?php endif;?>
		</div>
		
		<div>
			<?php echo $this -> translate('Status');?>: <b class="link"><?php echo $this -> translate(ucfirst($business -> status))?></b>
		</div>

		<?php if($package -> getIdentity() > 0):?>
			<div>
				<span><?php echo $this -> translate('Package'); ?>: <b class="link"><?php echo $package -> getTitle();?></span></b> |
				<span><?php echo $this -> translate('Price'); ?>: <b class="link"><?php echo $this -> locale()->toCurrency($package -> price, $this->currency) ?></b></span>
			</div> 
		<?php endif;?>

		<div><?php echo $this -> translate('Last Payment Date') ;?>: <b><?php echo (!is_null($lastPaymentDateObj)) ? date('M d Y', $lastPaymentDateObj -> getTimestamp()): ''; ?></b></div>

		<?php if($aOpenCloseButton) :?>
			<!-- close/publish  -->
			<a class = "button <?php
				if (!empty($aOpenCloseButton['class']))
					echo $aOpenCloseButton['class'];
			?>" href="<?php echo $this -> url($aOpenCloseButton['params'], $aOpenCloseButton['route'], array()); ?>" > 
				<?php echo $this -> translate($aOpenCloseButton['label']) ?>
			</a>
		<?php endif;?>
			
			<?php if($aDeleteButton) :?>
			<!-- delete  -->
			<a class = "button <?php
				if (!empty($aDeleteButton['class']))
					echo $aDeleteButton['class'];
			?>" href="<?php echo $this -> url($aDeleteButton['params'], $aDeleteButton['route'], array()); ?>" > 
				<?php echo $this -> translate($aDeleteButton['label']) ?>
			</a>
		<?php endif;?>
	</div>
</div>

<div class="ynbusinesspages-dashboard-statistics-list">
	<ul>
		<li>
			<div>
				<?php echo $this -> translate('Reviews') ;?>:
				<span><?php echo $business -> getReviewCount() ;?></span>
			</div>
		</li>
		<li>
			<div>
				<?php echo $this -> translate('Rating') ;?>: 
				<span><?php echo $business -> getRating() ;?></span>
			</div>
		</li>
		<li>
			<div>
				<?php echo $this -> translate('Members') ;?>: 
				<span><?php echo $business -> getMemberCount() ;?></span>
			</div>
		</li>
		<li>
			<div>
				<?php echo $this -> translate('Followers') ;?>: 
				<span><?php echo $business -> getFollowerCount() ;?></span>
			</div>
		</li>
		<li>
			<div>
				<?php echo $this -> translate('Liked') ;?>: 
				<span><?php echo $business -> like_count ;?></span>
			</div>
		</li>
		<li>
			<div>
				<?php echo $this -> translate('Commented') ;?>: 
				<span><?php echo $business -> getCommentCount() ;?></span>
			</div>
		</li>
		<li>
			<div>
				<?php echo $this -> translate('Shared') ;?>: 
				<span><?php echo $business -> getTotalShare() ;?></span>
			</div>
		</li>
		<li>
			<div>
				<?php echo $this -> translate('Viewed') ;?>: 
				<span><?php echo $business -> view_count ;?></span>
			</div>
		</li>

		<?php if ($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('ynwiki_page') && Engine_Api::_() -> hasModuleBootstrap('ynwiki')) :?> 
		<li>
			<div>
				<?php echo $this -> translate('Pages') ;?>: 
				<span><?php echo $business -> getPagesCount();?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if ($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('event') && Engine_Api::_() -> hasModuleBootstrap('event')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Events') ;?>: 
				<span><?php echo $business -> countItemMapping(array('event'));?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if ($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('ynbusinesspages_album')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Photos') ;?>: 
				<span><?php echo $business -> getAlbumPhotosCount();?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if ($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('video') && Engine_Api::_()->hasItemType('video')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Videos') ;?>: 
				<span><?php echo $business -> countItemMapping(array('video'));?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if ($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('ynfilesharing_folder') && Engine_Api::_() -> hasModuleBootstrap('ynfilesharing')) :?>
		<li>
			<div>
				<?php echo $this -> translate('File Sharing') ;?>: 
				<span><?php echo $business -> getFilesCount();?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if ($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('mp3music_album') && Engine_Api::_() -> hasModuleBootstrap('mp3music')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Mp3Music Albums') ;?>: 
				<span><?php echo $business -> countItemMapping(array('mp3music_album'));?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if ($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('music_playlist') && Engine_Api::_() -> hasModuleBootstrap('music')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Music Albums') ;?>: 
				<span><?php echo $business -> countItemMapping(array('music_playlist'));?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if ($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('blog') && Engine_Api::_() -> hasModuleBootstrap('blog')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Blogs') ;?>: 
				<span><?php echo $business -> countItemMapping(array('blog'));?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if($business->isViewable() && $business -> getPackage() -> checkAvailableModule('poll') && Engine_Api::_()->hasModuleBootstrap('poll')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Polls') ;?>: 
				<span><?php echo $business -> countItemMapping(array('poll'));?></span>
			</div>
		</li>
		<?php endif;?>

		<li>
			<div>
				<?php echo $this -> translate('Discussions') ;?>: 
				<span><?php echo $business -> getDiscussionsCount();?></span>
			</div>
		</li>

		<?php if($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('classified') && Engine_Api::_() -> hasModuleBootstrap('classified')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Classified') ;?>: 
				<span><?php echo $business -> countItemMapping(array('classified'));?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if($business->isViewable() && $business -> getPackage() -> checkAvailableModule('groupbuy_deal') && Engine_Api::_() -> hasModuleBootstrap('groupbuy')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Groupbuy') ;?>: 
				<span><?php echo $business -> countItemMapping(array('groupbuy_deal'));?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('yncontest_contest') && Engine_Api::_() -> hasModuleBootstrap('yncontest')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Contest') ;?>: 
				<span><?php echo $business -> countItemMapping(array('yncontest_contest'));?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if($business->isViewable() && $business -> getPackage() -> checkAvailableModule('ynlistings_listing') && Engine_Api::_()->hasModuleBootstrap('ynlistings')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Listings') ;?>: 
				<span><?php echo $business -> countItemMapping(array('ynlistings_listing'));?></span>
			</div>
		</li>
		<?php endif;?>

		<?php if($business->isViewable() && $business -> getPackage() -> checkAvailableModule('ynjobposting_job') && Engine_Api::_()->hasModuleBootstrap('ynjobposting')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Jobs') ;?>: 
				<span><?php echo $business -> countItemMapping(array('ynjobposting_job'));?></span>
			</div>
		</li>
		<?php endif;?>
		<?php if ($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('ynmusic_song') && Engine_Api::_() -> hasModuleBootstrap('ynmusic')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Social Music Albums') ;?>: 
				<span><?php echo $business -> countItemMapping(array('ynmusic_album'));?></span>
			</div>
		</li>
		<li>
			<div>
				<?php echo $this -> translate('Social Music Songs') ;?>: 
				<span><?php echo $business -> countItemMapping(array('ynmusic_song'));?></span>
			</div>
		</li>
		<?php endif;?>
		<?php if ($business -> isViewable() && $business -> getPackage() -> checkAvailableModule('ynultimatevideo_video') && Engine_Api::_() -> hasModuleBootstrap('ynultimatevideo')) :?>
		<li>
			<div>
				<?php echo $this -> translate('Ultimate Videos') ;?>:
				<span><?php echo $business -> countItemMapping(array('ynultimatevideo_video'));?></span>
			</div>
		</li>
		<?php endif;?>
	</ul>
</div>	

<div class="ynbusinesspages-dashboard-statistics-graph">
	<h4><?php echo $this->translate("Graph Statistics") ?></h4>
	<p><?php echo $this->translate('The filters below allow you to observe various metrics and their change over different time periods.')?></p>
	<div class="yn_filter">
		<div class="search">
			<?php echo $this->formChartStatistic->render($this) ?>
		</div>
	</div>

	<br />
	<div class="admin_statistics_nav">
	    <a href="" id="admin_stats_offset_previous" onclick="processStatisticsPage(-1, event);"><?php echo $this->translate("Previous") ?></a>
	    <a href="" id="admin_stats_offset_next" onclick="processStatisticsPage(1, event);" style="display: none;"><?php echo $this->translate("Next") ?></a>
	</div>

	<br />
	<div class="admin_statistics">
	  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/swfobject/swfobject.js"></script>
	  <script type="text/javascript">
	  	
	    var updateFormOptions = function() {
	      var periodEl = $('statistic_form').getElement('#period');
	      var chunkEl = $('statistic_form').getElement('#chunk');
	      switch( periodEl.get('value')) {
	        case 'ww':
	          var children = chunkEl.getChildren();
	          for( var i = 0, l = children.length; i < l; i++ ) {
	            if( ['dd'].indexOf(children[i].get('value')) == -1 ) {
	              children[i].setStyle('display', 'none');
	              if( children[i].get('selected') ) {
	                children[i].set('selected', false);
	              }
	            } else {
	              children[i].setStyle('display', '');
	            }
	          }
	          break;
	        case 'MM':
	          var children = chunkEl.getChildren();
	          for( var i = 0, l = children.length; i < l; i++ ) {
	            if( ['dd', 'ww'].indexOf(children[i].get('value')) == -1 ) {
	              children[i].setStyle('display', 'none');
	              if( children[i].get('selected') ) {
	                children[i].set('selected', false);
	              }
	            } else {
	              children[i].setStyle('display', '');
	            }
	          }
	          break;
	        case 'y':
	          var children = chunkEl.getChildren();
	          for( var i = 0, l = children.length; i < l; i++ ) {
	            if( ['dd', 'ww','MM'].indexOf(children[i].get('value')) == -1 ) {
	              children[i].setStyle('display', 'none');
	              if( children[i].get('selected') ) {
	                children[i].set('selected', false);
	              }
	            } else {
	              children[i].setStyle('display', '');
	            }
	          }
	          break;
	        default:
	          break;
	      }
	    }
	    
	    var currentArgs = {};
	    var processStatisticsFilter = function(formElement) {
	      var vals = formElement.toQueryString().parseQueryString();
	      vals.offset = 0;
	      buildStatisticsSwiff(vals);
	      return false;
	    }
	    
	    var processStatisticsPage = function(count, event) {
	      event.preventDefault();
	      var args = $merge(currentArgs);
	      args.offset += count;
	      buildStatisticsSwiff(args);
	    }
	    var buildStatisticsSwiff = function(args) {
	      currentArgs = args;
	      $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));

	      var url = new URI('<?php echo '//' . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'chart-data', 'business_id' => $business -> getIdentity() ),'ynbusinesspages_dashboard', true) ?>');
	      url.setData(args);
	      new Request.JSON({
				method: 'post',
				url: url,
				data: {
		            'id': '<?php echo $business -> getIdentity()?>',
		        },
				onSuccess: function(responseJSON) 
				{
					var tooltip = new Element('div', {
					    id: "tooltip"
					});
					var json_data = responseJSON.json;
					var d = [];
	                var ticks = [];
	                var count = 0;
	                
	                for(var i in json_data)
	                {
	                    d.push([count, json_data [i]]);
	                    ticks.push([count, i]);
	                    count = count +1;
	                }   
	                
	                var data = [];
	                switch(args.type) {
	                	case "reviews":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Reviews")?>'
			                }];
	                        break;
	                    case "members":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Members")?>'
			                }];
	                        break;    
	                    case "followers":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Followers")?>'
			                }];
	                        break;
						case "comments":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Comments")?>'
			                }];
	                        break;     
	                    case "shares":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Shares")?>'
			                }];
	                        break; 
	                    case "events":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Events")?>'
			                }];
	                        break;              
	                    case "photos":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Photos")?>'
			                }];
	                        break;    
	                    case "videos":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Videos")?>'
			                }];
			                 break;
			             case "files":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("File Sharing")?>'
			                }];
			                 break; 
			             case "mp3musics":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Mp3Music Albums")?>'
			                }];
			                 break;     
			             case "musics":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Musics")?>'
			                }];
			                 break; 
			             case "blogs":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Blogs")?>'
			                }];
			                 break;
			             case "polls":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Polls")?>'
			                }];
			                 break;                      
			             case "discussions":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Discussions")?>'
			                }];
			                 break;
			             case "wikis":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Wikis")?>'
			                }];  
	                        break;
	                     case "classified":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Classified")?>'
			                }];  
	                        break;  
	                     case "groupbuy":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Groupbuy")?>'
			                }];  
	                        break;  
	                     case "contests":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Contests")?>'
			                }];  
	                        break;  
	                     case "listings":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Listings")?>'
			                }];  
	                        break; 
	                     case "jobs":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Jobs")?>'
			                }];  
	                        break;
                         case "ynmusic_songs":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Social Music Songs")?>'
			                }];
			                 break; 
		                 case "ynmusic_albums":
	                         var data = [{
			                    data: d,
			                    label: '<?php echo $this -> translate("Social Music Albums")?>'
			                }];
			                 break;
						case "ynultimatevideo_videos":
							var data = [{
								data: d,
								label: '<?php echo $this -> translate("Ultimate Videos")?>'
							}];
							break;
	                }
	                var title_data = responseJSON.title;
	                 flot.plot(document.id('placeholder'), data, {
	                    legend: {
	                        labelFormatter: function(label, series) {
	                            return  label + " - " + title_data;
	                        }
	                    },
	                    series: {
	                        lines: {
	                            show: true
	                        },
	                        points: {
	                            show: true
	                        }
	                    },
	                    grid: {
	                        hoverable: true,
	                        clickable: true
	                    },
	                    xaxis: { 
	                        show: true,
	                        ticks: ticks
	                    }
	                });
	                tooltip.inject(document.body);
				    
				    document.id('placeholder').addEvent('plothover', function (event, pos, items) {
				        if (items) {
				            var html = '';
				            items.each(function (el) {
				                var y = el.datapoint[1].toFixed(2);
				                html += el.series.label + " of " + el.series.xaxis.ticks[el.dataIndex].label + " = " + y + "<br />";
				            });
				
				            $("tooltip").set('html', html).setStyles({
				                top: items[0].pageY,
				                left: items[0].pageX
				            });
				            $("tooltip").fade('in');
				        } else {
				            $("tooltip").fade('out');
				        }
				    });
				    
				    if(args.chunk == "dd" && args.period =="y")
					{
						$$('.xAxis .tickLabel').setStyle('display', 'none');
					}
				    document.id('placeholder').addEvent('plotclick', function (event, pos, items) {
				    });
				}
			}).send();
	    }

	    window.addEvent('load', function() {
	      updateFormOptions();
	      $('period').addEvent('change', function(event) {
	        updateFormOptions();
	      });
	      buildStatisticsSwiff({
	      	'type' : 'reviews',
	        'mode' : 'normal',
	        'chunk' : 'dd',
	        'period' : 'ww',
	        'start' : 0,
	        'offset' : 0
	      });
	      
	      
	    });
	</script>
	    <div id="placeholder" style="width:600px;height:350px;position: relative;"></div>
	  	<div id="clickInfo"></div> 
	</div>
</div>					