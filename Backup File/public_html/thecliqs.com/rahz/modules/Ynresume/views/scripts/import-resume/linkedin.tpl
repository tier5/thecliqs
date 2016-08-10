<?php
	$profileName = $this -> me['formattedName'];
	$profileUrl = "https://www.linkedin.com/profile/view?id=" . $this -> me['id'];
?>

<h3><?php echo $this -> translate("Import Resume From LinkedIn");?></h3>
<br />
<div>
	<span><?php echo $this -> translate("Linkedin account");?></span>: 
	<a target="_blank" href="<?php echo $profileUrl;?>"><?php echo $profileName;?></a>
</div>
<br />
<div>
	<?php echo $this->translate("_YNRESUME_IMPORT_RESUME_FROM_LINKEDIN");?>
</div>
<br />
<?php if (count($this -> inputs)):?>
<form method="post" action="<?php echo $this->url(array('controller' => 'import-resume', 'action' => 'linkedin'),'ynresume_extended');?>">
	<ul class="ynresume-import-linkedin">
	<?php foreach ($this -> inputs as $key => $value):?>
		<li>
			<input type="checkbox" name="linkedin_settings[]" value="<?php echo $key;?>" checked="checked" />
			<label><?php echo $value;?></label>
		</li>
	<?php endforeach;?>
	</ul>
	<button type="submit" style="margin-top: 15px;"><?php echo $this -> translate("Import from LinkedIn");?></button>
</form>
<?php endif;?>
