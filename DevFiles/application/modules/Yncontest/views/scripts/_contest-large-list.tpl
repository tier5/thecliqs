<?php if($this->flag):?>
	<div class="tip">
	        <span>
	            <?php echo $this->translate('There is no contest meets your criteria.'); ?>
	           
	        </span>
	    </div>
	
<?php else:?>	
<ul class="large_contest_list">
<?php $zebra = 0;?>
	<?php foreach($this->items as $item): ?>
		<?php if(Engine_Api::_()->user()->getUser($item->user_id)->getIdentity() != 0): ?>
		<li class="clearfix <?php echo ($zebra % 2) == 0? 'odd' : 'even';?>" style="width:<?php echo $this->width?>px; height: <?php echo $this->height?>px;">
			<div class="contest_large_img" style="background-image:url(<?php echo $item->getPhotoUrl('thumb.profile');?>);">
				<div class="corner_icon <?php echo Engine_Api::_()->yncontest()->arrPlugins[$item->contest_type]?>"></div>
				<div class="wrap_link">
				<?php if($item->contest_status == 'close'):?>
					<div class="link"><span class="close"><?php echo $this->translate('CLOSED');?></span></div>					
				<?php else:?>
					<?php if($item->featured_id):?>
						<div class="link"><span class="feature"><?php echo $this->translate('FEATURE');?></span></div>		
					<?php endif; ?>
					<?php if($item->premium_id):?>
					<div class="link"><span class="premium"><?php echo $this->translate('PREMIUM');?></span></div>
					<?php endif; ?>
					<?php if($item->endingsoon_id):?>
					<div class="link"><span class="ending_soon"><?php echo $this->translate('ENDING SOON');?></span></div>
					<?php endif; ?>
				<?php endif; ?>
				</div>
				<div class="desc_contest">
					<p><?php echo $this->htmlLink($item->getHref(),wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($item->contest_name),20), 13, "\n", true),array('title'=>$this->string()->stripTags($item->contest_name))); ?></p>
				</div>
				<div class="wrap_desc">
					<p><?php echo $this->htmlLink($item->getHref(),wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($item->contest_name),20), 13, "\n", true),array('title'=>$this->string()->stripTags($item->contest_name))); ?></p>
					<p><span>
					<?php 
					   if($item->start_date > date('Y-m-d H:i:s'))
						{
							echo $this->locale()->toDateTime( $item->start_date, array('size' => 'short'));
						}
						elseif($item->end_date < date('Y-m-d H:i:s'))
						{	
							echo $this->translate("End");	
						}	
						else
						{
						   	if($item -> yearleft >= 1)
						   		echo $this->translate(array('%s year left','%s years left',$item -> yearleft),$item -> yearleft);
							elseif($item -> monthleft >= 1)
								echo $this->translate(array('%s month left','%s months left',$item -> monthleft),$item -> monthleft);
								
							elseif($item -> dayleft >= 1)
								echo $this->translate(array('%s day left','%s days left',$item -> dayleft),$item -> dayleft);							
							else {							
								echo  $this->translate(array('%s hour %s minute left','%s hours %s minutes left', $item -> hourleft, $item -> minuteleft), $item -> hourleft, $item -> minuteleft);														
							}
						}	
					?></span>
					</p>
					<p><?php echo $this->translate('Created by')?> <a href="#"><?php echo $item->getOwner()?></a></p>
					<?php if($this->follow):?>
						<p>
							<?php 					
								echo $this->htmlLink(
									  array('route' => 'yncontest_mycontest', 'action' => 'un-follow', 'contestId' => $item->contest_id),
									  $this->translate('Unfollow'),
									  array('class' => 'smoothbox buttonlink menu_yncontest_unfollow'));					
							 ?>
						</p>
					<?php endif;?>
					<?php if($this->favorite):?>
						<p>
							<?php 					
								echo $this->htmlLink(
									  array('route' => 'yncontest_mycontest', 'action' => 'un-favourite', 'contestId' => $item->contest_id),
									  $this->translate('Unfavorite'),
									  array('class' => 'smoothbox buttonlink menu_yncontest_unfavourite'));					
							 ?>
						</p>
					<?php endif;?>
				</div>
			</div>	
			<div class="yncontest_contest_info">
				<div class="column">
					<p><?php echo $this->translate("Participants")?></p>
					<strong>
						<?php if($item->participants==''){
								$paticipants = 0;	
							}							
							else {
								$paticipants = $item->participants;
							}
							echo $paticipants;
						?>
					</strong>
				</div>
				<div class="column">
					<p><?php echo $this->translate("Entries")?></p>
					<strong><?php echo $item->entries;?></strong>
				</div>
				<div style="clear:both"></div>
			</div>
			<div style="clear: both"></div>	
		</li>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php
	if(isset($this->limit))
		$limit = $this->limit;
	else $limit = 4;
	
	if($this->items->getTotalItemCount() > $limit):?>	
	<div class = "yncontest_no_border">
		<?php echo $this->htmlLink(array(
			'route' => 'yncontest_general', 
			'action' => 'listing',
			'browseby' => $this->browseby,
			'contest_status' => $this->contest_status,
			),
			"<span>&rsaquo;</span>".$this->translate('View more'), 
			array('class' => 'ynContest_viewAll'));
		?>
	</div>
	<?php endif; ?>
</ul>
<?php endif;?>
