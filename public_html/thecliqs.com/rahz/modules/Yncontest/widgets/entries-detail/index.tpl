<ul id="" class=''>  
   
	     	<div class="entries_left left">
	     			<?php if($this->entry->entry_status == 'win'):?>
						<div class = "feature_icon contest_icon">
						 <?php echo $this->award->award_name;
						 ?>	
						</div>
					<?php endif;?>
					
	     	 	<div class="entries_title">
	             <span>			        	
			        	<?php echo $this->entry->getTitle()?>
	              </span>
		        </div>
		        <!-- main photo    -->		 
            
		        <div class="contest_ga_large_photo">
			        <a href="<?php echo $this->entry->getPhotoUrl("thumb.profile")?>" 		       
			        title="<?php echo $this->entry->getTitle()?>">
			        <img src="<?php echo $this->entry->getPhotoUrl("thumb.profile")?>" />
			        </a>
		        </div> 
		       
			</div>
			<div class="entries_right left">
		       <div class="ynContest_listings_front">
					<ul>
						<li>
							 <div class="user_photo">
	                         	<?php 
	                         	echo $this->htmlLink($this->member->getProfile(), $this->itemPhoto($this->member, 'thumb.normal'));
	                        
							
	                         	?>        
	                         </div>
	                         <div class="user_approve">
	                         	<?php 	echo $this->translate('Approved date:');
	                         			echo $this->member->approve_date;?>
	                         </div>
	                         <div class="entries_view">
	                         	<?php 	echo $this->translate('View(s):');?>
	                         	<span><?php echo $this->entry->view_count;?></span>
	                         </div>
	                         <div class="entries_vote">
	                         <span>
                              <?php 	echo $this->translate('Vote(s):'); ?>
                              <?php 	echo $this->entry->vote_count; ?>
                            </span>
	                          <?php
	                     
	                          if($this->flag && $this->member->member_status == 'approved' && !$this->entry->isOwner($viewer) && $this->entry->checkVote()  && $this->contest->authorization()->isAllowed($viewer,'voteentries') && !$organizerList->has($viewer)){
		                       	 echo $this->htmlLink(array(
						  			'route' => 'yncontest_myentries', 
						  			'action' => 'vote', 
						  			'id' => $this->entry->entry_id,		  		
						  			'format' => 'smoothbox'),
						  			 $this->translate('Vote'), 
						  			array('class' => 'buttonlink smoothbox'));
								}?>
	                         </div>
	                         
	                       
	                         <div class="entries_per">	  
	                            
	                              <div class="entry_id">
	                         	<?php echo $this->translate('Entry_ID:');?>
	                         	<span><?php echo $this->entry->entry_id;?></span>
	                         </div>
	                            
	                          <?php	   
	                         if($this->entry->entry_status == 'published'){

									if($this->contest->authorization()->isAllowed($viewer,'or_ban_user')){
										echo $this->htmlLink(array(
										'route' => 'yncontest_members',
										'action' => 'ban-member',
										'id' => $this->entry->entry_id,
										'format' => 'smoothbox'),
										$this->translate('Ban user'),
										array('class' => 'buttonlink smoothbox'));
									}
									if($this->contest->authorization()->isAllowed($viewer,'or_give_award')){
										echo $this->htmlLink(array(
										'route' => 'yncontest_myentries',
										'action' => 'give-award',
										'id' => $this->entry->entry_id,
										'format' => 'smoothbox'),
										$this->translate('Give Award'),
										array('class' => 'buttonlink smoothbox'));
									}
									
								}
								elseif($this->entry->entry_status == 'draft'){
									echo $this->htmlLink(array(
									'route' => 'yncontest_myentries',
									'action' => 'approved',
									'id' => $this->entry->entry_id,
									'format' => 'smoothbox'),
									$this->translate('Approve'),
									array('class' => 'buttonlink smoothbox'));
									echo $this->htmlLink(array(
									'route' => 'yncontest_myentries',
									'action' => 'deny',
									'id' => $this->entry->entry_id,
									'format' => 'smoothbox'),
									$this->translate('Deny'),
									array('class' => 'buttonlink smoothbox'));
								}
								else{
								
								}
								if($this->contest->authorization()->isAllowed($viewer,'or_edit_entries') && $this->entry->entry_status != 'win'){
									echo $this->htmlLink(array(
											'route' => 'yncontest_myentries',
											'action' => 'edit',
											'id' => $this->entry->entry_id,
											'format' => 'smoothbox'),
											$this->translate('Edit'),
											array('class' => 'buttonlink smoothbox'));
								}
								
								?>
	                         </div>
						</li>
						
					</ul>
				</div>	
			</div>
      <div class = "clear"></div>
		
	<div style="margin-bottom: 10px; border-bottom: 1px solid #EAEAEA;"></div>
	<div class="entries_summary">
	             <span>
			        	<b><?php echo $this->translate('Summary:')?></b>
			        	<?php echo $this->entry->summary?>
	              </span>
		        </div> 
	
		
	  
</ul>



