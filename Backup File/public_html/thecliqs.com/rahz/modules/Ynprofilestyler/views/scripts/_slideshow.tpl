<div id="ynps-slideshow-container" 
	style="width:<?php echo $this->width?>px;height:<?php echo $this->height?>px;<?php if (empty($this->left)) echo 'margin-left:auto;margin-right:auto'?>">
	<?php $idx = 0;?>
	<?php foreach ($this->slides as $slide) : ?>
		<img src="<?php echo $slide->url?>" data-slideshow="transition:<?php echo ($idx % 2)?'pushLeft':'pushRight'?>" />
		<?php $idx++;?>
	<?php endforeach;?>
</div>