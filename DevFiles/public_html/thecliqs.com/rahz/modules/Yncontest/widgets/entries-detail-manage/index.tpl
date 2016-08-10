<div>

   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>
<ul id="" class=''>
    <?php $viewer = Engine_Api::_()->user()->getViewer();
    $organizerList = $this->contest->getOrganizerList();
    ?>
    <?php foreach ($this->paginator as $item):?>
	    
	    <?php //$object =  Engine_Api::_()->yncontest()->getEntryThumnail($item->entry_type,$item->item_id);
	    ?>
	     	<div class="entries_manage_left left">
	     	 	<div class="entries_manage_summary">
	             <span>
			        	<?php echo $this->translate('Summary:')?>
			        	<?php echo $item->summary?>
	              </span>
		        </div> 
		        <!-- main photo    -->		 
            
		        <div class="contest_ga_large_photo">
			        <a href="<?php echo $object->getPhotoUrl("thumb.profile")?>" 		       
			        title="<?php echo $object->getTitle()?>">
			        <img src="<?php echo $object->getPhotoUrl("thumb.profile")?>" />
			        </a>
		        </div> 
		       
			</div>
			<div class="entries_manage_right left">
		       <div class="ynContest_listings_front">
					<ul>
						<li>
							 <div class="user_photo">
	                         	<?php 
	                         	$member = Engine_Api::_()->getItemTable('yncontest_members')->getMemberContest2(array(
									'contestId'=>$item->contest_id,
									'user_id'=> $item->user_id));	  
	                         	echo  $this->itemPhoto($member, 'thumb.normal');
							
	                         	?>        
	                         </div>
	                         <div class="user_approve">
	                         	<?php 	echo $this->translate('Approved date:');
	                         			echo $member->approve_date;?>
	                         </div>
	                         <div class="entries_manage_view">
	                         	<?php 	echo $this->translate('View(s):');?>
	                         	<span><?php echo $item->view_count;?></span>
	                         </div>
	                         <div class="entries_manage_vote">
	                         <span>
                              <?php 	echo $this->translate('Vote(s):'); ?>
                              <?php echo $item->vote_count; ?>
                            </span>
                          <?php
                   
                          if($this->flag && !$item->isOwner($viewer) && $item->checkVote()  && $this->contest -> membership() -> isMember($viewer, true) && !$organizerList->has($viewer)){
	                       	 echo $this->htmlLink(array(
					  			'route' => 'yncontest_myentries', 
					  			'action' => 'vote', 
					  			'id' => $item->entry_id,		  		
					  			'format' => 'smoothbox'),
					  			 $this->translate('Vote'), 
					  			array('class' => 'buttonlink smoothbox'));
							}?>
	                         </div>
	                         <div class="entries_manage_id">
	                         	<?php echo $this->translate('Entry_ID:');?>
	                         	<span><?php echo $item->entry_id;?></span>
	                         </div>
						</li>
						
					</ul>
				</div>	
			</div>
      <div class = "clear"></div>
		
		<div style="margin-bottom: 10px; border-bottom: 1px solid #EAEAEA;"></div>

		<?php echo $this->action("list", "comment", "core", array("type"=>"contest", "id"=>$item->entry_id)) ?>

		
	    <?php endforeach; ?>
</ul>



