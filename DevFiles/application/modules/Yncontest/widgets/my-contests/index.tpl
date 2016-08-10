<div class="mycontest_search clearfix">
    <?php echo $this->form->render($this);?>
</div>
<?php 
	$viewer = Engine_Api::_()->user()->getViewer();
	if( count($this->paginator) ): ?>
	<ul class="ynContest_myContest_browse">
		<?php foreach ($this->paginator as $item): ?>
		<li>
			<div class='ynContest_myContest_browse_photo'>
				<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal', $item->getTitle())); ?>				
			</div>			
			<div class='ynContest_myContest_browse_options'>
				<?php if($item->contest_status == 'draft'):?>
					<?php echo $this->htmlLink(
						array('route' => 'yncontest_mycontest', 'action' => 'edit-contest', 'contest' => $item->contest_id),
						$this->translate('Edit'), array(
						'class' => 'buttonlink icon_yncontest_edit')) ?>				
					<?php echo $this->htmlLink(
						array('route' => 'yncontest_mycontest', 'action' => 'delete', 'contestId' => $item->contest_id, 'format' => 'smoothbox'),
						  $this->translate('Delete'),
						  array('class' => 'buttonlink smoothbox icon_yncontest_delete')) ?>		
					<?php if($item->approve_status == 'new'):?>
						<?php 	echo $this->htmlLink(
							array('route' => 'yncontest_mycontest', 'action' => 'publish', 'contest' => $item->contest_id, 'view'=>1, 'format' => 'smoothbox'),
							$this->translate('Publish'),
							array('class' => 'buttonlink smoothbox icon_yncontest_publish'))
						 ?>
					<?php endif;?>
				<?php elseif($item->contest_status == 'waiting'):?>
						<?php echo $this->htmlLink(
							array('route' => 'yncontest_mycontest', 'action' => 'delete', 'contestId' => $item->contest_id, 'format' => 'smoothbox'),
							$this->translate('Delete'),
							array('class' => 'buttonlink smoothbox icon_yncontest_delete')) ?>	
						<?php echo $this->htmlLink(
							array('route' => 'yncontest_mycontest', 'action' => 'edit-contest', 'contest' => $item->contest_id),
							$this->translate('Edit'),
							array('class' => 'buttonlink icon_yncontest_edit')) ?>	
				<?php elseif($item->contest_status == 'published'):?>	
						<?php echo $this->htmlLink(
							array('route' => 'yncontest_mycontest','action' => 'close', 'contestId' => $item->contest_id, 'format' => 'smoothbox'),
							$this->translate('Close'),
							array('class' => 'buttonlink smoothbox icon_yncontest_close')) ?>		
						<?php $service = false; ?>
			        
							<?php if($item->featured_id != 1) {								
								$service = true;
							}?>
							<?php if($item->premium_id != 1) {								
								$service = true;
							}?>
							<?php if($item->endingsoon_id != 1) {								
								$service = true;
							}?>			 				
			<?php if($service == true):?>
			<?php echo $this->htmlLink(
					array('route' => 'yncontest_mycontest', 'action' => 'service','view'=>1, 'id' => $item->contest_id, 'format' => 'smoothbox'),
					$this->translate('Update service'),
						  		  array('class' => 'buttonlink smoothbox icon_yncontest_publish')) ?>
							<?php endif;?>
							
				<?php elseif($item->contest_status == 'denied'):?>	
						<?php echo $this->htmlLink(
							array('route' => 'yncontest_mycontest', 'action' => 'delete', 'contestId' => $item->contest_id, 'format' => 'smoothbox'),
							$this->translate('Delete'),
							array('class' => 'buttonlink smoothbox icon_yncontest_delete')) ?>
						<?php echo $this->htmlLink(
							array('route' => 'yncontest_mycontest', 'action' => 'create-contest', 'contest' => $item->contest_id),
							$this->translate('Edit'), array(
							'class' => 'buttonlink icon_yncontest_edit')) ?>						
				<?php else:?>
					<?php //echo $this->translate("Closed");?>	  
				<?php endif; ?>	
				
				<?php echo $this->htmlLink(array(
				  'route' => 'yncontest_photo',				 
				  'action' => 'list-photo',
				  'contestId' => $item->getIdentity(),
				), $this->translate('Edit Photos'), array(
				  'class' => 'buttonlink icon_yncontest_photo'
			)) ?>	   											
			</div>			
			<div class='ynContest_myContest_browse_info'>
				<p class='ynContest_myContest_browse_info_title'>
					<?php echo $this->htmlLink($item->getHref(),wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($item->contest_name),100), 48, "\n", true)); ?>
                    <span class="active"><?php if($item->activated) echo $this->translate("Activated"); else echo $this->translate("Un-Activated"); ?></span> 
				</p>
				<p class='ynContest_myContest_browse_info_date'>
					<span><?php echo $this->translate("Submitted date").": ".$this->locale()->toDate( $item->creation_date, array('size' => 'long'))." "; ?></span> - 
					<span><?php echo $this->translate("Approved date").": ".$this->locale()->toDate( $item->approved_date, array('size' => 'long')); ?></span><br/>
					<span><?php echo $this->translate("Start date").": ".$this->locale()->toDate( $item->start_date, array('size' => 'long'))." "; ?></span> - 
					<span><?php echo $this->translate("End date").": ".$this->locale()->toDate( $item->end_date, array('size' => 'long')); ?></span><br/>
					<span><?php echo $this->translate("Status").": ".$item->contest_status;?></span> <br/> 
					<span><?php echo $this->translate("Entries").": ".$item->entries;?></span><br/>	
					<span><?php echo $this->translate("Views").": ".$item->view_count;?></span> - 
					<span><?php echo $this->translate("Likes").": ".$item->like_count;?></span> <br/>	
					<span><?php 
						$service = "";
						if($item->featured_id==1)
							$service .= "Featured, ";
						if($item->endingsoon_id==1)
							$service .= "Ending Soon, ";
						if($item->premium_id==1)
							$service .= "Premium ";
																														
						if($service!="")echo $this->translate("Service").": ".$service;
					?></span>				
				</p>											
			</div>			
		</li>
		<?php endforeach; ?>
	</ul>
<div>
   <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    //'query' => $this->formValues,
  )); ?>
</div>	
	
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate("There are no contest.") ?>
		</span>
	</div>
<?php endif; ?>