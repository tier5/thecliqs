<?php $resume = Engine_Api::_()->ynresume()->getResumeByUserId($this->viewer()->getIdentity());?>

<div class="ynresume-import">
	<div><?php echo $this -> translate("You can import resume from external sources into this site using supported options below");?></div>
	<div>
        <?php if ($this -> canImport) :?>
		<a href="<?php echo $this -> url(array('controller' => 'import-resume', 'action' => 'linkedin'), 'ynresume_extended');?>">
			<button><?php echo $this -> translate("Import from LinkedIn");?></button>
		</a>
        <?php else: ?>
        <div class="tip">
            <span><?php echo $this -> translate("Please contact admin to enable this function.");?></span>
        </div>
        <?php endif; ?>
	</div>	
</div>

<?php if (!is_null($resume)):?>
<div class="ynresume-export">
	<div><?php echo $this -> translate("You can export your resume from this site to a file");?></div>
	<div>
		<a target="_blank" href="<?php echo $this -> url(array('controller' => 'resume', 'action' => 'export-pdf', 'resume_id' => $resume->getIdentity()), 'ynresume_extended');?>">
			<button><?php echo $this -> translate("Export to PDF");?></button>
		</a>
		<a href="<?php echo $this -> url(array('controller' => 'resume', 'action' => 'export-word', 'resume_id' => $resume->getIdentity()), 'ynresume_extended');?>">
			<button><?php echo $this -> translate("Export to docx");?></button>
		</a>
		
	</div>
</div>
<?php endif;?>