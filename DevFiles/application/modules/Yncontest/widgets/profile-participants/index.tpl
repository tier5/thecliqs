<div id="yncontest_participants_list" class="ynContest_participantsWrapper">
	<ul id="list_member" class='ynContest_participantsList'>
	    <?php 
			$countItem = 0;
			foreach( $this->items as $item ):?>
				<?php $countItem++; if($this->limit >= $countItem):?>
			      <li>
					<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'ynContest_LRH3ULLi_thumb')) ?>
			      </li>
			    <?php endif;?>
	    <?php endforeach; ?>
	</ul>

	<?php if($this->canViewmore):?>	
		<div style="clear: both;">	
		<?php echo $this->htmlLink(
				array('route' => 'yncontest_mycontest', 'action' => 'view', 'contestId' => $this->contest_id, 'view_participants' => true),
				$this->translate("View more..."), array("style" => "float:left;")) ?>
		</div>
	<?php endif;?>
</div>


