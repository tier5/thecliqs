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
						<?php
							echo $this->htmlLink($this->subject->getHref(), $this->itemPhoto($this->subject, 'thumb.profile', $this->subject->getTitle(), array('width' => '100px', 'height' => '100px')));
						?>
					</div></td>
					<td  valign="top">
					<div class="uiYnfbppHovercardTitle">
						<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>
					</div>
					<ul class="uiYnfbppHovercardInfo">
						<?php if(!$allow): ?>
							<li>
								<?php echo $this->translate('You have no permission to view this profile.') ?>
							</li>
						<?php endif; ?>
						<?php if($allow && $settings -> getSetting('ynfbpp.user.status', 1) && isset($this->subject->status) ): ?>
						<li>
							<?php echo $this->string()->truncate($this->string()->stripTags($this->subject->status), 50) ?>
						</li>
						<?php endif; ?>
						<?php if($allow &&  $settings -> getSetting('ynfbpp.user.membertype', 0) && ($memberType = $this->ynfbppProfileTypeString($this->subject)) ): ?>
						<li>
							<span><?php echo $this->translate($memberType) ?></span>
						</li>
						<?php endif; ?>
						<?php if($allow &&  $settings -> getSetting('ynfbpp.user.profile', 1)): ?>
						<?php echo $this->profileFields($this->subject) ?>
						<?php endif; ?>
					</ul></td>
				</tr>
				<tr>
					<td  valign="bottom"> <?php if($allow && $settings->getSetting('ynfbpp.user.mutual',1)): ?>
					<?php echo $this->mutualFriends($this->subject,null,$settings->getSetting('ynfbpp.user.mutuallimit',5), '%s mutual friend','%s mutual friends'); ?>
					<?php endif; ?> </td>
				</tr>
			</table>
		</div>
	</div>
	<div class="uiYnfbppHovercardFooter">
		<?php if(isset($this->actions) && !empty($this->actions)): ?>
		<ul class="uiYnfbppListHorizontal">
			<?php if($this->isSubjectOnline): ?>
		    <li class="uiYnfbppListItem">
                <a class="buttonlink icon_user_online" style="height:16px;padding-left:16px" href="<?php echo $this->subject->getHref();?>" title="<?php echo $this->subject->getTitle(),' ',$this->translate("is online");?>"></a>
		    </li>
		    <?php endif; ?>
			<?php foreach($this->actions as $action): ?>
			<li class="uiYnfbppListItem">
				<?php echo $action?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?> <div class="clearfix"></div>
		<div class="clearfix"></div>
	</div>
</div>
