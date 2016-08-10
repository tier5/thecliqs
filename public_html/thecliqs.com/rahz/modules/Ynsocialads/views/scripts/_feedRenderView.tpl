<?php foreach($this->ads_arr as $ad) :?>
<li class="ynsocial_ads_item">
	<div class="feed_item_photo">
		<?php echo $this->htmlLink($ad->getOwner()->getHref(), $this->itemPhoto($ad->getOwner(), 'thumb.icon')) ?>
	</div>
	<div class="feed_item_body">
		
		<span class="ynsocial_ads_feed_setting" id="ynsocial_ads_feed" data-id="ynsocial_ads_setting_feed_ad<?php echo $ad->ad_id; ?>">
			</span>

		<div class="ynsocial_ads_setting_choose" id="ynsocial_ads_setting_feed_ad<?php echo $ad->ad_id; ?>">		
			<a ad_id= '<?php echo $ad->getIdentity(); ?>' class='hide_ad_feed'  onclick='return false;' href='#'>
				<?php echo  $this->translate('Hide this ad'); ?>
			</a> 
			<a ad_id= '<?php echo $ad->getIdentity(); ?>'class='hide_owner_feed' onclick='return false;' href='#'>
				<?php echo  $this->translate('Hide all ads from this advertiser'); ?>
			</a> 
		</div>

		<a href="<?php echo $ad->getOwner()->getHref();?>" title="<?php echo $this->translate($ad->getOwner()->getTitle());?>" style="font-weight: bold;"><?php echo $this->translate($ad->getOwner()->getTitle());?></a>

		<a ad_id='<?php echo $ad->getIdentity(); ?>' onclick="preventClick(this,event);" class="prevent_click_<?php echo $ad->getIdentity(); ?> ynsocial_ads_cont_title" href="<?php echo $ad->getLinkUpdateStats()?>">
			<?php echo $this->translate($ad->name);?>
		</a>
		
		<div class="ynsocial_ads_cont"><?php echo $this->translate($ad->description)?></div>
		
		<div class="ynsocial_ads_cont_image">
			<a ad_id='<?php echo $ad->getIdentity(); ?>' onclick="preventClick(this,event);" class='prevent_click_<?php echo $ad->getIdentity(); ?>' href="<?php echo $ad->getLinkUpdateStats()?>">
				<img src="<?php echo $ad -> getPhotoUrl('thumb.normal') ?>"/>
			</a>	
		</div>

		<?php if ($this->viewer() -> getIdentity()):?>
		<div class="feed_item_date">
			<ul>          		
				<li>          		
					<?php echo $this->htmlLink(array(
						'module' => 'activity',
						'controller' => 'index',
						'action' => 'share',
						'route' => 'default',
						'type' => $ad->getType(),
						'id' => $ad->getIdentity(),
						'format' => 'smoothbox'
						), $this->translate("Share"), array('class' => 'smoothbox')); ?>
				</li>
				<li>
					<span>-</span>
					<?php echo $this->htmlLink(array(
						'module' => 'core',
						'controller' => 'report',
						'action' => 'create',
						'route' => 'default',
						'subject' => $ad->getGuid(),
						'format' => 'smoothbox'
					  ), $this->translate("Report"), array('class' => 'smoothbox')); ?>
				</li>                     
			</ul>
		</div>
		<?php endif;?>	
	</div>	
</li>
<?php endforeach;?>