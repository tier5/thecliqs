<?php if($this->totalItems > 0): ?>
	<?php if($this->title): ?>
	<h3><?php echo $this->translate($this->title);?></h3>
	<?php endif;?>
	<ul class="ynContest_LRH3ULLi">
		<?php foreach($this->items as $item): ?>
		<?php if(Engine_Api::_()->user()->getUser($item->user_id)->getIdentity() != 0): ?>
			<li>
				<?php
				$iphoto = Engine_Api::_() -> yncontest() -> getEntryThumnail($item -> entry_type, $item -> item_id);
				if (is_object($iphoto))
				{
					if ($item -> entry_type == 'ynblog')
						echo $this -> htmlLink($item -> getHref(), $this -> itemPhoto($iphoto -> getOwner(), 'thumb.icon'), array('class' => 'ynContest_LRH3ULLi_thumb'));
					else
						echo $this -> htmlLink($item -> getHref(), $this -> itemPhoto($iphoto, 'thumb.icon'), array('class' => 'ynContest_LRH3ULLi_thumb'));
				}
				else
					echo '<img src="" width="48px" height="48px" />';
				?>			
				<div class='ynContest_LRH3ULLi_info'>
					<div class='ynContest_LRH3ULLi_name'>
						<?php echo $this -> htmlLink($item -> getHref(), $this->string()->truncate($item -> entry_name, 30), array('title'=>$item->entry_name));?>
					</div>
					<div class='ynContest_LRH3ULLi_listInfo'>
						<?php echo $this -> translate('By') .'&nbsp;'. $item -> getOwner();?>
					</div>
					<div class='ynContest_LRH3ULLi_listInfo'>
						<?php echo $this -> translate("Vote:") .'&nbsp;'. $item -> vote_count . " - " . $this -> translate("Like:") .'&nbsp;'. $item -> like_count;?>
					</div>
					<div class='ynContest_LRH3ULLi_listInfo'>
						<?php echo $this -> translate('Award:') . '<span>&nbsp;' . $item -> award_name . '</span>';?>
					</div>				
				</div>			
			</li>
		<?php endif;?>
		<?php endforeach;?>
	</ul>
<?php endif;?>