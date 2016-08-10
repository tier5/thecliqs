<?php if( count($this->viewers) > 0 ): ?>
	<?php foreach($this -> viewers as $viewer) :?>
		<div class="ynresume-who-viewed-resume-item">
		<?php $user = Engine_Api::_() -> getItem('user', $viewer -> user_id); ?>
		<?php if($user -> getIdentity()) :?>
			<div class="ynresume-who-viewed-resume-thumb">
			<?php echo $this -> itemPhoto($user, 'thumb.icon');?>
			</div>
	
			<div class="ynresume-who-viewed-resume-main">
			<?php $resume = Engine_Api::_() -> ynresume() -> getUserResume($user -> getIdentity());?>
			<?php if(!empty($resume)) :?>
				<?php echo $this -> htmlLink($resume -> getHref(), $user -> getTitle());?>
			<?php else:?>
				<?php echo $this -> htmlLink($user -> getHref(), $user -> getTitle());?>
			<?php endif;?>
			</div>
		<?php endif;?>
		</div>
	<?php endforeach;?> 
<?php else: ?>
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no viewers yet.') ?>
		</span>
    </div>
<?php endif; ?>

<?php if($this -> resume -> serviced) :?>
	<div class="ynresume-who-viewed-resume-description">
	<?php echo $this -> htmlLink($this -> url(array('action' => 'who-viewed-me'), 'ynresume_general', true),$this -> translate("View more"), array('class' => 'button bold fullwidth')); ?>
	</div>
<?php else:?> <!-- no service -->
	<div class="ynresume-who-viewed-resume-description"><?php echo $this -> translate('Register service to see the full list of <span>%s people</span> that viewed your resume', $this -> total) ?></div>

	<?php echo $this -> htmlLink($this -> url(array('action' => 'service', 'resume_id' => $this -> resume -> getIdentity()), 'ynresume_specific', true),$this -> translate('Register "<b>Who Viewed Me</b>" service'), array('class' => 'smoothbox button fullwidth')); ?>
<?php endif;?>
