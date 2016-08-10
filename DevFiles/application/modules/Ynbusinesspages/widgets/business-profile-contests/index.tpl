<script type="text/javascript">
	en4.core.runonce.add(function(){
		var anchor = $('ynbusinesspages_contest').getParent();
		$('ynbusinesspages_contest_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
		$('ynbusinesspages_contest_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

		$('ynbusinesspages_contest_previous').removeEvents('click').addEvent('click', function(){
			en4.core.request.send(new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
				data : {
					format : 'html',
					subject : en4.core.subject.guid,
					page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
				}
			}), {
				'element' : anchor
			})
		});

		$('ynbusinesspages_contest_next').removeEvents('click').addEvent('click', function(){
			en4.core.request.send(new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
				data : {
					format : 'html',
					subject : en4.core.subject.guid,
					page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
				}
			}), {
				'element' : anchor
			})
		});
	});
</script>

 <div class="ynbusinesspages-profile-module-header">
	<!-- Menu Bar -->
	<div class="ynbusinesspages-profile-header-right">
		<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
			<?php echo $this->htmlLink(array(
				'route' => 'ynbusinesspages_extended',
				'controller' => 'contest',
				'action' => 'list',
				'business_id' => $this->business->getIdentity(),
				'parent_type' => 'ynbusinesspages_business',
				 'tab' => $this->identity,
			), '<i class="fa fa-list"></i>'.$this->translate('View all Contests'), array(
				'class' => 'buttonlink'
			))
			?>
		<?php endif; ?>

		<?php if ($this->canCreate):?>
			<?php echo $this->htmlLink(array(
				'route' => 'yncontest_mycontest',
				'controller' => 'my-contest',
				'action' => 'create-contest',
				'business_id' => $this->business->getIdentity(),
				'parent_type' => 'ynbusinesspages_business',
			), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Contest'), array(
				'class' => 'buttonlink'
			))
			?>
		<?php endif; ?>
	</div>      

	<div class="ynbusinesspages-profile-header-content">
		<?php if( $this->paginator->getTotalItemCount() > 0 ): 
			$business = $this->business;?>
			<span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
			<?php echo $this-> translate(array("ynbusiness_contest", "Contests", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
		<?php endif; ?>
	</div>
</div>

<div class="ynbusinesspages_list" id="ynbusinesspages_contest">
	<!-- Content -->
	<?php if( $this->paginator->getTotalItemCount() > 0 ): 
	$business = $this->business;?>
	<ul class="ynbusinesspages_contest contest_browse large_contest_list">  
		<?php $zebra = 0;?>         
		<?php foreach ($this->paginator as $contest): 
			$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($contest);?>
		<li class="clearfix <?php echo ($zebra % 2) == 0? 'odd' : 'even';?>">
			<div class="contest_large_img" style="background-image:url(<?php echo $contest->getPhotoUrl('thumb.profile');?>);">
				<div class="corner_icon <?php echo Engine_Api::_()->yncontest()->arrPlugins[$contest->contest_type]?>"></div>
				<div class="wrap_link">
				<?php if($contest->contest_status == 'close'):?>
					<div class="link"><span class="close"><?php echo $this->translate('CLOSED');?></span></div>                 
				<?php else:?>
					<?php if($contest->featured_id):?>
						<div class="link"><span class="feature"><?php echo $this->translate('FEATURE');?></span></div>      
					<?php endif; ?>
					<?php if($contest->premium_id):?>
					<div class="link"><span class="premium"><?php echo $this->translate('PREMIUM');?></span></div>
					<?php endif; ?>
					<?php if($contest->endingsoon_id):?>
					<div class="link"><span class="ending_soon"><?php echo $this->translate('ENDING SOON');?></span></div>
					<?php endif; ?>
				<?php endif; ?>
				</div>
				<div class="desc_contest">
					<p><?php echo $this->htmlLink($contest->getHref(),wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($contest->contest_name),20), 13, "\n", true),array('title'=>$this->string()->stripTags($contest->contest_name))); ?></p>
				</div>
				<div class="wrap_desc">
					<p><?php echo $this->htmlLink($contest->getHref(),wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($contest->contest_name),20), 13, "\n", true),array('title'=>$this->string()->stripTags($contest->contest_name))); ?></p>
					<p><span>
					<?php 
					   if($contest->start_date > date('Y-m-d H:i:s'))
						{
							echo $this->locale()->toDateTime( $contest->start_date, array('size' => 'short'));
						}
						elseif($contest->end_date < date('Y-m-d H:i:s'))
						{   
							echo $this->translate("End");   
						}   
						else
						{
							if($contest -> yearleft >= 1)
								echo $this->translate(array('%s year left','%s years left',$contest -> yearleft),$contest -> yearleft);
							elseif($contest -> monthleft >= 1)
								echo $this->translate(array('%s month left','%s months left',$contest -> monthleft),$contest -> monthleft);
								
							elseif($contest -> dayleft >= 1)
								echo $this->translate(array('%s day left','%s days left',$contest -> dayleft),$contest -> dayleft);                           
							else {                          
								echo  $this->translate(array('%s hour %s minute left','%s hours %s minutes left', $contest -> hourleft, $contest -> minuteleft), $contest -> hourleft, $contest -> minuteleft);                                                     
							}
						}   
					?></span>
					</p>
					<p><?php echo $this->translate('Created by')?> <?php echo $owner?></p>
					<?php if($this->follow):?>
						<p>
							<?php                   
								echo $this->htmlLink(
									  array('route' => 'yncontest_mycontest', 'action' => 'un-follow', 'contestId' => $contest->contest_id),
									  $this->translate('Unfollow'),
									  array('class' => 'smoothbox buttonlink menu_yncontest_unfollow'));                    
							 ?>
						</p>
					<?php endif;?>
					<?php if($this->favorite):?>
						<p>
							<?php                   
								echo $this->htmlLink(
									  array('route' => 'yncontest_mycontest', 'action' => 'un-favourite', 'contestId' => $contest->contest_id),
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
						<?php if($contest->participants==''){
								$paticipants = 0;   
							}                           
							else {
								$paticipants = $contest->participants;
							}
							echo $paticipants;
						?>
					</strong>
				</div>
				<div class="column">
					<p><?php echo $this->translate("Entries")?></p>
					<strong><?php echo $contest->entries;?></strong>
				</div>
				<div style="clear:both"></div>
			</div>
			<div style="clear: both"></div> 
		</li>  
		<?php endforeach; ?>             
	</ul>  
	
	<div class="ynbusinesspages-paginator">
		<div id="ynbusinesspages_contest_previous" class="paginator_previous">
			<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
			  'onclick' => '',
			  'class' => 'buttonlink icon_previous'
			)); ?>
		</div>
		<div id="ynbusinesspages_contest_next" class="paginator_next">
			<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
			  'onclick' => '',
			  'class' => 'buttonlink_right icon_next'
			)); ?>
		</div>
	</div>
	
	<?php else: ?>
	<div class="tip">
		<span>
			 <?php echo $this->translate('No contests have been created.');?>
		</span>
	</div>
	<?php endif; ?>
</div>