<div class="ynContest_participantsWrapper" style="margin-top: 20px;">
	<ul class='ynContest_participantsList most_list'>
		<?php $countItem = 0;?>
	    <?php foreach( $this->items as $item ):?>
	      <?php $countItem++; if($this->maxItem >= $countItem):?>
		 	  
		 	  <?php if($item->entry_type == 'advalbum' || $item->entry_type == 'ynvideo'):?>
		 	  	<li>
					<?php //echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array("style" => "float:left;")) ?>
					<a href="<?php echo $item->getHref();?>">
						<div class="img_100" style="background-image:url(<?php echo $item->getPhotoUrl('thumb.profile');?>);"></div>
					</a>
			        <div style="float: left; margin-left: 4px; width: 50%">		
					<div class="title">
						<?php echo $this->htmlLink($item->getHref(),  $this->string()->truncate($item->getTitle(), 15)); ?>	
					</div>
					<div class="owner">
						<?php echo $this->translate('By:'); ?> 
						<?php $user = $item->getOwner(); ?>
						<?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())); ?>
					</div> 
					<?php 
					if($this->type == 'view_count') 
					{
						echo '<div class="view"><span class="icon"></span>'.$item->view_count.'</div>';
					} 
					elseif($this->type == 'vote_count')
					{
						echo '<div class="vote"><span class="icon"></span><p>'.$item->vote_count.'</p></div>';
					}	
					?> 
					</div>	
				</li>
		 	  <?php else:?>
		 	  	<li>
					<?php //echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array("style" => "float:left;")) ?>
					<a href="<?php echo $item->getHref();?>">
						<div class="img_48" style="background-image:url(<?php echo $item->getPhotoUrl('thumb.profile');?>);"></div>
						
					</a>
			        <div style="float: left; margin-left: 4px; width: 50%">		
					<div class="title">
						<?php echo $this->htmlLink($item->getHref(),  $this->string()->truncate($item->getTitle(), 15)); ?>	
					</div>
					<div class="owner">
						<?php echo $this->translate('By:'); ?> 
						<?php $user = $item->getOwner(); ?>
						<?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())); ?>
					</div> 
					<?php 
					if($this->type == 'view_count') 
					{
						echo '<div class="view"><span class="icon"></span>'.$item->view_count.'</div>';
					} 
					elseif($this->type == 'vote_count')
					{
						echo '<div class="vote"><span class="icon"></span><p>'.$item->vote_count.'</p></div>';
					}	
					?> 
					</div>	
				</li>
			  <?php endif;?>
	      	
	      
	      <?php endif;?>
	      
	    <?php endforeach; ?>
	</ul>
	<?php if($this->canViewmore):?>	
	<div>	
	<?php echo $this->htmlLink(
			array('route' => 'yncontest_mycontest', 'action' => 'view', 'contestId' => $this->contest_id, 'order' => $this->type),
			$this->translate("View more..."), array("style" => "float:left;")) ?>
	</div>
	<?php endif;?>
</div>


