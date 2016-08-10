<style>
	.ynjobposting-table-options a:not(:last-child) {
	  	border-right: 1px solid gray;
	  	padding-right: 5px;
	  	padding-left: 5px;
	}
</style>
<script type="text/javascript">
function reloadApplication()
{
	job_id = $("ynjobposting_list_jobs").value;
	new Request.HTML({
		method: 'post',
		url: '<?php echo $this->url(Array('controller'=>'jobs', 'action'=>'candidates'), 'ynjobposting_extended', true);?>',
		data: {
			format: 'html',
			company_id: <?php echo $this->company->getIdentity();?>,
			id: job_id 
		},
		onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript) 
		{
			$("ynjobposting_list_applications").innerHTML = responseHTML;
			
			if ($("ynjobposting_no_candidates") != null)
			{
				$("cmdDownloadAll").destroy();
			}
		}
	}).send();
}

function downloadAll()
{
	job_id = $("ynjobposting_list_jobs").value;
	window.location = '<?php echo $this->url(array('controller' => 'jobs', 'action' => 'download-all', 'company_id' => $this->company->company_id), 'ynjobposting_extended');?>' + '/id/' + job_id;
}
</script>

<div class="ynjobposting-breadcrumb-list">
	<h3>
		<a href="<?php echo $this->company->getHref();?>"><?php echo $this->company->name?></a> <span>&raquo; <?php echo $this->translate("View Applications");?></span>
	</h3>
</div>

<?php if (count($this->jobs)):?>
<div style="margin: 15px 0px;">
<select onchange="reloadApplication();" id="ynjobposting_list_jobs">
	<option value="-1"><?php echo $this->translate("All Jobs");?></option>
	<?php foreach ($this->jobs as $job) : ?>
	<option value="<?php echo $job -> getIdentity();?>" <?php echo ($this->jobId == $job -> getIdentity()) ? 'selected="selected"' : '';?>><?php echo $job -> title;?></option>
	<?php endforeach;?>
</select>
</div>

	<?php if (count($this->candidates)):?>
	<?php $downloadAll = false;?>
	<div id="ynjobposting_list_applications" class="ynjobposting-table">
		<div class="ynjobposting-table-header">
			<div><?php echo $this->translate("Candidate name");?></div>
			<div><?php echo $this->translate("Submitted date");?></div>
			<div><?php echo $this->translate("Status");?></div>
			<div><?php echo $this->translate("Title");?></div>
			<div><?php echo $this->translate("Options");?></div>
		</div>
		<div class="ynjobposting-table-listings">
			<?php foreach ($this->candidates as $candidate) : ?>
			<div class="ynjobposting-application-item">
				<div><a href="<?php echo $candidate -> getHref();?>"><?php echo $candidate -> getTitle();?></a></div>
				<div><?php echo $candidate -> creation_date;?></div>
				<div><?php echo $candidate -> status;?></div>
				<div>
				<?php $job = Engine_Api::_()->getItem('ynjobposting_job', $candidate->job_id);
				echo $job -> title;
				?>
				</div>
				<div class="ynjobposting-table-options">
					<?php if(!$candidate -> resume) :?>
						<?php
						$download = false; 
						$resumeTbl = Engine_Api::_()->getDbTable('resumefiles', 'ynjobposting');
						$resumes = $resumeTbl->fetchAll(array(
							'jobapply_id = ?' => $candidate->jobapply_id
						));
						foreach ($resumes as $resume) {
							if ($resume->file_id) {
								$file = Engine_Api::_() -> getApi('storage', 'storage') -> get($resume -> file_id, '');
								if ($file) {
									$download = true;
									$downloadAll = true;
									break;
								}
							}
						}
						if ($download) :
						?>
						<a href="<?php echo $this->url(array('controller' => 'jobs', 'action' => 'download-resume', 'id' => $candidate->jobapply_id), 'ynjobposting_extended');?>"><?php echo $this->translate("Download");?></a>
						<?php endif;?>
						<?php if ($candidate->video_id || $candidate->video_link):?>
						 
							 <?php if ($candidate->video_id):?>
							 	<?php $video = Engine_Api::_()->getItem('video', $candidate->video_id);?>
								<?php if($video):?>
								<a target="_blank" href="<?php echo $video -> getHref();?>"><?php echo $this->translate("View video resume");?></a>
								<?php endif;?>
							 <?php endif;?>
							 
							 <?php if ($candidate->video_link):?>
								<a target="_blank" href="<?php echo $candidate->video_link;?>"><?php echo $this->translate("View video resume");?></a>
							 <?php endif;?>
							 
						<?php endif;?>
					<?php elseif(Engine_Api::_()->hasModuleBootstrap('ynresume')) :?>
						<?php $resume = Engine_Api::_() -> ynresume() -> getResumeByUserId($candidate -> getOwner() -> getIdentity());?>
						<?php if($resume) :?>
						<a target="_blank" href="<?php echo $resume -> getHref();?>"><?php echo $this->translate("View Resume");?></a>
						<?php endif;?>
					<?php endif;?>
					<a class="smoothbox" href="<?php echo $this->url(array('controller' => 'jobs', 'action' => 'view-application', 'id' => $candidate->jobapply_id), 'ynjobposting_extended');?>"><?php echo $this->translate("View");?></a>
					<a class="smoothbox" href="<?php echo $this->url(array('controller' => 'jobs', 'action' => 'delete-application', 'id' => $candidate->jobapply_id), 'ynjobposting_extended');?>"><?php echo $this->translate("Delete");?></a>
					<?php if ($candidate -> status == 'pending'):?>
					<a class="smoothbox" href="<?php echo $this->url(array('controller' => 'jobs', 'action' => 'reject-application', 'id' => $candidate->jobapply_id), 'ynjobposting_extended');?>"><?php echo $this->translate("Reject");?></a>
					<a class="smoothbox" href="<?php echo $this->url(array('controller' => 'jobs', 'action' => 'pass-application', 'id' => $candidate->jobapply_id), 'ynjobposting_extended');?>"><?php echo $this->translate("Passed");?></a>
					<?php endif;?>
					<a class="smoothbox" href="<?php echo $this->url(array('controller' => 'jobs', 'action' => 'compose-message', 'to' => $candidate->getIdentity()), 'ynjobposting_extended');?>"><?php echo $this->translate("Message");?></a>
				</div>
			</div>
			<?php endforeach;?>
		</div>
	</div>
	
	<?php if ($downloadAll) :?>
	<button id="cmdDownloadAll" onclick="downloadAll();"><?php echo $this->translate("Download All");?></button>
	<?php endif;?>
	<?php else:?>
		 <div class="tip">
		    <span>
		      <?php echo $this->translate("There is no candidates to view.");?>
		    </span>
		  </div>
	
	<?php endif;?>
<?php else:?>
 <div class="tip">
    <span>
      <?php echo $this->translate("This company doesn't have any jobs to view candidates.");?>
    </span>
  </div>
<?php endif;?>