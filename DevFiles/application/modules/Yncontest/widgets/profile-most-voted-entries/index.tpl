<div class="ynContest_participantsWrapper" style="margin-top: 20px;">
	<ul class='ynContest_participantsList'>
	    <?php foreach( $this->paginator as $entry ):?>
	      <li>
			<?php echo $this->htmlLink($entry->getHref(), $this->itemPhoto($entry, 'thumb.icon'), array("style" => "float:left;")) ?>
	        <div style="float: left; margin-left: 7px;">		
				<div style="font-weight: bold;">
					<?php echo $this->htmlLink($entry->getHref(),  $this->string()->truncate($entry->getTitle(), 15)); ?>	
				</div>
				<div>
					<div>
						<?php echo $this->translate('By:'); ?> 
						<?php $user = $entry->getOwner(); ?>
						<?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())); ?>
					</div> 
					<div> <?php echo $this->translate('Comment(s): ')?>
						<?php echo $entry->comment_count; ?> 
					</div>
				</div>	
			</div>	
	      </li>
	    <?php endforeach; ?>
	</ul>
	<!-- 
	<div>
	   <?php  echo $this->paginationControl($this->paginator, null, null, array(
	      'pageAsQuery' => false,
	      'query' => $this->formValues,
	    ));     ?>
	</div>
	 -->
	<?php if ( $this->paginator->getTotalItemCount() > $this->paginator->getItemCountPerPage() ):?>
	<div>
	<?php echo $this->htmlLink(
			array('route' => 'yncontest_mycontest', 'action' => 'view', 'contestId' => $this->contest->getIdentity(), 'order' => 'vote'),
			$this->translate("View more..."), array("style" => "float:left;")) ?>
	</div>
	<?php endif;?>
</div>


