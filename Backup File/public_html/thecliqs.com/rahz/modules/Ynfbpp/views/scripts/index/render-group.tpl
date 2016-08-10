<?php $settings =  Engine_Api::_() -> getApi('settings', 'core'); 
$allow = $this->subject->authorization()->isAllowed($this->viewer, 'view');
?>
<div class="uiContextualDialogContent">
	<div class="uiYnfbppHovercardStage">
		<div class="uiYnfbppHovercardContent">
			<table cell-padding="0">
				<tr>
					<td rowspan="2" valign="top">
					<div class="uiYnfbppScaledImageContainer">
						<?php echo $this->htmlLink($this->subject->getHref(), $this->itemPhoto($this->subject, 'thumb.profile', $this->subject->getTitle(), array('width' => '100px', 'height' => '100px'))); ?>
					</div></td>
					<td  valign="top">
					<div class="uiYnfbppHovercardTitle">
						<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>
					</div>
					<ul class="uiYnfbppHovercardInfo">
						<?php if(!$allow): ?>
							<li>
								<?php echo $this->translate('You have no permission to view this group.') ?>
							</li>	
						<?php endif; ?>
						<?php if($allow && $settings->getSetting('ynfbpp.group.description',0) && !empty($this->subject->description)): ?>
						<li>
							<?php echo $this->string()->truncate($this->string()->stripTags($this->subject->description), 300) ?>
						</li>
						<?php endif; ?>
						<?php if($allow && $settings->getSetting('ynfbpp.group.owner',0) && !empty($this->subject->description)): ?>
						<li class="uiYnfbppRow">
							<div>
								<?php echo $this->translate('Owner') ?>:
							</div>
							<span>
								<?php echo $this->subject->getOwner() ?>
							</span>
						</li>
						<?php endif; ?>
					</ul></td>
				</tr>
				<tr>
					<td  valign="bottom"> <?php if($allow && $settings->getSetting('ynfbpp.group.mutual',1)): ?>
					<?php echo $this->mutualFriends($this->subject,null, $settings->getSetting('ynfbpp.group.mutuallimit',0), '%s friend joined','%s friends joined'); ?>
					<?php endif; ?> </td>
				</tr>
			</table>
		</div>
	</div>
	<div class="uiYnfbppHovercardFooter">
		<?php if(isset($this->actions) && !empty($this->actions)): ?>
		<ul class="uiYnfbppListHorizontal">
			<?php foreach($this->actions as $action): ?>
			<li class="uiYnfbppListItem">
				<?php echo $action?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?> <div class="clearfix"></div>
	</div>
</div>
