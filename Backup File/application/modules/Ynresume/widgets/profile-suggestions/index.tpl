<ul class="resume-list">
	<?php foreach ($this->resumes as $resume):?>
	<li class="resume-item">
		<div class="resume-avatar"><?php echo $this->htmlLink($resume->getHref(), $this->itemPhoto($resume, 'thumb.icon'))?></div>
        <div class="resume-info">
            <div class="resume-title"><?php echo $this->htmlLink($resume->getHref(), $resume->getTitle())?></div>
            <div class="resume-position"><?php echo $resume->title?></div>
            <div class="resume-company"><i class="fa fa-building"></i> <?php echo $resume->company?></div>
        </div>
	</li>
	<?php endforeach;?>
</ul>
