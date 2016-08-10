<?php if (count($this->candidates)):?>
	<div class="ynjobposting-table-header">
		<div><?php echo $this->translate("Candidate name");?></div>
		<div><?php echo $this->translate("Submitted date");?></div>
		<div><?php echo $this->translate("Status");?></div>
		<div><?php echo $this->translate("Title");?></div>
		<div><?php echo $this->translate("Option");?></div>
	</div>
	<div class="ynjobposting-table-listings">
		<?php foreach ($this->candidates as $candidate) : ?>
		<div class="ynjobposting-candidates-item">
			<div><a href="<?php echo $candidate -> getHref();?>"><?php echo $candidate -> getTitle();?></a></div>
			<div><?php echo $candidate -> creation_date;?></div>
			<div><?php echo $candidate -> status;?></div>
			<div>
			<?php $job = Engine_Api::_()->getItem('ynjobposting_job', $candidate->job_id);
			echo $job -> title;
			?>
			</div>
			<div>
				<a href="#"><?php echo $this->translate("Download");?></a>
				<?php if ($candidate->video_id || $candidate->video_link):?>
				 | 
					 <?php if ($candidate->video_id):?>
					 	<?php $video = Engine_Api::_()->getItem('video', $candidate->video_id);?>
						<a target="_blank" href="<?php echo $video -> getHref();?>"><?php echo $this->translate("View video resume");?></a>
					 <?php endif;?>
					 
					 <?php if ($candidate->video_link):?>
					 	<?php $video = Engine_Api::_()->getItem('video', $candidate->video_id);?>
						<a target="_blank" href="<?php echo $candidate->video_link;?>"><?php echo $this->translate("View video resume");?></a>
					 <?php endif;?>
					 
				<?php endif;?>
				 | 
				<a class="smoothbox" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->url(array('controller' => 'jobs', 'action' => 'view-application', 'id' => $candidate->jobapply_id), 'ynjobposting_extended');?>');"><?php echo $this->translate("View");?></a>
				 | 
				<a class="smoothbox" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->url(array('controller' => 'jobs', 'action' => 'delete-application', 'id' => $candidate->jobapply_id), 'ynjobposting_extended');?>');"><?php echo $this->translate("Delete");?></a>
				 <?php if ($candidate -> status == 'pending'):?>
				 | 
				<a class="smoothbox" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->url(array('controller' => 'jobs', 'action' => 'reject-application', 'id' => $candidate->jobapply_id), 'ynjobposting_extended');?>');"><?php echo $this->translate("Reject");?></a>
				 | 
				<a class="smoothbox" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->url(array('controller' => 'jobs', 'action' => 'pass-application', 'id' => $candidate->jobapply_id), 'ynjobposting_extended');?>');"><?php echo $this->translate("Passed");?></a>
				<?php endif;?>
				 | 
				<a class="smoothbox" href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $this->url(array('controller' => 'jobs', 'action' => 'compose-message', 'to' => $candidate->getIdentity()), 'ynjobposting_extended');?>');"><?php echo $this->translate("Message");?></a>
			</div>
		</div>
		<?php endforeach;?>
	</div>
<?php else:?>
	 <div class="tip">
	    <span>
	      <?php echo $this->translate("There is no candidates to view.");?>
	    </span>
	  </div>
	  <i id="ynjobposting_no_candidates"></i>
<?php endif;?>