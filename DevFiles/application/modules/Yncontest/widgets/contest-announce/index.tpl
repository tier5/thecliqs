<h3><?php echo $this->translate('Announcement')?></h3>

<div class="announce_action">
	<?php $viewer = Engine_Api::_()->user()->getViewer();?>
	<?php if($this->contest->isOwner($viewer)):?> 	   		
			<?php echo $this->htmlLink(array(
			  			'route' => 'yncontest_mycontest', 
			  			'action' => 'create-announce', 
			  			'announce' => $this->announcement->getIdentity(),	
						'contestId' => $this->contest -> getIdentity(),	  		
			  			'format' => 'smoothbox'),
			  			 $this->translate('Edit'), 
			  			array('class' => 'smoothbox'));?>
			  			|
			  			<?php echo $this->htmlLink(array(
			  			'route' => 'yncontest_mycontest', 
			  			'action' => 'delete-announce', 
			  			'announce' => $this->announcement->getIdentity(),		  		
			  			'format' => 'smoothbox'),
			  			 $this->translate('Delete'), 
			  			array('class' => 'smoothbox'));      ?> 		
	    <?php endif;?>
</div>
<div class="announce_title">
	<?php echo $this->announcement->title?>
</div>


<div class="announce_descriptioin">
	<?php echo $this->viewMore($this->announcement->body, null, 100*10270)?>
</div>

  