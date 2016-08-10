<?php
    //get variables 
    $job = $this->job;
    $canApply = $this->canApply;
    $canShare = $this->canShare;
    $canPrint = $this->canPrint;
    $canReport = $this->canReport;
    $submissionForm = $job->getSubmissionForm();
?>
<div id="job-profile-options">
    <!-- Job button options-->
    <div id="job-button-options">
        <!-- employees's view-->
        <!-- Apply job -->
        <?php if (!$job->isOwner() && $canApply && $submissionForm) : ?>
            <?php if ($job->isPublished()) : ?>
                <div id="apply-job">
                <?php if (!$job->hasApplied()) : ?>
                    <button id="apply-job-btn" onclick="applyJob()"><?php echo $this->translate('Apply Job')?></button>
                <?php else: ?>
                    <button class="disabled-btn" disabled><?php echo $this->translate('Applied')?></button>
                <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Expired Job-->
        <?php if (!$job->isOwner() && $job->isExpired()): ?>
        <div id="expired-job">
            <button class="disabled-btn" disabled><?php echo $this->translate('Expired')?></button>
        </div>
        <?php endif; ?>
        
        <!-- employer's view-->
        <!-- edit job-->
        <?php if ($job->isEditable()) :?>
        <div id="edit-job">
            <a href="javascript:void(0)" id="edit-job-btn" onclick="editJob()">
                <i class="fa fa-pencil-square-o"></i>
                <?php echo $this->translate('Edit Job Details')?>
            </a>
        </div>    
        <?php endif; ?>
        
        <!-- view applications-->
        <?php if ($job->isOwner() && ($job->isPublished() || $job->isEnded() || $job->isExpired())) :?>
        <div id="view-applications">
            <a href="javascript:void(0)" id="view-applications-btn" onclick="viewApplications()">
                <i class="fa fa-paperclip"></i>
                <?php echo $this->translate('View Applications')?>
            </a>
        </div>    
        <?php endif; ?>
        
        <!-- end job-->
        <?php if ($job->isEndable() && $job->isPublished()) :?>
        <div id="end-job">
            <a href="javascript:void(0)" id="end-job-btn" onclick="endJob()">
                <i class="fa fa-times-circle-o"></i>
                <?php echo $this->translate('End this Job')?>
            </a>
        </div>    
        <?php endif; ?>
        
        <!-- delete job-->
        <?php if ($job->isDeletable()) :?>
        <div id="delete-job">
            <a href="javascript:void(0)" id="delete-job-btn" onclick="deleteJob()">
                <i class="fa fa-trash-o"></i>
                <?php echo $this->translate('Delete this Job')?>
            </a>
        </div>    
        <?php endif; ?>
    </div>
    
    <!-- Job link options-->
    <?php if ($job->isPublished()) : ?>
    <div id="job-link-options">
        <!-- Save Job-->
        <?php if (!$job->isOwner() && $canApply && !$job->hasApplied() && !$job->hasSaved()) : ?>
            <div id="save-job">
                <?php $url = $this -> url(array(
                'module' => 'ynjobposting_job',
                'controller' => 'jobs',
                'action' => 'save',
                'id' => $job->getIdentity(),
                'format' => 'smoothbox'),'ynjobposting_job', true)
                ;?>
                <a href="javascript:void(0);" onclick="checkOpenPopup('<?php echo $url?>')">
                    <i class="fa fa-floppy-o"></i>
                    <?php echo $this->translate('Save this Job')?>
                </a>
            </div>
        <?php endif; ?>

        <!-- promote this job-->
        <div id="promote-job">
            <?php $url = $this -> url(array(
                'module' => 'ynjobposting_job',
                'controller' => 'jobs',
                'action' => 'promote',
                'id' => $job->getIdentity(),
                'format' => 'smoothbox'),'ynjobposting_job', true)
            ;?>
            <a href="javascript:void(0);" onclick="checkOpenPopup('<?php echo $url?>')">
                <i class="fa fa-bullhorn"></i>
                <?php echo $this->translate('Promote this Job')?>
            </a>
        </div>

        <!-- share this job-->
        <?php if ($canShare) : ?>
        <div id="share-job">
            <?php $url = $this -> url(array(
                'module' => 'activity',
                'controller' => 'index',
                'action' => 'share',
                'type' => 'ynjobposting_job',
                'id' => $job->getIdentity(),
                'format' => 'smoothbox'),'default', true)
            ;?>
            <a href="javascript:void(0);" onclick="checkOpenPopup('<?php echo $url?>')">
                <i class="fa fa-share-alt"></i>
                <?php echo $this->translate('Share')?>
            </a>
        </div>
        <?php endif; ?>
        <!-- print this job-->
        <?php if ($canPrint) : ?>
        <div id="print-job">
            <?php $url = $this -> url(array(
                'module' => 'ynjobposting',
                'controller' => 'jobs',
                'action' => 'print',
                'id' => $job->getIdentity()),'default', true)
            ;?>
            <a href="<?php echo $url?>">
                <i class="fa fa-print"></i>
                <?php echo $this->translate('Print')?>
            </a>
        </div>
        <?php endif; ?>
        <!-- report this job-->
        <?php if ($canReport) : ?>
        <div id="report">
        <?php
        $url = $this->url(array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $job->getGuid(),
            'format' => 'smoothbox'),'default', true);
        ?>
        <a href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $url?>')">
            <i class="fa fa-exclamation-triangle"></i>
            <?php echo $this->translate('Report') ?>
        </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    //apply job
    function applyJob() {
        url = '<?php echo $this->url(array('module' => 'ynjobposting', 'controller' => 'jobs', 'action' => 'apply', 'id' => $job->getIdentity()), 'ynjobposting_job', true)?>';
        window.location = url;
    }
    
    //check open popup
    function checkOpenPopup(url) {
        if(window.innerWidth <= 480) {
            Smoothbox.open(url, {autoResize : true, width: 300});
        }
        else {
            Smoothbox.open(url);
        }
    }
    
    //print job
    function printJob() {
        var allPage = document.body.innerHTML;
        var print = document.body.getElement('.layout_main .layout_middle');
        document.body.empty();
        document.body.innerHTML = print.innerHTML;
        window.print();
        document.body.innerHTML = allPage;
        
    }
    
    //edit job
    function editJob() {
        url = '<?php echo $this->url(array('module' => 'ynjobposting', 'controller' => 'jobs', 'action' => 'edit', 'id' => $job->getIdentity()), 'ynjobposting_job', true)?>';
        window.location = url;
    }
    
    //view applications
	<?php $company = $job -> getCompany();?>    
    function viewApplications() {
        url = '<?php echo $this->url(array('module' => 'ynjobposting', 'controller' => 'jobs', 'action' => 'applications', 'company_id' => $company->getIdentity(), 'id' => $job->getIdentity()), 'ynjobposting_job', true)?>';
        window.location = url;
    }
    
    //end job
    function endJob() {
        url = '<?php echo $this->url(array('module' => 'ynjobposting', 'controller' => 'jobs', 'action' => 'end', 'id' => $job->getIdentity()), 'ynjobposting_job', true)?>';
        Smoothbox.open(url);
    }
    
    //delete job
    function deleteJob() {
        url = '<?php echo $this->url(array('module' => 'ynjobposting', 'controller' => 'jobs', 'action' => 'delete', 'id' => $job->getIdentity()), 'ynjobposting_job', true)?>';
        Smoothbox.open(url);
    }
</script>