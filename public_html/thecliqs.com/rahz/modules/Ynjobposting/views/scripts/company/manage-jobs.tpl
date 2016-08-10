<script type="text/javascript">
function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
    // Just change direction
    if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } 
    else {
        $('order').value = listby;
        $('direction').value = default_direction;
    }
    $('filter_form').submit();
}
</script>

<div class="ynjobposting-breadcrumb-list">
    <h3><a href="<?php echo $this->company->getHref();?>"><?php echo $this->company->name;?></a> <span>&#187; <?php echo $this->translate("My Posted Jobs");?></span></h3>
</div>

<div class="ynjobposting-clearfix" style="margin-bottom: 10px;">
    <?php echo $this->form->render($this);?>
</div>

<?php if( count($this->paginator) ): ?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="select_action" name="select_action" value=""/>
</form>
<form id='multiselect_form' class="yn_admin_form" method="post" action="<?php echo $this->url();?>">
<div id="ynjobposting_list_applications" class="ynjobposting-table">
    <div class="ynjobposting-table-header">
        <div><a href="javascript:void(0);" onclick="javascript:changeOrder('job.title', 'ASC');"><?php echo $this->translate("Job Title") ?></a></div>
        <div><a href="javascript:void(0);" onclick="javascript:changeOrder('job.creation_date', 'ASC');"><?php echo $this->translate("Posted Date") ?></a></div>
        <div><a href="javascript:void(0);" onclick="javascript:changeOrder('job.expiration_date', 'ASC');"><?php echo $this->translate("Expired Date") ?></a></div>
        <div><a href="javascript:void(0);" onclick="javascript:changeOrder('job.status', 'ASC');"><?php echo $this->translate("Status") ?></a></div>
        <div><?php echo $this->translate("Option");?></div>
    </div>

    <div class="ynjobposting-table-listings">
    <?php foreach ($this->paginator as $item): ?>
    <?php 
        	$creationDateObj = new Zend_Date(strtotime($item->creation_date));
        	$expirationDateObj = null;
        	if (!is_null($item->expiration_date) && !empty($item->expiration_date) && $item->expiration_date) 
        	{
        		$expirationDateObj = new Zend_Date(strtotime($item->expiration_date));	
        	}
        	if( $this->viewer() && $this->viewer()->getIdentity() ) {
				$tz = $this->viewer()->timezone;
				$creationDateObj->setTimezone($tz);
				if (!is_null($expirationDateObj))
				{
					$expirationDateObj->setTimezone($tz);
				}
	        }
    ?>
    <div class="ynjobposting-manage-jobs-item">
        <div><?php echo $this->htmlLink($item->getHref(), $item->getTitle())?></div>
        <div><?php echo $this->locale()->toDate($creationDateObj) ?></div>
        <div><?php echo (!is_null($expirationDateObj)) ? $this->locale()->toDate($expirationDateObj): ''; ?></div>
        <div><?php echo ucfirst($this->translate($item->status)) ?></div>
        <div>
        	<?php if ($item->isDeletable()) : ?>
	            <?php echo $this->htmlLink(
	            array('route' => 'ynjobposting_job', 'action' => 'delete', 'id' => $item->getIdentity()), 
	            $this->translate('Delete'), 
	            array('class' => 'smoothbox')) ?>
            <?php endif; ?>
            <?php if ($item->isEditable() && ($item -> status != 'expired' && $item -> status != 'pending')) : ?>
            	 | 
	            <?php echo $this->htmlLink(
	            array('route' => 'ynjobposting_job', 'action' => 'edit', 'id' => $item->getIdentity()), 
	            $this->translate('Edit'), 
	            array()) ?>
            <?php endif; ?>
            
            <?php if ($item->isEndable() && $item -> status == 'published') : ?>
            	 | 
	            <?php echo $this->htmlLink(
	            array('route' => 'ynjobposting_job', 'action' => 'end', 'id' => $item->getIdentity()), 
	            $this->translate('End'), 
	            array('class' => 'smoothbox')) ?>
            <?php endif; ?>
            
            <?php if ($item->isEndable() && $item -> status == 'ended') : ?>
            	 | 
	            <?php echo $this->htmlLink(
	            array('route' => 'ynjobposting_job', 'action' => 're-publish', 'id' => $item->getIdentity()), 
	            $this->translate('Re-publish'), 
	            array('class' => 'smoothbox')) ?>
            <?php endif; ?>
            
            <?php if ($item->isEditable() && $item -> status == 'expired') : ?>
            	 | 
	            <?php echo $this->htmlLink(
	            array('route' => 'ynjobposting_job', 'action' => 'edit', 'id' => $item->getIdentity()), 
	            $this->translate('Renew'), 
	            array()) ?>
            <?php endif; ?>
            
            <?php if ((int)$item->candidate_count > 0) : ?>
            	 | 
	            <?php
	            echo $this->htmlLink(
	            array('route' => 'ynjobposting_job', 'action' => 'applications', 'company_id' => $item->company_id ,'id' => $item->getIdentity()), 
	            $this->translate('View Applications'), 
	            array()) 
	            ?>
            <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
</div>
</form>

<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s job', 'Total %s jobs', $total),$total);
    echo '</p>';
}?>

<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no Jobs.') ?>
    </span>
  </div>
<?php endif; ?>
