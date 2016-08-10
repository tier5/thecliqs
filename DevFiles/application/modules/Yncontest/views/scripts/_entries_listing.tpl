<div class="wrap_contest">
	<div class="wrap_images">
		<div class="wrap_hover">
			<strong>
				<?php 
				echo 	$this->htmlLink($this->entries->getHref(), 
								$this->string()->truncate($this->entries->getTitle(), 30), 
								array('class' => 'ynContest_thumbEntries_title', 'title' => $this->entries->getTitle())) 
				?>
			</strong>
			<p>
				<?php $user = $this->entries->getOwner() ?>
				<?php if ($user) : ?>
					<?php echo $this->translate('By') ?>
					<?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
				<?php endif; ?>
			</p>
			<?php if($this->entries->checkCotOwner()):?>
				<p>
				<?php if($this->entries->approve_status == 'pending'):?>
					<?php echo $this->htmlLink(
									 array('route' => 'yncontest_myentries', 'action' => 'approve-entry', 'id' => $this->entries->getIdentity()),
									  "<span>&rsaquo;</span>".$this->translate('Approve'),
									  array('class' => 'smoothbox ynContest_viewAll')) ?>
									  |
					<?php echo $this->htmlLink(
									  array('route' => 'yncontest_myentries', 'action' => 'deny-entry', 'id' => $this->entries->getIdentity()),
									  "<span>&rsaquo;</span>".$this->translate('Deny'),
									  array('class' => 'smoothbox ynContest_viewAll')) ?>
				
					<?php elseif($this->entries->approve_status == 'denied'):?>
						<?php echo $this->htmlLink(
										  array('route' => 'yncontest_myentries', 'action' => 'approve-entry', 'id' => $this->entries->getIdentity()),
										  "<span>&rsaquo;</span>".$this->translate('Approve'),
										  array('class' => 'smoothbox ynContest_viewAll')) ?>
					<?php else:?>
						<?php echo $this->htmlLink(
										  array('route' => 'yncontest_myentries', 'action' => 'deny-entry', 'id' => $this->entries->getIdentity()),
										  "<span>&rsaquo;</span>".$this->translate('Deny'),
										  array('class' => 'smoothbox ynContest_viewAll')) ?>
				<?php endif;?>
				</p>
			<?php endif;?>
		</div>
		<?php echo $this->htmlLink($this->entries->getHref(), $this->itemPhoto($this->entries, 'thumb.profile')) ?> 
	</div>
	<div class="wrap_vote">
		<span><?php echo $this->entries->vote_count ?></span>
		<span><?php echo $this->entries->view_count ?></span>
	</div>
</div>	
