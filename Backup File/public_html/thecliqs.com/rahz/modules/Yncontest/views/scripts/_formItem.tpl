<?php
$request =  Zend_Controller_Front::getInstance()->getRequest();
$action = $request -> getActionName();
$controller = $request -> getControllerName();
$titleOwner = strip_tags($this->item->getOwner()->getTitle());
 if($this->item->entry_type == 'advalbum' || $this->item->entry_type == 'ynvideo' || $this->item->entry_type == 'ynultimatevideo'):?>
	<div class="wrap_images" style="background-image: url(<?php echo $this->item->getPhotoUrl('thumb.profile'); ?>);">
		<div class="desc_contest">
			<p><?php echo $this->htmlLink($this->item->getHref(),wordwrap(Engine_Api::_()->yncontest()->subPhrase($this->item->entry_name, 47), 13, "\n", true),array('title'=>$this->string()->stripTags($this->item->entry_name))); ?></p>
		</div>
		<div class="wrap_backend">
			<p>
				<?php echo $this->htmlLink($this->item->getHref(), Engine_Api::_()->yncontest()->subPhrase($this->item->entry_name, 47), array('title' => $this->string()->stripTags($this->item->entry_name)));?>
			</p>
			<?php if($controller.'_'.$action != 'my-contest_view'):
				$contest = Engine_Api::_()->getDbTable('contests','yncontest')->find($this->item->contest_id)->current(); ?>
				<p>
				<?php echo $this->translate('In').' '.$this->htmlLink($contest->getHref(),Engine_Api::_()->yncontest()->subPhrase($contest->contest_name,25)); ?>
				</p>
			<?php endif;?>
			<p><?php echo $this->translate('Created by %s', $this->htmlLink($this->item->getOwner(),  $this->string()->truncate($titleOwner,12), array('title'=>$titleOwner)))?></p>
			
			<?php if(isset($this->deny) && $this->deny && $this->item->checkCotOwner()):?>
				<p>
				<?php if($this->item->approve_status == 'pending'):?>
					<?php echo $this->htmlLink(
									 array('route' => 'yncontest_myentries', 'action' => 'approve-entry', 'id' => $this->item->getIdentity()),
									  "<span>&rsaquo;</span>".$this->translate('Approve'),
									  array('class' => 'smoothbox ynContest_viewAll')) ?>
									  |
					<?php echo $this->htmlLink(
									  array('route' => 'yncontest_myentries', 'action' => 'deny-entry', 'id' => $this->item->getIdentity()),
									  "<span>&rsaquo;</span>".$this->translate('Deny'),
									  array('class' => 'smoothbox ynContest_viewAll')) ?>
				
					<?php elseif($this->item->approve_status == 'denied'):?>
						<?php echo $this->htmlLink(
										  array('route' => 'yncontest_myentries', 'action' => 'approve-entry', 'id' => $this->item->getIdentity()),
										  "<span>&rsaquo;</span>".$this->translate('Approve'),
										  array('class' => 'smoothbox ynContest_viewAll')) ?>
					<?php else:?>
						<?php echo $this->htmlLink(
										  array('route' => 'yncontest_myentries', 'action' => 'deny-entry', 'id' => $this->item->getIdentity()),
										  "<span>&rsaquo;</span>".$this->translate('Deny'),
										  array('class' => 'smoothbox ynContest_viewAll')) ?>
				<?php endif;?>
				</p>
			<?php endif;?>
		</div>		
	</div>
	<?php if($this->item->entry_status =='win'):?>
		<div class="award_icon"></div>
	<?php endif;?>
	
	<?php if(isset($this->my_entries)):?>
		<div class="checksub">
			<input style="position: static;"  type='checkbox' class='checkbox' name='delete[]' value="<?php echo $this->item->getIdentity() ?>"/>
		</div>	 
	<?php endif;?>
	
	<?php if(isset($this->manage_entries)): ?>
		<ul class="form-options-wrapper">
			<li>
				<div class="checksub">
					<input style="position: static;" id="ynContest_win_entry_checkbox_<?php echo $this->item->getIdentity()?>" type="checkbox" 
	
	<?php if($this->item->waiting_win == 1):?> checked
	
	<?php endif;?>
	 class="checkbox" onclick="entryChoose(<?php echo $this->item->getIdentity(); ?>,this)"  ></div></li>			
		</ul>
	<?php endif;?>
	
	<div class="wrap_info">
		<div class="wrap_info_table">
			<span><strong><?php echo $this->item->vote_count;?></strong></span>
			<span><strong><?php echo $this->item->view_count;?></strong></span>
		</div>
	</div>
<?php else: ?>
	<div class="wrap_blog_music">
		<div class="wrap_left">			
			<a class="thumbs_photo" href="<?php echo $this->item->getHref(); ?>"> <?php $src = $this->item->getPhotoUrl("thumb.icon")?>
			<?php if(!$src):?> 
				<span style=""> <?php echo $this->itemPhoto($this->item, "thumb.icon", "",array('style'=>'max-width:none;'))?></span> 
			<?php else:?> <span style="width:48px; height:48px;background-image: url(<?php echo $this->item->getPhotoUrl("thumb.icon"); ?>);"></span>
			<?php endif;?>
			</a>
		</div>
		<?php if($this->item->entry_status =='win'):?>
			<div class="award_icon"></div>
		<?php endif;?>
		<div class="wrap_right">
			<div>
				<p><?php echo $this->htmlLink($this->item->getHref(), Engine_Api::_()->yncontest()->subPhrase($this->item->entry_name, 47), array('title' => $this->string()->stripTags($this->item->entry_name)));?></p>
				<p><?php echo $this->translate('Created by %s', $this->htmlLink($this->item->getOwner(),  $this->string()->truncate($titleOwner,12), array('title'=>$titleOwner)))?>
				</p>
				<p>	
				<?php
				if($controller.'_'.$action != 'my-contest_view'){
					$contest = Engine_Api::_()->getDbTable('contests','yncontest')->find($this->item->contest_id)->current();
					echo $this->translate('In').' '.$this->htmlLink($contest->getHref(),Engine_Api::_()->yncontest()->subPhrase($contest->contest_name,25));
				}?>
				</p>
				<?php if(isset($this->deny) && $this->deny && $this->item->checkCotOwner()):?>
				<p>
				<?php if($this->item->approve_status == 'pending'):?>
					<?php echo $this->htmlLink(
									 array('route' => 'yncontest_myentries', 'action' => 'approve-entry', 'id' => $this->item->getIdentity()),
									  "<span>&rsaquo;</span>".$this->translate('Approve'),
									  array('class' => 'smoothbox ynContest_viewAll')) ?>
									  |
					<?php echo $this->htmlLink(
									  array('route' => 'yncontest_myentries', 'action' => 'deny-entry', 'id' => $this->item->getIdentity()),
									  "<span>&rsaquo;</span>".$this->translate('Deny'),
									  array('class' => 'smoothbox ynContest_viewAll')) ?>
				
					<?php elseif($this->item->approve_status == 'denied'):?>
						<?php echo $this->htmlLink(
										  array('route' => 'yncontest_myentries', 'action' => 'approve-entry', 'id' => $this->item->getIdentity()),
										  "<span>&rsaquo;</span>".$this->translate('Approve'),
										  array('class' => 'smoothbox ynContest_viewAll')) ?>
					<?php else:?>
						<?php echo $this->htmlLink(
										  array('route' => 'yncontest_myentries', 'action' => 'deny-entry', 'id' => $this->item->getIdentity()),
										  "<span>&rsaquo;</span>".$this->translate('Deny'),
										  array('class' => 'smoothbox ynContest_viewAll')) ?>
				<?php endif;?>
				</p>
			<?php endif;?>
			</div>
			<div class="vote">
				<span><strong><?php echo $this->item->vote_count;?></strong></span>
				<span><strong><?php echo $this->item->view_count;?></strong></span>
			</div>
			<?php if(isset($this->my_entries)):?>
				<div class="checksub">
					<input style="position: static;"  type='checkbox' class='checkbox' name='delete[]' value="<?php echo $this->item->getIdentity() ?>"/>
				</div>	 
			<?php endif;?>
			<?php if(isset($this->manage_entries)): ?>
					<div class="checksub">
					<input style="position: static;" id="ynContest_win_entry_checkbox_<?php echo $this->item->getIdentity()?>" type="checkbox"			
					<?php if($this->item->waiting_win == 1):?> checked <?php endif;?>
				 	class="checkbox" onclick="entryChoose(<?php echo $this->item->getIdentity(); ?>,this)"/> </div> 
			<?php endif;?>	
		</div>
		
		<div class="clrf"></div>
	</div>
<?php endif;?>
	