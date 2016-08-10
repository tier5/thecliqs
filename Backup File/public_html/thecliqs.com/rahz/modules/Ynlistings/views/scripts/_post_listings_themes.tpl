<?php if ($this->canSelectTheme) : ?>
<div class="form-wrapper form-ynlisting-choose-theme">
	<div class="form-label">
		<?php echo $this->translate('Select Themes')?>
	</div>
	<div class="form-element">
		<?php if(count($this->category->themes) > 0):?>
			<?php foreach($this->category->themes as $item) :?>
				<div class="item-form-theme-choose">
					<input <?php if($this->theme == $item) echo "checked='true'"?>  id='category_<?php echo $item?>' type='radio'  name='theme' value ='<?php echo $item?>'>
					<img width="50" src="<?php echo $this->baseUrl();?>/application/modules/Ynlistings/externals/images/<?php echo $item?>.png" >
					<?php if(!$this->select_theme) :?>
						<span class="btn-preview-theme" data-image="<?php echo $this->baseUrl();?>/application/modules/Ynlistings/externals/images/prev_<?php echo $item?>.jpg"><?php echo $this->translate('Preview Theme')?></span>
					<?php endif ;?>
				</div>
			<?php endforeach ;?>
		<?php else:?>
			<?php 
				if($this -> category -> level != 1)
				{
					$main_parent_category = $this -> category -> getParentCategoryLevel1();
				}
			?>
			<?php foreach($main_parent_category -> themes as $item) :?>
				<div class="item-form-theme-choose">
					<input <?php if($this->theme == $item) echo "checked='true'"?>  id='category_<?php echo $item?>' type='radio'  name='theme' value ='<?php echo $item?>'>
					<img width="50" src="<?php echo $this->baseUrl();?>/application/modules/Ynlistings/externals/images/<?php echo $item?>.png" >
					<?php if(!$this->select_theme) :?>
						<span class="btn-preview-theme" data-image="<?php echo $this->baseUrl();?>/application/modules/Ynlistings/externals/images/prev_<?php echo $item?>.jpg"><?php echo $this->translate('Preview Theme')?></span>
					<?php endif ;?>
				</div>
			<?php endforeach ;?>
		<?php endif;?>
	</div>
</div>
<?php else: ?>
<input id='category_<?php echo $this->category->themes[0]?>' type='hidden'  name='theme' value ='<?php echo $this->category->themes[0]?>'>
<?php endif; ?>
