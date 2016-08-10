<div class="ynfeed_more_friend">
	<h3><?php echo $this -> translate("People")?></h3>
	<ul>
		<?php foreach($this -> friends as $user):?>
		<li>
			<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('class' => 'ynfeedmembers_thumb', 'target' => '_top')) ?>
		      <div class='ynfeedmembers_info'>
		        <div class='ynfeedmembers_name'>
		          <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('target' => '_top')) ?>
		        </div>
		        <div class='ynfeedmembers_friends'>
		          <?php echo $this->translate(array('%s friend', '%s friends', $user->member_count),$this->locale()->toNumber($user->member_count)) ?>
		        </div>
	        </div>
		</li>
		<?php endforeach;?>
	</ul>
</div>