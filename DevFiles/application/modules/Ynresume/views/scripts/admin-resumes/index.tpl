<script type="text/javascript">
	
	function featureResume(obj, id) {
	    var value = (obj.checked) ? 1 : 0;
	    var url = en4.core.baseUrl+'admin/ynresume/resumes/feature';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'id': id,
	            'value': value
	        },
	        'onSuccess' : function(responseJSON, responseText)
	        {
	        }
	    }).send();
	}
	
	function featureSelected(){
	    var checkboxes = $$('td input.multiselect_checkbox[type=checkbox]');
	    var selecteditems = [];
	    checkboxes.each(function(item){
	      var checked = item.checked;
	      var value = item.value;
	      if (checked == true && value != 'on'){
	        selecteditems.push(value);
	      }
	    });
	    $('multiselect').action = en4.core.baseUrl +'admin/ynresume/resumes/featureselected';
	    $('ids').value = selecteditems;
	    $('multiselect').submit();
	}
	
	function serviceSelected(){
	    var checkboxes = $$('td input.multiselect_checkbox[type=checkbox]');
	    var selecteditems = [];
	    checkboxes.each(function(item){
	      var checked = item.checked;
	      var value = item.value;
	      if (checked == true && value != 'on'){
	        selecteditems.push(value);
	      }
	    });
	    $('multiselect').action = en4.core.baseUrl +'admin/ynresume/resumes/serviceselected';
	    $('ids').value = selecteditems;
	    $('multiselect').submit();
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
	    $('multiselect').action = en4.core.baseUrl +'admin/ynresume/resumes/multiselected';
	    $('ids').value = selecteditems;
	    $('select_action').value = action;
	    $('multiselect').submit();
	}
</script>
<h2><?php echo $this->translate("YouNet Resume Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Resumes') ?></h3>

<p>
  <?php echo $this->translate("YNRESUME_ADMINMANAGE_RESUMES_DESCRIPTION") ?>
</p>

<br />
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="select_action" name="select_action" value=""/>
</form>	
<form id='multiselect_form' style="overflow: auto;" method="post" action="<?php echo $this->url();?>">
	<table class='admin_table' style="width: 100%">
	  <thead>
	    <tr>
	      <th><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('resume.name', 'DESC');"><?php echo $this->translate('Resumes') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('resume.headline', 'DESC');"><?php echo $this->translate('Professional Headline') ?></a></th>
	      <th><?php echo $this->translate('"Who Viewed Me" Expiration Date') ?></th>
	      <th><?php echo $this->translate('Featured Resumes') ?></th>
	      <th><?php echo $this->translate('Actions') ?></th>
	    </tr>
	  </thead>
	  <tbody>
	    <?php foreach ($this->paginator as $item): ?>
	    <tr>
	    	<td><input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>"/></td>
	    	<td><?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?></td>
	    	<td><?php echo $item->headline; ?></td>
	    	<?php
	    		$serviceDateObject = null;
				if (!is_null($item->service_expiration_date) && !empty($item->service_expiration_date) && $item->service_expiration_date) 
				{
					$serviceDateObject = new Zend_Date(strtotime($item->service_expiration_date));	
				}
	    	?>
	    	<td><?php echo (!is_null($serviceDateObject)) ? date('M d Y', $serviceDateObject -> getTimestamp())  : ''; ?></td>
	    	<td><input type="checkbox" class="feature_checkbox" value="1" onclick="featureResume(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->featured) echo 'checked'?>/></td>
	    	<td>
           		<!-- delete button -->
	    		<?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'resumes', 'action' => 'delete',  'id' => $item->getIdentity()), 
               		  $this->translate('Delete'), 
           		   array('class' => 'smoothbox')) ?>
           		|
           		<!-- update service button -->
	    		<?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'resumes', 'action' => 'serviceselected',  'ids' => $item->getIdentity()), 
               		  $this->translate('Update Service'), 
           		   array('class' => '')) ?>
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
    <button type='button' onclick="multiSelected('Delete')"><?php echo $this->translate('Delete Selected') ?></button>
    <button type='button' onclick="featureSelected('Feature')"><?php echo $this->translate('Feature Selected') ?></button>
    <button type='button' onclick="serviceSelected('Service')"><?php echo $this->translate('Update Service Selected') ?></button>
</div>
<br/>
<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no resumes.') ?>
    </span>
  </div>
<?php endif; ?>
