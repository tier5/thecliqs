<!-- admin menu -->
<div class="profile_fields">
	
		<ul>
			<li>
				<span><?php echo $this->translate("Total Contests") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->totalContests); ?>
				</span>
			</li>			
			
			<li>
				<span><?php echo $this->translate("Total Participants") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->totalPaticipants); ?>
				</span>
			</li>		
			
			<li>
				<span><?php echo $this->translate("Total Entries") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->totalEntries); ?>
				</span>
			</li>		
			
			<li>
				<span><?php echo $this->translate("Views") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->totalViews); ?>
				</span>
			</li>		
			
			<li>
				<span><?php echo $this->translate("Likes") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->totalLikes); ?>
				</span>
			</li>		
			
		</ul>	
	</div>



