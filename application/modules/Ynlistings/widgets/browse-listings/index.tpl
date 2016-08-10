<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/wookmark/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/wookmark/jquery.wookmark.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/wookmark/jquery.imagesloaded.js"></script>

<div id="ynlistings_list_item_browse" class="<?php echo $this -> class_mode;?>">  
	<div id="yn_listings_tabs_browse" class="tabs_alt tabs_parent">
		<!--  Tab bar -->
		<ul id="yn_listings_tab_list_browse" class = "main_tabs">
			<li class="active">
				<a href="javascript:;" rel="tab_browse_listings" class="selected">
					<?php if ($this->category) : ?>
						<?php echo $this->category->title?>
					<?php else: ?>
						<?php echo $this->translate('Browse Listings');?>
					<?php endif; ?>
				</a>
			</li>
		</ul>
		<div class="ynlistings-action-view-method">
			<?php if(in_array('map', $this -> mode_enabled)):?>
				<div class="ynlistings_home_page_list_content" rel="map_view">
					<div class="ynlistings_home_page_list_content_tooltip"><?php echo $this->translate('Map View')?></div>
					<span id="map_view_<?php echo $this->identity;?>" class="ynlistings_home_page_list_content_icon tab_icon_map_view" title="<?php echo $this->translate('Map View')?>" onclick="ynlistings_view_map_browse();"></span>
				</div>
			<?php endif;?>
			<?php if(in_array('pin', $this -> mode_enabled)):?>
				<div class="ynlistings_home_page_list_content" rel="pin_view">
					<div class="ynlistings_home_page_list_content_tooltip"><?php echo $this->translate('Pin View')?></div>
					<span id="pin_view_<?php echo $this->identity;?>" class="ynlistings_home_page_list_content_icon tab_icon_pin_view" title="<?php echo $this->translate('Pin View')?>" onclick="ynlistings_view_pin_browse();"></span>
				</div>
			<?php endif;?>
			<?php if(in_array('grid', $this -> mode_enabled)):?>
				<div class="ynlistings_home_page_list_content" rel="map_view">
					<div class="ynlistings_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
					<span id="grid_view_<?php echo $this->identity;?>" class="ynlistings_home_page_list_content_icon tab_icon_grid_view" title="<?php echo $this->translate('Grid View')?>" onclick="ynlistings_view_grid_browse();"></span>
				</div>
			<?php endif;?>
			<?php if(in_array('list', $this -> mode_enabled)):?>
				<div class="ynlistings_home_page_list_content" rel="map_view">
					<div class="ynlistings_home_page_list_content_tooltip"><?php echo $this->translate('List View')?></div>
					<span id="list_view_<?php echo $this->identity;?>" class="ynlistings_home_page_list_content_icon tab_icon_list_view" title="<?php echo $this->translate('List View')?>" onclick="ynlistings_view_grid_list();"></span>
				</div>
			<?php endif;?>
		</div>
	</div>
	<div id="ynlistings_list_item_browse_content" class="ynlistings-tabs-content ynclearfix">
		<div id="tab_listings_browse_listings">
			<?php
			echo $this->partial('_list_most_item.tpl', 'ynlistings', array('listings' => $this->paginator, 'tab' => 'listings_browse_listing'));
			?>
		</div>
		<iframe id='browse-iframe' style="max-height: 500px;"> </iframe>
	</div>
	
	<script type="text/javascript">
		var ynlistings_view_map_browse = function() {
			document.getElementById('ynlistings_list_item_browse').set('class','ynlistings_map-view');
			var tab = $$('.layout_ynlistings_browse_listings #yn_listings_tab_list_browse li .selected')[0].get('rel');
			var html =  '<?php echo $this->url(array('action'=>'display-map-view'), 'ynlistings_general') ?>'+'/tab/'+tab+'/'+'<?php echo $this->params_str?>';
			document.getElementById('browse-iframe').dispose();

			var iframe = new IFrame({
				id : 'browse-iframe',
				src: html,
				styles: {
					'width': '100%',
					'height': 500,
				},
			});

			iframe.inject($$('#ynlistings_list_item_browse_content')[0]);
			document.getElementById('browse-iframe').style.display = 'block';
			setCookie('browse_view_mode', 'map');
			$$('.pages').hide();
		}  

		var ynlistings_view_pin_browse =  function()
		{
			document.getElementById('ynlistings_list_item_browse').set('class','ynlistings_pin-view');
			setCookie('browse_view_mode','pin');
			$$('.pages').show();

			jQuery.noConflict();
			(function (jQuery){
				var handler = jQuery('#ynlistings_list_item_browse .listing_pin_view_content li');

				handler.wookmark({
				  // Prepare layout options.
				  autoResize: true, // This will auto-update the layout when the browser window is resized.
				  container: jQuery('#ynlistings_list_item_browse .listing_pin_view_content'), // Optional, used for some extra CSS styling
				  offset: 20, // Optional, the distance between grid items
				  outerOffset: 0, // Optional, the distance to the containers border
				  itemWidth: 220, // Optional, the width of a grid item
				  flexibleWidth: '50%',
				});

			})(jQuery);
		} 

		var ynlistings_view_grid_browse =  function()
		{
			document.getElementById('ynlistings_list_item_browse').set('class','ynlistings_grid-view');
			setCookie('browse_view_mode','grid');
			$$('.pages').show();
		}  

		var ynlistings_view_grid_list = function()
		{
			document.getElementById('ynlistings_list_item_browse').set('class','ynlistings_list-view');
			setCookie('browse_view_mode','list');
			$$('.pages').show();
		}   

		<?php if($this -> view_mode == 'map'):?>
		ynlistings_view_map_browse();
	<?php endif;?>       
</script>
</div>
<?php if( count($this->paginator) > 1 ): ?>
	<?php echo $this->paginationControl($this->paginator, null, array(
		'paginator.tpl',
		'ynlistings',
		), array(
		'pageAsQuery' => true,
		'query' => $this->formValues,
		)); ?>
	<?php endif; ?>

	<script type="text/javascript">
		window.addEvent('domready', function(){
			
			if(getCookie('browse_view_mode')!= "")
			{
				document.getElementById('ynlistings_list_item_browse').set('class',"ynlistings_"+getCookie('browse_view_mode')+"-view");
				var map = getCookie('browse_view_mode');                       
				if(map == 'map')
				{
					ynlistings_view_map_browse();
				}
			}
			else
			{
				document.getElementById('ynlistings_list_item_browse').set('class',"<?php echo $this -> class_mode;?>");
			}

			if ( document.getElementById('ynlistings_list_item_browse').hasClass('ynlistings_pin-view') ) {
				jQuery.noConflict();
				(function (jQuery){
					var handler = jQuery('#ynlistings_list_item_browse .listing_pin_view_content li');

					handler.wookmark({
					  // Prepare layout options.
					  autoResize: true, // This will auto-update the layout when the browser window is resized.
					  container: jQuery('#ynlistings_list_item_browse .listing_pin_view_content'), // Optional, used for some extra CSS styling
					  offset: 20, // Optional, the distance between grid items
					  outerOffset: 0, // Optional, the distance to the containers border
					  itemWidth: 220, // Optional, the width of a grid item
					  flexibleWidth: '50%',
					});
					
				})(jQuery);

			}
		
		$$('#ynlistings_list_item_browse #yn_listings_tab_list_browse > li > a').each(function(el, idx){
			el.addEvent('click', function(e){

				$$('.ynlistings-action-view-method').show();
				if(getCookie('browse_view_mode') != "")
				{
					var map = getCookie('browse_view_mode');                           
					if(map == 'map')
					{
						ynlistings_view_map_browse();
					}
					document.getElementById('ynlistings_list_item_browse').set('class',"ynlistings_"+getCookie('browse_view_mode')+"-view");
				}
				else
				{                           
					document.getElementById('ynlistings_list_item_browse').set('class',"<?php echo $this -> class_mode;?>");
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