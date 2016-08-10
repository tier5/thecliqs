window.addEvent('domready', function() 
{	
	var ads_html = new Element('li', {'html':
	<?php echo Zend_Json::encode($this->partial('_feedRenderView.tpl', 'ynsocialads', 
		array(
			'ads_arr' => $this -> ads_arr, 
		)))
	?>
	});

	ads_html.set('class', 'feed_ynsocial_ads');

	<?php if($this->pos > 0 ) :?>
		ads_html.inject( $$('#activity-feed > li:nth-child(<?php echo $this->pos ?>)')[0], 'after');
	<?php else :?>
		ads_html.inject( $$('#activity-feed > li:nth-child(1)')[0], 'before');
	<?php endif;?>
	
});

window.addEvent('load', function() {
      $$('.hide_owner_feed').addEvent('click', function(event) {
      	var ad_id = this.getProperty('ad_id');
      	 var obj = this.getParent().getParent().getParent();
      	var owner_id = this.getProperty('owner_id');
       var url = '<?php echo $this->url(array('action'=>'hidden','type'=>'owner'), 'ynsocialads_ads')?>';
	     url = url + '/id/' + ad_id;
	      new Request.JSON({
				method: 'post',
				url: url,
				data: {
				},
				onSuccess: function(responseJSON) 
				{
					obj.innerHTML = '<div class="tip" style="clear: inherit;">'
					      + '<span>'
					      + '<?php echo $this -> translate("We will try not to show you this ad again.")?>'
					      + '</span>'
					      + '<div style="clear: both;"></div>'
					    +'</div>';		
				}
		  }).send();
      });
      
       $$('.hide_ad_feed').addEvent('click', function(event) {
       var ad_id = this.getProperty('ad_id');
       var obj = this.getParent().getParent().getParent();
       var url = '<?php echo $this->url(array('action'=>'hidden','type'=>'ad'), 'ynsocialads_ads')?>';
       url = url + '/id/' + ad_id;
	      new Request.JSON({
				method: 'post',
				url: url,
				data: {
				},
				onSuccess: function(responseJSON) 
				{
					obj.innerHTML = '<div class="tip" style="clear: inherit;">'
					      + '<span>'
					      + '<?php echo $this -> translate("We will try not to show you this ad again.")?>'
					      + '</span>'
					      + '<div style="clear: both;"></div>'
					    +'</div>';	
				}
		  }).send();
      });

       $$('.ynsocial_ads_feed_setting').addEvent('click',function(){
		var this_id = this.get('data-id');
		document.id(this_id).toggle();
		});
	});
	
	var preventClick = function(obj,event){
		var ad_id = obj.getProperty('ad_id');
		var prevent_click = '.prevent_click_'+ad_id;
		$$(prevent_click).addClass('click_disabled');
	}
