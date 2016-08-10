<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/wookmark/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/wookmark/jquery.wookmark.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/wookmark/jquery.imagesloaded.js"></script>

<div id="ynlistings_list_item" class="<?php echo $this -> class_mode;?>">  
	<div id="yn_listings_tabs" class="tabs_alt tabs_parent">
		<!--  Tab bar -->
		<ul id="yn_listings_tab_list" class = "main_tabs">
			<?php if(in_array('recent', $this -> tab_enabled)):?>
				<!-- Recent -->
				<li>
					<a href="javascript:;" rel="tab_listings_recent" class="selected">
						<?php echo $this->translate('Recent Listings');?>
					</a>
				</li>
			<?php endif;?>
			<?php if(in_array('popular', $this -> tab_enabled)):?>
				<!-- Most view -->
				<li>
					<a href="javascript:;" rel="tab_listings_popular">
						<?php echo $this->translate('Most Viewed Listings');?>
					</a>
				</li>
			<?php endif;?>    
		</ul>

		<div class="ynlistings-action-view-method">
			<?php if(in_array('list', $this -> mode_enabled)):?>
				<div class="ynlistings_home_page_list_content" rel="map_view">
					<div class="ynlistings_home_page_list_content_tooltip"><?php echo $this->translate('List View')?></div>
					<span id="list_view_<?php echo $this->identity;?>" class="ynlistings_home_page_list_content_icon tab_icon_list_view" title="<?php echo $this->translate('List View')?>" onclick="ynlistings_view_list();"></span>
				</div>
			<?php endif;?>

			<?php if(in_array('grid', $this -> mode_enabled)):?>
				<div class="ynlistings_home_page_list_content" rel="map_view">
					<div class="ynlistings_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
					<span id="grid_view_<?php echo $this->identity;?>" class="ynlistings_home_page_list_content_icon tab_icon_grid_view" title="<?php echo $this->translate('Grid View')?>" onclick="ynlistings_view_grid();"></span>
				</div>
			<?php endif;?>

			<?php if(in_array('pin', $this -> mode_enabled)):?>
				<div class="ynlistings_home_page_list_content" rel="pin_view">
					<div class="ynlistings_home_page_list_content_tooltip"><?php echo $this->translate('Pin View')?></div>
					<span id="pin_view_<?php echo $this->identity;?>" class="ynlistings_home_page_list_content_icon tab_icon_pin_view" title="<?php echo $this->translate('Pin View')?>" onclick="ynlistings_view_pin();"></span>
				</div>
			<?php endif;?>
			
			<?php if(in_array('map', $this -> mode_enabled)):?>
				<div class="ynlistings_home_page_list_content" rel="map_view">
					<div class="ynlistings_home_page_list_content_tooltip"><?php echo $this->translate('Map View')?></div>
					<span id="map_view_<?php echo $this->identity;?>" class="ynlistings_home_page_list_content_icon tab_icon_map_view" title="<?php echo $this->translate('Map View')?>" onclick="ynlistings_view_map();"></span>
				</div>
			<?php endif;?>
		</div>
	</div>
	<div id="ynlistings_list_item_content" class="ynlistings-tabs-content ynclearfix">
		<?php if(in_array('recent', $this -> tab_enabled)):?>
			<!-- Recent Listings Tab Content-->
			<div id="tab_listings_recent" class="tabcontent">
				<?php
				echo $this->partial('_list_most_item.tpl', 'ynlistings', array('listings' => $this->recentListings, 'tab' => 'listings_recent'));
				?>
			</div>
		<?php endif;?>
		<?php if(in_array('popular', $this -> tab_enabled)):?>
			<!-- Popular listings Tab Content-->
			<div id="tab_listings_popular" class="tabcontent">
				<?php
				echo $this->partial('_list_most_item.tpl', 'ynlistings', array('listings' => $this->popularListings, 'tab' => 'listings_popular'));
				?>
			</div>
		<?php endif;?>
		<iframe id='list-most-items-iframe' style="max-height: 500px;"> </iframe>
	</div>
	
	<script type="text/javascript">
		$$('#yn_listings_tab_list li')[0].addClass('active');
		var yn_listings_tabs =new ddtabcontent("yn_listings_tabs");
		yn_listings_tabs.setpersist(false);
		yn_listings_tabs.setselectedClassTarget("link");
		yn_listings_tabs.init(900000);
		var ynlistings_view_map = function() {
			document.getElementById('ynlistings_list_item').set('class','ynlistings_map-view');
			var tab = $$('.layout_ynlistings_list_most_items #yn_listings_tab_list li .selected')[0].get('rel');
			var html =  '<?php echo $this->url(array('action'=>'display-map-view'), 'ynlistings_general') ?>'+'/tab/'+tab+'/itemCount/'+<?php echo $this->itemCount?>;
			document.getElementById('list-most-items-iframe').dispose();
			var iframe = new IFrame({
				id : 'list-most-items-iframe',
				src: html,
				styles: {
					'width': '100%',
					'height': 500,
				},
			});
			iframe.inject($$('#ynlistings_list_item_content')[0]);
			document.getElementById('list-most-items-iframe').style.display = 'block';
			setCookie('view_mode', 'map');
		}  

		var ynlistings_view_pin =  function()
		{
			document.getElementById('ynlistings_list_item').set('class','ynlistings_pin-view');
			setCookie('view_mode','pin');

			var tab_content = $$('#yn_listings_tab_list .selected')[0].get('rel');

			jQuery.noConflict();
			(function (jQuery){
				var handler = jQuery('#'+tab_content+' .listing_pin_view_content li');

				handler.wookmark({
				  // Prepare layout options.
				  autoResize: true, // This will auto-update the layout when the browser window is resized.
				  container: jQuery('#'+tab_content+' .listing_pin_view_content'), // Optional, used for some extra CSS styling
				  offset: 20, // Optional, the distance between grid items
				  outerOffset: 0, // Optional, the distance to the containers border
				  itemWidth: 220, // Optional, the width of a grid item
				  flexibleWidth: '50%',
				});

			})(jQuery);
		} 

		var ynlistings_view_grid =  function()
		{
			document.getElementById('ynlistings_list_item').set('class','ynlistings_grid-view');
			setCookie('view_mode','grid');
		}  

		var ynlistings_view_list = function()
		{
			document.getElementById('ynlistings_list_item').set('class','ynlistings_list-view');
			setCookie('view_mode','list');
		}  

		<?php if($this -> view_mode == 'map'):?>
		ynlistings_view_map();
	<?php endif;?>       
</script>
</div>
<script type="text/javascript">
	window.addEvent('domready', function(){

		if(getCookie('view_mode')!= "")
		{
			document.getElementById('ynlistings_list_item').set('class',"ynlistings_"+getCookie('view_mode')+"-view");
			var map = getCookie('view_mode');                       
			if(map == 'map')
			{
				ynlistings_view_map();
			}
		}
		else
		{
			document.getElementById('ynlistings_list_item').set('class',"<?php echo $this -> class_mode;?>");
			
		}

		if ( document.getElementById('ynlistings_list_item').hasClass('ynlistings_pin-view') ) {
			jQuery.noConflict();
			(function (jQuery){
				var handler = jQuery('#ynlistings_list_item .listing_pin_view_content li');

				handler.wookmark({
				  // Prepare layout options.
				  autoResize: true, // This will auto-update the layout when the browser window is resized.
				  container: jQuery('#ynlistings_list_item .listing_pin_view_content'), // Optional, used for some extra CSS styling
				  offset: 20, // Optional, the distance between grid items
				  outerOffset: 0, // Optional, the distance to the containers border
				  itemWidth: 220, // Optional, the width of a grid item
				  flexibleWidth: '50%',
				});

			})(jQuery);
		}

		$$('#ynlistings_list_item #yn_listings_tab_list > li > a').each(function(el, idx){
			el.addEvent('click', function(e){
				var tab_content = $$('#yn_listings_tab_list .selected')[0].get('rel');

				jQuery.noConflict();
				(function (jQuery){
					var handler = jQuery('#'+tab_content+' .listing_pin_view_content li');

					handler.wookmark({
					  // Prepare layout options.
					  autoResize: true, // This will auto-update the layout when the browser window is resized.
					  container: jQuery('#'+tab_content+' .listing_pin_view_content'), // Optional, used for some extra CSS styling
					  offset: 20, // Optional, the distance between grid items
					  outerOffset: 0, // Optional, the distance to the containers border
					  itemWidth: 220, // Optional, the width of a grid item
					  flexibleWidth: '50%',
					});
					
				})(jQuery);

				if(this.getProperty('rel') == 'tab_groups_directories')
					$$('.ynlistings-action-view-method').hide();
				else
				{
					$$('.ynlistings-action-view-method').show();
					if(getCookie('view_mode') != "")
					{
						var map = getCookie('view_mode');                           
						if(map == 'map')
						{
							ynlistings_view_map();
						}
						document.getElementById('ynlistings_list_item').set('class',"ynlistings_"+getCookie('view_mode')+"-view");
					}
					else
					{                           
						document.getElementById('ynlistings_list_item').set('class',"<?php echo $this -> class_mode;?>");
					}

				}           
			});
		});
		
	});

function setCookie(cname,cvalue,exdays)
{
	var d = new Date();
	d.setTime(d.getTime()+(exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname)
{
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) 
	{
		var c = ca[i].trim();
		if (c.indexOf(name)==0) return c.substring(name.length,c.length);
	}
	return "";
}
</script>