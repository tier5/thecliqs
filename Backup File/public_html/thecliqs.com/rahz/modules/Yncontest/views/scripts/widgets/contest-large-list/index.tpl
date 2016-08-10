<?php if($this->flag):?>
	<div class="tip">
	        <span>
	            <?php echo $this->translate('There are no contest.'); ?>
	           
	        </span>
	    </div>
	
<?php else:?>	
<ul class="large_contest_list">
<?php $zebra = 0; ?>
	<?php foreach($this->items as $item): ?>
		<?php if(Engine_Api::_()->user()->getUser($item->user_id)->getIdentity() != 0): ?>
		<li class = "clearfix <?php echo ($zebra % 2) == 0? 'odd' : 'even';?>">
			<div class="contest_large_img">
				<?php 
					//echo $this -> htmlLink($item -> getHref(), $this -> itemPhoto($this->member, 'thumb.normal'), array('class' => 'ynContest_LRH3ULLi_thumb'));
					echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile', $item->getTitle()));
				?>				
			</div>			
			
			<div class="yncontest_contest_info">
				<div class="ynContest_title"> <?php echo $this->htmlLink($item->getHref(),wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($item->contest_name),20), 13, "\n", true),array('title'=>$this->string()->stripTags($item->contest_name))); ?> </div>	
				<p class="ynContest_LRH3ULLi_listInfo">
					<span><?php echo $this->translate('Contest type').": ".$this->translate(Engine_Api::_()->yncontest()->arrPlugins[$item->contest_type]);?></span><br/>
					<span>
						<?php
							if($item->participants==''){
								$paticipants = 0;	
							}							
							else {
								$paticipants = $item->participants;
							}						 
							echo $this->translate("Participants").": " . $paticipants;
						?>
					</span> -
					<span><?php echo $this->translate("Entries").": " . $item->entries; ?></span> <br/>
					<span><?php
						//$dayleft = ($item->dayleft < 0)? 0 : $item->dayleft; 
						$today = date("Y-m-d H:m:s");
						if($item->start_date <= $today && $today <= $item->end_date){
							$dayleft = ($item->dayleft<0)?0:$item->dayleft;
							$dayleft = $item->dayleft;
							echo $this->translate("Days left").": ".$dayleft;
						}
						 ?></span>								
				</p>				
				<p class="ynContest_contestDesc"> <?php echo wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($item->description),50), 27, "\n", true); ?> </p>
				<!-- feature-->
				<?php if(Engine_Api::_()->yncontest()->checkFeature($item->featured_id,$item->premium_id) ==1):?>
				  <div class="small_feature_icon small_contest_icon">
					<span><?php echo $this->translate('Feature')?></span>
				  </div>
				<?php elseif(Engine_Api::_()->yncontest()->checkFeature($item->featured_id,$item->premium_id) ==2):?>
				  <div class="small_premium_icon small_contest_icon">		          		
					<span><?php echo $this->translate('Premium')?></span>
				  </div>
				<?php endif;?>
				<!-- end feature-->
							
			</div>
			<?php $zebra++;?>
			<div style="clear: both"></div>	
		</li>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php if(count($this->items) > 6):?>	
	<div class = "yncontest_no_border">
		<?php echo $this->htmlLink(array(
			'route' => 'yncontest_general', 
			'action' => 'listing', 
			'typed' => $this->typed,		  		
			),
			"<span>&rsaquo;</span>".$this->translate('View more'), 
			array('class' => 'ynContest_viewAll'));
		?>
	</div>
	<?php endif; ?>
</ul>
<?php endif;?>
