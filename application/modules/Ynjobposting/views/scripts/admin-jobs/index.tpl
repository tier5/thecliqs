<script type="text/javascript">

function selectAll() {
    var hasElement = false;
    var i;
    var multiselect_form = $('multiselect_form');
    var inputs = multiselect_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled && inputs[i].hasClass('multiselect_checkbox')) {
            inputs[i].checked = inputs[0].checked;
        }
    }
}

function multiSelected(action){
    var checkboxes = $$('td input.multiselect_checkbox[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('multiselect').action = en4.core.baseUrl +'admin/ynjobposting/jobs/multiselected';
    $('ids').value = selecteditems;
    $('select_action').value = action;
    $('multiselect').submit();
}

function featureJob(obj, id) {
    var value = (obj.checked) ? 1 : 0;
    var url = en4.core.baseUrl+'admin/ynjobposting/jobs/feature';
    new Request.JSON({
        url: url,
        method: 'post',
        data: {
            'id': id,
            'value': value
        }
    }).send();
}

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
<h2>
    <?php echo $this->translate('YouNet Job Posting Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Jobs') ?></h3>

<p><?php echo $this->translate("YNJOBPOSTING_MANAGE_JOBS_DESCRIPTION") ?></p>

<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="select_action" name="select_action" value=""/>
</form>
<form id='multiselect_form' class="yn_admin_form" method="post" action="<?php echo $this->url();?>">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('job.title', 'ASC');"><?php echo $this->translate("Job Title") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('company.name', 'ASC');"><?php echo $this->translate("Company Name") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('industry.title', 'ASC');"><?php echo $this->translate("Industry") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('job.status', 'ASC');"><?php echo $this->translate("Status") ?></a></th>
      <th><?php echo $this->translate("Featured") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>" <?php if ($item->isDeleted()) echo 'disabled'?>/></td>
        <td><?php echo $this->htmlLink($item->getHref(), $item->getTitle())?></td>
        <?php $company = $item->getCompany();?>
        <td><?php echo ($company) ? $this->htmlLink($company->getHref(), $company->getTitle()) : 'Unknown Company';?></td>
        <?php $industry = $item->getIndustry();?>
        <td><?php echo ($industry) ? $industry->getTitle() : 'Unknown Industry';?></td>
        <td><?php echo ucfirst($this->translate($item->status)) ?></td>
        <td><input type="checkbox" class="featured_checkbox" value="1" onclick="featureJob(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->isFeatured()) echo 'checked'?> <?php if (!$item->isPublished()) echo 'disabled'?>/></td>
        <td>
        <?php if (!$item->isDeleted()) : ?>
            <?php if ($item->isEditable()) : ?>
            <?php echo $this->htmlLink(
            array('route' => 'ynjobposting_job', 'action' => 'edit', 'id' => $item->getIdentity()), 
            $this->translate('edit'), 
            array()) ?>
            <?php if ($item->isDeletable()) : ?>
             | 
            <?php endif; ?>
            <?php endif; ?>
            <?php if ($item->isDeletable()) : ?>
            <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'ynjobposting', 'controller' => 'jobs', 'action' => 'delete', 'id' => $item->getIdentity()), 
            $this->translate('delete'), 
            array('class' => 'smoothbox')) ?>
            <?php endif; ?>
        <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</form>

<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
    echo '</p>';
}?>

<div class='buttons'>
    <button type='button' onclick="multiSelected('approve')"><?php echo $this->translate('Approve Selected') ?></button>
    <button type='button' onclick="multiSelected('deny')"><?php echo $this->translate('Deny Selected') ?></button>
    <button type='button' onclick="multiSelected('end')"><?php echo $this->translate('End Selected') ?></button>
    <button type='button' onclick="multiSelected('delete')"><?php echo $this->translate('Delete Selected') ?></button>
</div>

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
