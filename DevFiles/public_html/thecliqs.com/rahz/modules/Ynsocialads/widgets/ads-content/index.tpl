<?php if ($this->is_ajax): ?>
<?php $strRand = rand(1,100).rand(1,100);?>
<div id="ynsocialads_ajax_<?php echo $strRand?>"></div>
<script type="text/javascript">
 window.addEvent('domready', function()
 {
    var l = document.getElementById('ynsocialads_ajax_<?php echo $strRand?>');
    l.innerHTML = '<img src="./application/modules/Ynsocialads/externals/images/loading.gif"/>';
    var content_id = <?php echo $this->content_id?>;
     var makeRequest = new Request(
            {
                url: "ynsocialads/ads/ajax-render-ads/content_id/"+ content_id+"/ajax/"+<?php echo $strRand;?>,
                onComplete: function (respone){
                 l.innerHTML = respone;
                }
            }
    )
    makeRequest.send();
 });
</script>

<?php else :?>
<?php if(count($this->ads) > 0) :?>
<div class="ynsocial_ads" >
	<div class="ynsocial_ads_title">
		<?php if ($this->viewer()->getIdentity()): ?>
			<a href="<?php echo $this->url(array('action'=>'create-choose-package'), 'ynsocialads_ads')?>"><?php echo $this->translate('Create Ads'); ?></a>
		<?php endif;?>
		<h5><?php echo $this->translate('Ads'); ?></h5>
	</div>
	<div class="ynsocial_ads_content">
		<?php foreach($this->ads as $ad) :?>
		<div class="ynsocial_ads_item">
			<span onclick="javascript:clickSetting(this);" class="ynsocial_ads_setting" id="ynsocial_ads_<?php echo $this->content_id.'_'.$ad->ad_id ?>" data-id="ynsocial_ads_setting_<?php echo $this->content_id.'_'.$ad->ad_id; ?>">
			</span>

			<div class="ynsocial_ads_setting_choose" id="ynsocial_ads_setting_<?php echo $this->content_id.'_'.$ad->ad_id; ?>">	
				<a onclick="javascript:hideAd(this); return false;" ad_id= '<?php echo $ad->getIdentity(); ?>'  href='#'>
					<?php echo  $this->translate('Hide this ad'); ?>
				</a> 
				<a onclick="javascript:hideOwner(this); return false;" ad_id= '<?php echo $ad->getIdentity(); ?>'  href='#'>
					<?php echo  $this->translate('Hide all ads from this advertiser'); ?>
				</a> 
			</div>

			<a ad_id='<?php echo $ad->getIdentity(); ?>' onclick="preventClick(this,event);" class="prevent_click_<?php echo $ad->getIdentity(); ?> ynsocial_ads_cont_title" href="<?php echo $ad->getLinkUpdateStats()?>">
				<?php echo $this->translate($ad->name);?>
			</a>
			<a ad_id='<?php echo $ad->getIdentity(); ?>' onclick="preventClick(this,event);" class="prevent_click_<?php echo $ad->getIdentity(); ?> ynsocial_ads_cont_image" href="<?php echo $ad->getLinkUpdateStats()?>">
				<img src="<?php echo $ad -> getPhotoUrl('thumb.normal') ?>"/>
			</a>
			<div class="ynsocial_ads_cont"><?php echo $this->translate($ad->description)?></div>
			
			<?php if ($this->viewer()->getIdentity()): ?>
					<?php if ($ad->likes()->isLike($this->viewer())) : ?>
						<span class="icon_ynsocial_ads_like"></span>		
						<a ad_id= '<?php echo $ad->getIdentity(); ?>' title="<?php echo $this->translate("Unlike")?>"
						id="ynsocialads_unlike" href="javascript:void(0);"
						onClick="ynsocialads_like(this);"
						class= 'ynsocialads_unlike'> 
						     <?php echo $this->translate("Unlike")?>
						</a>	
					<?php else : ?>
						<span class="icon_ynsocial_ads_like"></span>
						<a ad_id= '<?php echo $ad->getIdentity(); ?>' title="<?php echo $this->translate("Like") ?>" id="ynsocialads_like"
								href="javascript:void(0);" onClick="ynsocialads_like(this);"
								class= 'ynsocialads_like'> 
						    <?php echo $this->translate("Like")?>
						</a>
	            <?php endif;?>
			<?php endif; ?>
			
			<?php
				$isLike = 0; if ($ad->likes()->isLike($this->viewer())) $isLike = 1;
				$aUserLike = $ad->getUserLike();
				$likes = $ad->likes()->getAllLikesUsers();
			?>
			<div id='count_like_<?php echo $ad->getIdentity(); ?>' <?php if((count($likes) < 1) && !$isLike && (count($aUserLike) < 1)) echo "class=''"; else echo "class='ynsocial_ads_like_cont'"; ?>>
			
				<div id='display_name_like_<?php echo $ad->getIdentity(); ?>' style="display: <?php if($isLike) echo 'inline'; else echo 'none';?>">
					<a href="<?php echo $this->viewer()->getHref();?>"><?php echo $this->translate('You'); ?></a>
				</div>	
				<?php
					//handle like function
					$return_str = "";
					if(count($aUserLike) > 0){
						$iUserId = $aUserLike[0]['iUserId'];
						$user = Engine_Api::_() -> getItem('user', $iUserId);
						$sDisplayName = $aUserLike[0]['sDisplayName'];
						$return_str = "<a href='".$user->getHref()."'>".$sDisplayName."</a>";
						if($isLike)
						{
							if(count($likes) > 2)
							{
								$return_str = ", " . $return_str . $this -> translate(array(" and %s other liked this.", " and %s others liked this." ,count($likes) -1), count($likes) -1);	
							}
							else 
							{
								$return_str = ", ". $return_str . $this -> translate(' liked this.');
							}
						}
						else {
							if(count($likes) > 1)
							{
								$return_str = $return_str. $this -> translate(array(" and %s other liked this."," and %s others liked this.", count($likes)), count($likes));
							}
							else 
							{
								$return_str = $return_str . $this -> translate(' liked this.');
							}
						}
					}
					else 
					{
						if($isLike)
						{
							if(count($likes) > 1)
							{
								$return_str .= $this -> translate(array("and %s other liked this.", "and %s others liked this.", count($likes) -1), count($likes) -1); 
							}
							else 
							{
								$return_str .= $this -> translate(' liked this.');
							}
						}
						else {
							if(count($likes) > 0)
							{
								$return_str .= count($likes). $this -> translate(' people liked this.');
							}
						}
					}
					//end function
				?>
				<div style='display: inline' id='ajax_call_<?php echo $ad->getIdentity(); ?>'><?php echo $return_str;?></div>
			</div>
		</div>	
		<?php endforeach;?>
	</div>
</div>
<?php endif; ?>	
<?php endif; ?>	

<script type="text/javascript">
	var clickSetting = function(obj){
			var this_id = obj.get('data-id');
			document.id(this_id).toggle();
	}
  
     var hideOwner = function(obj)
     { 	
      	var ad_id = obj.getProperty('ad_id');
      	var obj_hide = obj.getParent().getParent();
      	var owner_id = obj.getProperty('owner_id');
        var url = '<?php echo $this->url(array('action'=>'hidden','type'=>'owner'), 'ynsocialads_ads', true)?>';
	     url = url + '/id/' + ad_id;
	      new Request.JSON({
				method: 'post',
				url: url,
				data: {
				},
				onSuccess: function(responseJSON) 
				{
					obj_hide.innerHTML = '<div class="tip" style="clear: inherit;">'
					      + '<span>'
					      + '<?php echo $this -> translate("We will try not to show you this ad again.")?>'
					      + '</span>'
					      + '<div style="clear: both;"></div>'
					    +'</div>';					
				}
		  }).send();
      }
      
      var hideAd = function(obj)
      {
      	
       var ad_id = obj.getProperty('ad_id');
       var obj_hide = obj.getParent().getParent();
       var url = '<?php echo $this->url(array('action'=>'hidden','type'=>'ad'), 'ynsocialads_ads', true)?>';
       url = url + '/id/' + ad_id;
	      new Request.JSON({
				method: 'post',
				url: url,
				data: {
				},
				onSuccess: function(responseJSON) 
				{
					obj_hide.innerHTML = '<div class="tip" style="clear: inherit;">'
					      + '<span>'
					      + '<?php echo $this -> translate("We will try not to show you this ad again.")?>'
					      + '</span>'
					      + '<div style="clear: both;"></div>'
					    +'</div>';		
				}
		  }).send();
      }

    
function ynsocialads_like(ele)     
{   
	var ad_id = ele.getProperty('ad_id');
	ele.setStyle('display', 'none');
    if (ele.className=="ynsocialads_like") {
        var request_url = '<?php echo $this->url(array('module' => 'ynsocialads', 'controller' => 'like', 'action' => 'like'), 'default', true); ?>';
    } else {
        var request_url = '<?php echo $this->url(array('module' => 'ynsocialads', 'controller' => 'like', 'action' => 'unlike'), 'default', true); ?>';
    }
    request_url = request_url + '/subject/ynsocialads_ad_'+ad_id;
    new Request.JSON({
        url:request_url ,
        method: 'post',
        data : {
            format: 'json',
            'type':'ynsocialads_ad',
            'id': ad_id
        },
        onComplete: function(responseJSON, responseText) {
        	ele.setStyle('display', 'inline');
        	if (responseJSON.error) {
                en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
            } else {
                if (ele.className=="ynsocialads_like") {
                    ele.setAttribute("class", "ynsocialads_unlike")|| ele.setAttribute("className", "ynsocialads_unlike");
                    ele.title= '<?php echo $this->translate("Unlike") ?>';
                    ele.innerHTML = '<?php echo $this->translate("Unlike") ?></i>';
                    var class_name = 'display_name_like_' + ad_id;
                    var ajax_class = 'ajax_call_' +  ad_id;
                    var count_class = 'count_like_' + ad_id;
                    $(class_name).setStyle('display', 'inline');
                    $(ajax_class).set('html', responseJSON['list']);
                    $(count_class).addClass('ynsocial_ads_like_cont');
                } else {    
                    ele.setAttribute("class", "ynsocialads_like")|| ele.setAttribute("className", "ynsocialads_like"); 
                    ele.title= '<?php echo $this->translate("Like") ?>';                        
                    ele.innerHTML = '<?php echo $this->translate("Like") ?>';
                    var class_name = 'display_name_like_' + ad_id;
                    var ajax_class = 'ajax_call_' +  ad_id;
                    if(responseJSON['count'] < 1)
                    {
                    	var count_class = 'count_like_' + ad_id;
                    	 $(count_class).removeClass('ynsocial_ads_like_cont');
                    }
                    $(class_name).setStyle('display', 'none');
                    $(ajax_class).set('html', responseJSON['list']);
                }                   
            }
        }
    }).send(); 
}

var preventClick = function(obj,event){
		var ad_id = obj.getProperty('ad_id');
		var prevent_click = '.prevent_click_'+ad_id;
		$$(prevent_click).addClass('click_disabled');
}
</script>

