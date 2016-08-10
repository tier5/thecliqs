<script type="text/javascript">
function deleteNote(note_id)
{
	new Request.JSON({
		'format': 'json',
		'url' : '<?php echo $this->url(array('action' => 'delete-note'), 'ynjobposting_general'); ?>',
		'data' : {
			'format' : 'json',
			'note_id' : note_id
		},
		'onRequest' : function(){
		},
		'onSuccess' : function(responseJSON, responseText)
		{
			$("ynjobposting_note_"+note_id).destroy();
		}
	}).send();
}
</script>

<?php $submissionForm = $this -> submissionForm;?>

<div class="ynjobposting-view-applications">

	<div class="ynjobposting-view-application-block">
		<?php if ($submissionForm->form_title):?>
			<h3><?php echo $submissionForm->form_title;?></h3>
		<?php endif;?>

		<?php if ($submissionForm->form_description):?>
			<div><?php echo $submissionForm->form_description;?></div>
		<?php endif;?>
	</div>

	<div class="ynjobposting-clearfix">
		<?php $photoField = $this -> apply -> getPhotoFieldValue(); ?>
		<?php if (!is_null($photoField)):?>
			<?php $file = Engine_Api::_()->getItem('storage_file', $photoField->value);?>
			<div class="ynjobposting-view-application-image">
				<img src="<?php echo $file->map();?>" />
			</div>
		<?php endif;?>

		<div class="ynjobposting-view-application-content">
			<div class="ynjobposting-view-application-job-title" title="<?php echo $this->job->getTitle();?>">
				<?php $jobTitle = $this->string()->truncate($this->string()->stripTags($this->job->getTitle()), 80);?>
				<?php echo $this->htmlLink($this->job->getHref(), $jobTitle);?>
			</div>
			<div class="ynjobposting-view-application-company-info">
				<span class="ynjobposting-view-application-company" title="<?php echo $this->company->name;?>">
					<?php $companyName = $this->string()->truncate($this->string()->stripTags($this->company->name), 30);?>
					<i class="fa fa-building"></i>
					<?php echo $this -> htmlLink($this->company->getHref(), $companyName);?>
				</span>

				<?php if ($this->job->working_place):?>
				<span class="ynjobposting-view-application-working">
					<i class="fa fa-map-marker"></i>
					<?php echo $this->job->working_place;?>
				</span>
				<?php endif;?>
			</div>
			

			<?php  $textFields = $this -> apply -> getTextFieldValue(); ?>
			<?php foreach ($textFields as $field):?>
				<?php $field -> label = str_replace("Candidate", "", $field->label);?>
				<div class="ynjobposting-view-application-field-item">
					<span><?php echo $field -> label;?></span>
					<span><?php echo $field -> value;?></span>
				</div>
			<?php endforeach;?>	
		</div>
	</div>

	<h3 style="font-size: 1.1em; font-weight: bold"><?php echo $this->translate("Note:");?></h3>
	<?php $notes = $this->apply -> getNote();?>
	<?php if (count($notes)):?>
		<ul>
		<?php foreach($notes as $note):?>
			<?php $owner = Engine_Api::_()->user()->getUser($note->user_id);?>
			<li id="ynjobposting_note_<?php echo $note->applynote_id;?>"  class="ynjobposting-view-application-note" style="margin-left: 22px;">
				<span class="ynjobposting-view-application-btn-delete"><a href="javascript:void(0);" onclick="deleteNote(<?php echo $note->applynote_id; ?>);"><i class="fa fa-times"></i></a></span>
				<div class="ynjobposting-view-application-note-author">
					<span><?php echo $this->translate("Added by");?></span>
					<span><a href="<?php echo $owner->getHref();?>"><?php echo $owner->getTitle();?></a></span>
					 
					<?php if ($note->creation_date):?>
					 	<?php $dateObj = new Zend_Date(strtotime($note->creation_date));?>
					 	<span><?php echo $this->translate("on ");?> <?php echo $dateObj -> toString("Y-M-d"); ?></span>
					<?php endif;?>
					
				</div>
				<div class="ynjobposting-view-application-notes">
					<?php echo $note->content;?>
				</div>
			</li>
		<?php endforeach;?>
		</ul>
	<?php endif;?>

	<div class="ynjobposting-view-application-form">
		<?php echo $this->form->render();?>
	</div>
</div>