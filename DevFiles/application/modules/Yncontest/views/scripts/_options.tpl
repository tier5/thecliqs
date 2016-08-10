<script type="text/javascript">
   function follow_page()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'contest/my-contest/follow',
            'data' : {
                'contestId' : <?php echo $this->contest->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
                obj = document.getElementById('follow_id');
                obj.innerHTML = '<a class = "buttonlink menu_yncontest_unfollow" href="javascript:;" onclick="unfollow_page()">' + '<?php echo $this->translate("Unfollow")?>' + '</a>';
            }
        });
        request.send();  
   } 
   function unfollow_page()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'contest/my-contest/un-follow-ajax',
            'data' : {
                'contestId' : <?php echo $this->contest->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
                obj = document.getElementById('follow_id');
                obj.innerHTML = '<a class = "buttonlink menu_yncontest_follow" href="javascript:;" onclick="follow_page()">' + '<?php echo $this->translate("Follow")?>' + '</a>';
            }
        });
        request.send();  
   }
   function favourite_page()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'contest/my-contest/favourite',
            'data' : {
                'contestId' : <?php echo $this->contest->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
                obj = document.getElementById('favourite_id');
                obj.innerHTML = '<a class = "buttonlink menu_yncontest_unfavourite" href="javascript:;" onclick="unfavourite_page()">' + '<?php echo $this->translate("Unfavorite")?>' + '</a>';
            }
        });
        request.send();  
   } 
   function unfavourite_page()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'contest/my-contest/un-favourite-ajax',
            'data' : {
                'contestId' : <?php echo $this->contest->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
                obj = document.getElementById('favourite_id');
                obj.innerHTML = '<a class = "buttonlink menu_yncontest_favourite" href="javascript:;" onclick="favourite_page()">' + '<?php echo $this->translate("Favorite")?>' + '</a>';
            }
        });
        request.send();  
   }    
 </script>
 <div class="profile_img"> 	
	<?php echo $this->htmlLink($this->contest->getHref(), $this->itemPhoto($this->contest, "thumb.profile")) ?>
	<div class = "wrap_link">
		<?php if($this->contest->contest_status == 'close'):?>
			<div class="link close"><?php echo $this->translate('CLOSED');?></div>					
		<?php else:?>
			<?php if($this->contest->featured_id):?>
				<div class="link feature"><?php echo $this->translate('FEATURE');?></div>					
			<?php endif; ?>
			<?php if($this->contest->premium_id):?>
			<div class="link premium"><?php echo $this->translate('PREMIUM');?></div>
			<?php endif; ?>
			<?php if($this->contest->endingsoon_id):?>
			<div class="link ending_soon"><?php echo $this->translate('ENDING SOON');?></div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if(empty($this->contest->activated)):?>
			<div class="link inactivated"><span class="inactivated"><?php echo $this->translate("UNACTIVATED")?></span></div>	
        <?php endif;?>
		
	</div>
</div> 
<div id="profile_options">
<ul>


 <?php  $viewer = Engine_Api::_()->user()->getViewer(); ?>
 
	
    
 	
 	<?php 
    //if($this->contest->checkAllow(array("action"=>"editcontests", "user_id"=>$viewer->getIdentity()))):
 	if ($this->contest->authorization()->isAllowed($viewer,"editcontests")):	 
 	?>
		<li id = "edit_id">
			<?php echo $this->htmlLink(array(
					'route' => 'yncontest_mycontest', 
					'action' => 'edit-contest', 
					'contest' => $this->contest->getIdentity()	  		
					),
					$this->translate('Edit'), 
					array('class' => 'buttonlink  menu_yncontest_edit'));      ?> 
		</li>  
	<?php endif;?>  	
	 <?php
	 // if($this->contest->checkAllow(array("action"=>"deletecontests", "user_id"=>$viewer->getIdentity()))):
	  if (($viewer->isAdminOnly() && $this->contest->contest_status == 'close' ) ||$this->contest->authorization()->isAllowed($viewer,"deletecontests")):	
	  ?>	 
	   	<li id = "delete_id">
		<?php echo $this->htmlLink(array(
			  		'route' => 'yncontest_mycontest', 
			  		'action' => 'delete', 
					'contestId' => $this->contest->getIdentity(),		  		
			  		'format' => 'smoothbox'),
			  		 $this->translate('Delete'), 
			  		array('class' => 'buttonlink smoothbox menu_yncontest_delete'));      ?> 
		</li>  
	<?php endif;?> 
	 <?php
	 // if($this->contest->checkAllow(array("action"=>"publish", "user_id"=>$viewer->getIdentity()))):
	 if($this->contest->isOwner($viewer) && $this->contest->contest_status == 'draft'):

	 ?>		 
		<li id = "publish_id">
		<?php echo $this->htmlLink(array(
			  		'route' => 'yncontest_mycontest', 
			  		'action' => 'publish', 					
					'contest' => $this->contest->getIdentity(), 
					'view'=>1	,  		
			  		'format' => 'smoothbox'),
			  		 $this->translate('Publish'), 
			  		array('class' => 'buttonlink smoothbox menu_yncontest_publish'));      ?>
		</li>   
	<?php endif;?> 	
	
    
	
	<?php
	//if($this->contest->contest_status == 'published'  && $this->contest->checkAllow(array("action"=>"close", "user_id"=>$viewer->getIdentity()))):
	if(($this->contest->isOwner($viewer) || $viewer->isAdminOnly()) && $this->contest->contest_status == 'published'):
	?>    
		<li id = "close_id">
			<?php echo $this->htmlLink(array(
					'route' => 'yncontest_mycontest', 
					'action' => 'close', 
					'contestId' => $this->contest->getIdentity(),		  		
					'format' => 'smoothbox'),
					$this->translate('Close Contest'), 
					array('class' => 'buttonlink smoothbox menu_yncontest_close'));      ?> 
		</li> 
	<?php endif;?>
  
	<?php if($viewer->getIdentity() > 0):?>	
	
				<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('yncontest.print', 0) == 1): ?>
				    <li>
				    <a href="javascript:;" class="buttonlink menu_yncontest_print" onclick="window.open('<?php echo $this->contest->getPrintHref();?>', 'mywindow', 'location=1,status=1,scrollbars=1,  width=900,height=700')" > <?php echo $this->translate('Print');?> </a>
				    </li>
			    <?php endif;?>
    
		  		<li id = "follow_id">
		  		
			    <?php if($this->contest->checkFollow()):  ?>
			      	<a class = 'buttonlink menu_yncontest_follow' href="javascript:;" onclick="follow_page()"><?php echo $this->translate('Follow')?></a>
			    <?php
			    else: ?>	       
			        <a class = 'buttonlink menu_yncontest_unfollow' href="javascript:;" onclick="unfollow_page()"><?php echo $this->translate('Unfollow')?></a>
			    <?php endif; ?>
			    </li>  
	
	    
	
			    <li id = "favourite_id">
			    <?php if($this->contest->checkFavourite()): ?>    
			      	<a class = 'buttonlink menu_yncontest_favourite' href="javascript:;" onclick="favourite_page()"><?php echo $this->translate('Favorite')?></a>
			    <?php
			    else: ?>     
			       	<a class = 'buttonlink menu_yncontest_unfavourite' href="javascript:;" onclick="unfavourite_page()"><?php echo $this->translate('Unfavorite')?></a>
			    <?php
			    endif; 
			    ?>
			    </li>  
	
  			
  			
  			<li id = "share_id">	
			<?php echo $this->htmlLink(array(
			  			'module'=> 'activity',
				        'controller' => 'index',
				        'action' => 'share',
				        'route' => 'default',
				        'type' => 'contest',
				        'id' => $this->contest->getIdentity(),
				        'format' => 'smoothbox'),
			  			 $this->translate('Share'), 
			  			array('class' => 'buttonlink smoothbox menu_yncontest_share'));      ?>    
			
			</li>
			<li id = "invitation_id">		
					<?php echo $this->htmlLink(array(
					  			'route' => 'yncontest_mycontest', 
					  			'action' => 'invite-members', 
					  			'contestId' => $this->contest->getIdentity(),		  		
					  			'format' => 'smoothbox'),
					  			 $this->translate('Invite Friends'), 
					  			array('class' => 'buttonlink smoothbox menu_yncontest_invite'));      ?>    
			</li>  
		<?php if($this->contest->contest_status == 'published'):?>
			<?php if($this->contest->start_date <= date('Y-m-d H:i:s') && date('Y-m-d H:i:s') <= $this->contest->end_date ):?>
		
			 	<?php if(!$this->contest -> membership() -> isMember($viewer)):?> 			 
							    <li id = "join_id">
								<?php echo $this->htmlLink(array(
								  			'route' => 'yncontest_mycontest', 
								  			'action' => 'join', 
								  			'contestId' => $this->contest->getIdentity(),		  		
								  			'format' => 'smoothbox'),
								  			 $this->translate('Join Contest'), 
								  			array('class' => 'buttonlink smoothbox menu_yncontest_join'));      ?>    		
						   		</li>						   
				<?php else:?>	
				  	<?php if(!$this->contest->IsOwner($viewer)):?>
							    <li id = "join_id">
								<?php echo $this->htmlLink(array(
								  			'route' => 'yncontest_mycontest', 
								  			'action' => 'leave', 
								  			'contestId' => $this->contest->getIdentity(),		  		
								  			'format' => 'smoothbox'),
								  			 $this->translate('Leave Contest'), 
								  			array('class' => 'buttonlink smoothbox menu_yncontest_leave'));      ?>    		
						   		</li>
					<?php endif;?>		
					<?php if($this->contest->activated && $this->checkMaxEntries && $this->contest->authorization()->isAllowed($viewer,'createentries') && ($this->contest->start_date_submit_entries <= date('Y-m-d H:i:s') && date('Y-m-d H:i:s') <= $this->contest->end_date_submit_entries ) && $this->plugin):?>			
						   		<li id = "submit_id">
								<?php echo $this->htmlLink(array(
								  			'route' => 'yncontest_mycontest', 
								  			'action' => 'view', 
								  			'contestId' => $this->contest->getIdentity(),
											'submit' => 1		  		
								  			),
								  			 $this->translate('Submit an entry'), 
								  			array('class' => 'buttonlink menu_yncontest_submit'));      ?>    		
						   		</li>					  
				   		<?php endif;?>			   			   	
				<?php endif;?> 
			<?php endif;?>
		<?php endif;?>
	
 	<?php endif;?>
 
   		
  
    
   <?php if($this->contest->isOwner($viewer) && count($this->announcement)==0):?> 
   		<li id = "announce_id">
		<?php echo $this->htmlLink(array(
		  			'route' => 'yncontest_mycontest', 
		  			'action' => 'create-announce', 
		  			'contestId' => $this->contest->getIdentity(),		  		
		  			'format' => 'smoothbox'),
		  			 $this->translate('Create an Announcement'), 
		  			array('class' => 'buttonlink smoothbox menu_yncontest_announce'));      ?>  
		</li>  
   <?php endif;?>
    
    <li id = "promote_id">
		<?php echo $this->htmlLink(array(
		  			'route' => 'yncontest_general', 
		  			'action' => 'promote', 
		  			'contestId' => $this->contest->getIdentity(),		  		
		  			'format' => 'smoothbox'),
		  			 $this->translate('Promote Contest'), 
		  			array('class' => 'buttonlink smoothbox menu_yncontest_promote'));      ?>  
	</li>  
	
	<li>
      <?php echo $this->translate("Contest ID"); ?>: <?php echo $this->contest->getIdentity();?>
    </li>
    
    </ul> 
 </div>     
