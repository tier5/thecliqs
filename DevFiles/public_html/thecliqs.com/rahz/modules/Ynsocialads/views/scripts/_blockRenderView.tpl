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
							$return_str = ", ".$return_str.' and '.(count($likes) -1).' other(s) liked this.';
						}
						else 
						{
							$return_str = ", ".$return_str .' liked this.';
						}
					}
					else {
						if(count($likes) > 1)
						{
							$return_str = $return_str.' and '.count($likes).' other(s) liked this.';
						}
						else 
						{
							$return_str = $return_str .' liked this.';
						}
					}
				}
				else 
				{
					if($isLike)
					{
						if(count($likes) > 1)
						{
							$return_str .= (count($likes) -1).' other(s) liked this.';
						}
						else {
							if($isLike)
							{
								$return_str .= 'liked this.';
							}
						}
					}
					else {
						if(count($likes) > 0)
						{
							$return_str .= count($likes).' other(s) liked this.';
						}
						else {
							if($isLike)
							{
								$return_str .= 'liked this.';
							}
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
<?php endif;?>