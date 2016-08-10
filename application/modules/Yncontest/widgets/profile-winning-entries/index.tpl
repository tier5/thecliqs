<?php if($this->contest->vote_desc!=null):?>
	<h3>
		<?php echo $this->contest->vote_desc?>
	</h3>
	<?php if(isset($this->viewvote) && $this->viewvote->getTotalItemCount()>0):?>
	<ul id="ynContest_entries_listing" class="ynContest_listCompare thumbs">
		<?php foreach ($this->viewvote as $entry): ?>
		<li style="width:<?php echo $this->width?>px; height: <?php echo $this->height?>px;">
				<?php echo $this->partial('_formItem.tpl','yncontest' ,		
						array(									
								'item' => $entry
							)) 
				?> 
		</li>
		<?php endforeach;?>	
	</ul>
	<?php else:?>
		<div class="tip">
		    <span>
		    <?php echo $this->translate('There are no entries wins by vote') ?>        
		    </span>
		</div>
	<?php endif;?>
<?php endif;?>


<?php   if($this->contest->reason_desc!=null):?>
	<h3>
		<?php echo $this->contest->reason_desc?>
	</h3>
	<?php if($this->viewowner->getTotalItemCount()>0):?>
	<ul id="ynContest_entries_listing" class="ynContest_listCompare thumbs">
		<?php foreach ($this->viewowner as $entry): ?>
		<li style="width:<?php echo $this->width?>px; height: <?php echo $this->height?>px;">
				<?php echo $this->partial('_formItem.tpl','yncontest' ,		
						array(									
								'item' => $entry
							)) 
				?> 
		</li>
		<?php endforeach;?>	
	</ul>
	<?php else:?>
		<div class="tip">
		    <span>
		    <?php echo $this->translate('There are no entries wins by owner') ?>        
		    </span>
		</div>
	<?php endif;?>
<?php endif;?>

