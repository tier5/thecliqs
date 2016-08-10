<script type="text/javascript">
    function selectAll() {
        var i;
        var inputs = $$('input[type=checkbox]');
        for (i = 1; i < inputs.length; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
    
    function deleteSelected(){
        var checkboxes = $$('input.checkbox[type=checkbox]');
        var selecteditems = [];
        checkboxes.each(function(item){
          var checked = item.checked;
          var value = item.value;
          if (checked == true){
            selecteditems.push(value);
          }
        });
        $('multidelete').action = en4.core.baseUrl +'ynjobposting/jobs/multi-delete-my-jobs';
        $('ids').value = selecteditems;
        $('multidelete').submit();
    }
</script>
<div id="mamage-job-form">
    <form action="<?php echo $this->url(array('action' => 'manage'), 'ynjobposting_job', true)?>">
        <select name = "mode" onchange="this.form.submit();">
            <option value="applied" <?php if ($this->mode == 'applied') echo 'selected';?>><?php echo $this->translate('My Applied Jobs')?></option>
            <option value="saved" <?php if ($this->mode == 'saved') echo 'selected';?>><?php echo $this->translate('My Saved Jobs')?></option>
            <option value="posted" <?php if ($this->mode == 'posted') echo 'selected';?>><?php echo $this->translate('My Posted Jobs')?></option>
        </select>
    </form>
</div>

<?php if (count($this->paginator) > 0) : ?>
<h4>
<?php
	    $total = $this->paginator->getTotalItemCount();
	    echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
?>
</h4>

<form id='multidelete' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" name="mode" value="<?php echo $this->mode?>"/>
</form>

<div id="ynjobposting_list_my_jobs" class="ynjobposting-table">
    <div class="ynjobposting-table-header">
        <div><input id="check_all" onclick='selectAll();' type='checkbox' /></div>
        <div><?php echo $this->translate('Job Title') ?></div>
        <div><?php echo $this->translate('Company') ?></div>
        <?php if ($this->mode != 'posted') :?>
        <div><?php echo $this->translate('Industry') ?></div>
        <div><?php echo $this->translate('Location') ?></div>
        <?php endif;?>
        <div><?php echo $this->translate('Expired Date') ?></div>
        <?php if ($this->mode == 'applied') : ?>
        <div><?php echo $this->translate('Applied Date') ?></div>
        <?php elseif ($this->mode == 'posted'): ?>
        <div><?php echo $this->translate('Posted Date') ?></div>
        <div><?php echo $this->translate('Status') ?></div>
        <?php endif; ?>
        <?php if ($this->mode != 'applied') :?>
        <div><?php echo $this->translate('Options') ?></div>
        <?php endif; ?>
    </div>
    <div class="ynjobposting-table-listings">
        <?php foreach ($this->paginator as $job): ?>
        <div class="ynjobposting-jobmanage-item">
            <div><input type='checkbox' class='checkbox' name='delete_<?php echo $job->getIdentity(); ?>' value="<?php echo $job->getIdentity(); ?>" /></div>
            <div><?php echo $this->htmlLink($job->getHref(), $job->getTitle()); ?></div>
            <?php $company = $job->getCompany(); ?>
            <div><?php echo ($company) ? $this->htmlLink($company->getHref(), $company->getTitle()) : $this->translate('Unknown Company');?></div>
            <?php if ($this->mode != 'posted') :?>
            <?php $industry = $job->getIndustry();?>
            <div><?php echo ($industry) ? $industry->getTitle() : $this->translate('Unknown Industry');?></div>
            <div><?php echo $job->working_place ?></div>
            <?php endif;?>
            <?php if (!is_null($job->expiration_date)) : ?>
            <?php $expired_date = Engine_Api::_()->ynjobposting()->convertToUserTimezone($job->expiration_date)?>
            <div><?php if ($expired_date) echo $this->locale()->toDate($expired_date) ?></div>
            <?php else :?>
                <div><?php echo '-' ?></div>
            <?php endif;?>
            <?php if ($this->mode == 'applied') : ?>
            <?php $applied_date = Engine_Api::_()->ynjobposting()->convertToUserTimezone($job->applied_date)?>
            <div><?php echo $this->locale()->toDate($applied_date) ?></div>
            <?php elseif ($this->mode == 'saved') : ?>
            <div><?php echo $this->htmlLink(array(
                'route' => 'ynjobposting_job',
                'action' => 'apply',
                'id' => $job->getIdentity()
            ),
            $this->translate('Apply now'),
            array())?></div>
            <?php elseif ($this->mode == 'posted'): ?>
            <?php $posted_date = Engine_Api::_()->ynjobposting()->convertToUserTimezone($job->creation_date)?>
            <div><?php echo $this->locale()->toDate($posted_date) ?></div>
            <div><?php echo ucfirst($this->translate($job->status)) ?></div>
            <div>
            <?php if ($job->isDeletable()) : ?>
                <?php echo $this->htmlLink(
                array('route' => 'ynjobposting_job', 'action' => 'delete', 'id' => $job->getIdentity()), 
                $this->translate('Delete'), 
                array('class' => 'smoothbox')) ?>
            <?php endif; ?>
                <?php if ($job->isEditable() && ($job -> status != 'expired' && $job -> status != 'pending')) : ?>
                     | 
                    <?php echo $this->htmlLink(
                    array('route' => 'ynjobposting_job', 'action' => 'edit', 'id' => $job->getIdentity()), 
                    $this->translate('Edit'), 
                    array()) ?>
                <?php endif; ?>
                
                <?php if ($job->isEndable() && $job -> status == 'published') : ?>
                     | 
                    <?php echo $this->htmlLink(
                    array('route' => 'ynjobposting_job', 'action' => 'end', 'id' => $job->getIdentity()), 
                    $this->translate('End'), 
                    array('class' => 'smoothbox')) ?>
                <?php endif; ?>
                
                <?php if ($job->isEndable() && $job -> status == 'ended') : ?>
                     | 
                    <?php echo $this->htmlLink(
                    array('route' => 'ynjobposting_job', 'action' => 're-publish', 'id' => $job->getIdentity()), 
                    $this->translate('Re-publish'), 
                    array('class' => 'smoothbox')) ?>
                <?php endif; ?>
                
                <?php if ($job->isEditable() && $job -> status == 'expired') : ?>
                     | 
                    <?php echo $this->htmlLink(
                    array('route' => 'ynjobposting_job', 'action' => 'edit', 'id' => $job->getIdentity()), 
                    $this->translate('Renew'), 
                    array()) ?>
                <?php endif; ?>
                
                <?php if ((int)$job->candidate_count > 0) : ?>
                     | 
                    <?php
                    echo $this->htmlLink(
                    array('route' => 'ynjobposting_job', 'action' => 'applications', 'company_id' => $job->company_id ,'id' => $job->getIdentity()), 
                    $this->translate('View Applications'), 
                    array()) 
                    ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class='buttons'>
  <button type='button' onclick="deleteSelected()"><?php echo $this->translate('Delete Selected') ?></button>
</div>

<br/>
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no Jobs.") ?>
    </span>
  </div>
<?php endif; ?>
