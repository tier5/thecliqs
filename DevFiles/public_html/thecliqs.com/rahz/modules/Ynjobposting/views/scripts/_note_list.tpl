
<div class="form-wrapper">
	<div class="form-label">
		<label class="optional"><?php echo $this->translate("Notes");?></label>
	</div>
	<div class="form-element">
	<?php foreach ($this->notes as $note):?>
		<div>
			<?php $owner = Engine_Api::_()->user()->getUser($note->user_id);?>
			<?php echo $note->content;?> - 
			<?php echo $this->translate("added by");?> 
			<a href="<?php echo $owner->getHref();?>"><?php echo $owner->getTitle();?></a>
			<?php if ($note->creation_date):?>
			 - 
			 	<?php $dateObj = new Zend_Date(strtotime($note->creation_date));?>
				<?php echo $this->translate("on ");?> 
				<?php echo $dateObj -> toString("Y-M-d"); ?>
			<?php endif;?>
		</div>
	<?php endforeach;?>
	</div>
</div>
