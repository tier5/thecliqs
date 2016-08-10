<?php
	$startDateObject = new Zend_Date(strtotime($this->contest->start_date));
	$endDateObject = new Zend_Date(strtotime($this->contest->end_date));
	$start_date_submit_entriesObject = new Zend_Date(strtotime($this->contest->start_date_submit_entries));
	$start_date_vote_entriesObject = new Zend_Date(strtotime($this->contest->start_date_vote_entries));
	if( $this->viewer() && $this->viewer()->getIdentity() ) 
	{
		$tz = $this->viewer()->timezone;
		$startDateObject->setTimezone($tz);
		$endDateObject->setTimezone($tz);
		$start_date_submit_entriesObject->setTimezone($tz);  
		$start_date_vote_entriesObject->setTimezone($tz);
	}
?>

<?php if($this->contest->start_date > date('Y-m-d H:i:s') && $this->contest->contest_status !='close'):?>
<div class="tip">
    <span>
    	<?php echo $this->translate('This contest will start on %s. Please revisit later.', 		
		$this->locale()->toDateTime( $startDateObject, array('size' => 'short'))); ?>           
	</span>
</div>
<br />
<?php endif;?>
<?php if($this->contest->contest_status !='close'):?>
	<?php if(!empty($this->maxEntries) ):?>
		<?php if($this->maxEntries == 99):?>
			<div class="tip">
			    <span>
			    	<?php echo $this->translate('You can submit entries no limit.'); ?>           
				</span>
			</div>
		<?php elseif($this->maxEntries>1):?>
			<div class="tip">
			    <span>
			    	<?php echo $this->translate('You can submit %s entries',$this->maxEntries); ?>           
				</span>
			</div>
		<?php else:?>
			<div class="tip">
			    <span>
			    	<?php echo $this->translate('You have only one entry'); ?>           
				</span>
			</div>
		<br />
		<?php endif;?>
	<?php endif;?>
<?php endif;?>
<div class="contest_info clearfix">
	
	<div class="ynContest_detailPage_Left">
		<div class = "contest_info_top">
			<div class="contest_stats_title">
				<span>
					<h3><?php echo $this->contest->getTitle() ?></h3>
				</span>
			</div>
			<div class="ynContest_detail">
				<div class="ynContest_detail_common">
					<p><?php echo $this->translate("Created by:")?> <?php echo $this->htmlLink($this->contest->getHref(),$this->contest->getOwner())?> | 
						<?php echo $this->translate("Contest Type:")?> <?php echo $this->htmlLink($this->url(array('action'=>'listing', 'contest_type'=>$this->contest->contest_type), 'yncontest_general'),$this->arrPlugins[$this->contest->contest_type])?></p>
					<p><?php echo $this->translate("Category:")?> <?php echo $this->htmlLink($this->url(array('action'=>'listing', 'category_id'=>$this->contest->category_id), 'yncontest_general'),$this->translate($this->contest->getContestCategoryName($this->contest->category_id)))?></p>
				</div>
				<div class="ynContest_detail_more">
					<div class="ynContest_detail_more_left">
						<div class="duration">
							<div class="duration_left">
								<h3><?php echo $this->translate("Contest Duration")?></h3>
								<p><b><?php echo $this->translate("Start Date:")?></b> <?php echo $this->locale()->toDateTime( $startDateObject, array('size' => 'short'));?></p>
								<p><b><?php echo $this->translate("End Date:")?></b> <?php echo $this->locale()->toDateTime( $endDateObject, array('size' => 'short'));?></p>
							</div>
							<div class="duration_right">
								<p>
								<?php if($this->contest->end_date < date('Y-m-d H:i:s') || $this->contest->contest_status == 'close'):?>	
									<?php echo $this->translate("End");?>
								<?php elseif($this->contest->start_date> date('Y-m-d H:i:s')):?>	
									
								<?php else:?>
									<?php
									 $item = Engine_Api::_()->getApi('core','yncontest')->gettimeleft($this->contest->getIdentity(),'end_date');
									 
									 if($item -> yearleft >= 1)
								   		echo $this->translate(array('%s year left','%s years left',$item -> yearleft),$item -> yearleft);
									 elseif($item -> monthleft >= 1)
										echo $this->translate(array('%s month left','%s months left',$item -> monthleft),$item -> monthleft);
										
									 elseif($item -> dayleft >= 1)
										echo $this->translate(array('%s day left','%s days left',$item -> dayleft),$item -> dayleft);							
									 else {							
										echo  $this->translate(array('%s hour %s minute left','%s hours %s minutes left', $item -> hourleft, $item -> minuteleft), $item -> hourleft, $item -> minuteleft);														
									}?>
								<?php endif;?>
								 
								</p>
							</div>
							<div class="clrf"></div>
						</div>
						<?php if($this->contest->end_date_submit_entries < date('Y-m-d H:i:s') || $this->contest->contest_status == 'close'):?>								
								<div class="submit_entry">
									<p><?php echo $this->translate("Submit Entries")?></p>
									<span class="count_down"><?php echo $this->translate("END");?></span>							
								</div>
						<?php elseif($this->contest->start_date_submit_entries > date('Y-m-d H:i:s')):?>	
								<div class="submit_entry">
									<p><?php echo $this->translate("Submit Entries <i>(Opening)</i>")?></p>
									<span class="count_down"><?php echo $this->locale()->toDateTime( $start_date_submit_entriesObject, array('size' => 'short'));?></span>							
								</div>						
						<?php else:?>
								<div class="submit_entry">
									<p><?php echo $this->translate("Submit Entries <i>(On Going)</i>")?></p>
									<span class="count_down"> <?php
										$item = Engine_Api::_()->getApi('core','yncontest')->gettimeleft($this->contest->getIdentity(),'end_date_submit_entries');
										 
										if($item -> yearleft >= 1)
									   		echo $this->translate(array('%s year left','%s years left',$item -> yearleft),$item -> yearleft);
										elseif($item -> monthleft >= 1)
											echo $this->translate(array('%s month left','%s months left',$item -> monthleft),$item -> monthleft);
											
										elseif($item -> dayleft >= 1)
											echo $this->translate(array('%s day left','%s days left',$item -> dayleft),$item -> dayleft);							
										else {							
											echo  $this->translate(array('%s hour %s minute left','%s hours %s minutes left', $item -> hourleft, $item -> minuteleft), $item -> hourleft, $item -> minuteleft);														
										}?></span>							
								</div>
						<?php endif;?>
						
						<?php if($this->contest->end_date_vote_entries < date('Y-m-d H:i:s') || $this->contest->contest_status == 'close'):?>								
								<div class="vote_entry">
									<p><?php echo $this->translate("Voting")?></p>
									<span class="count_down"><?php echo $this->translate("END");?></span>							
								</div>
						<?php elseif($this->contest->start_date_vote_entries > date('Y-m-d H:i:s')):?>	
								<div class="vote_entry">
									<p><?php echo $this->translate("Voting <i>(Opening)</i>")?></p>
									<span class="count_down"><?php echo $this->locale()->toDateTime( $start_date_vote_entriesObject, array('size' => 'short'));?></span>							
								</div>
						
						<?php else:?>
								<div class="vote_entry">
									<p><?php echo $this->translate("Voting <i>(On Going)</i>")?></p>
									<span class="count_down"> <?php
										$item = Engine_Api::_()->getApi('core','yncontest')->gettimeleft($this->contest->getIdentity(),'end_date_vote_entries');
										 
										if($item -> yearleft >= 1)
									   		echo $this->translate(array('%s year left','%s years left',$item -> yearleft),$item -> yearleft);
										elseif($item -> monthleft >= 1)
											echo $this->translate(array('%s month left','%s months left',$item -> monthleft),$item -> monthleft);
											
										elseif($item -> dayleft >= 1)
											echo $this->translate(array('%s day left','%s days left',$item -> dayleft),$item -> dayleft);							
										else {							
											echo  $this->translate(array('%s hour %s minute left','%s hours %s minutes left', $item -> hourleft, $item -> minuteleft), $item -> hourleft, $item -> minuteleft);														
										}?></span>							
								</div>
						<?php endif;?>						
						<div class="clrf"></div>
					</div>
					<div class="ynContest_detail_more_right">
						<ul class="yc_view_statistic">
							<h4><?php echo $this->translate("Contest Statistics")?></h4>
							<li class="ycstat ycparticipants">
								<span><?php echo $this->translate("Participants:")?></span>
								<b><?php echo count($this->contest->membership() -> getMembers($this->contest->getIdentity(), true))?></b>
							</li>
							<li class="ycstat ycentries">
								<span><?php echo $this->translate("Entries:")?></span>
								<b><?php echo count($this->contest->getEntriesByContest())?></b>
							</li>
							<li class="ycstat yclikes">
								<span><?php echo $this->translate("Like(s):")?></span>
								<b><?php echo $this->contest->like_count?></b>
							</li>
							<li class="ycstat ycviews">
								<span><?php echo $this->translate("View(s):")?></span>
								<b><?php echo $this->contest->view_count?></b>
							</li>
						</ul>
					</div>
					<div class="clrf"></div>
				</div>
			</div>
		</div>		
	</div>
</div>
<script>
window.addEvent('domready', function() {
   if(Browser.name == 'firefox') { 
		$$('.layout_yncontest_profile_information .ynContest_detail_more .ynContest_detail_more_right').setStyle('padding-bottom','0px');
   }else if(Browser.name == 'chrome') { 
		$$('.layout_yncontest_profile_information .ynContest_detail_more .ynContest_detail_more_right').setStyle('padding-bottom','0px');
   }else if(Browser.name == 'ie') { 
		$$('.layout_yncontest_profile_information .ynContest_detail_more .ynContest_detail_more_right').setStyle('padding-bottom','0px');
   }
});
</script>