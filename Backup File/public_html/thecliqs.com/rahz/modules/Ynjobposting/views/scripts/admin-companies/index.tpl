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
	    console.log(currentOrderDirection);
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
	
	function sponsorCompany(obj, id) {
	    var value = (obj.checked) ? 1 : 0;
	    var url = en4.core.baseUrl+'admin/ynjobposting/companies/sponsor';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'id': id,
	            'value': value
	        },
	        'onSuccess' : function(responseJSON, responseText)
	        {
	          alert(responseJSON.message);
	        }
	    }).send();
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
	    $('multiselect').action = en4.core.baseUrl +'admin/ynjobposting/companies/multiselected';
	    $('ids').value = selecteditems;
	    $('select_action').value = action;
	    $('multiselect').submit();
	}
</script>
<h2><?php echo $this->translate("Job Posting Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Companies') ?></h3>

<br />
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="select_action" name="select_action" value=""/>
</form>	
<form id='multiselect_form' method="post" action="<?php echo $this->url();?>">
	<table class='admin_table' style="width: 100%">
	  <thead>
	    <tr>
	      <th><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('company.company_id', 'ASC');"><?php echo $this->translate('ID') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('company.name', 'ASC');"><?php echo $this->translate('Company Name') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user.displayname', 'ASC');"><?php echo $this->translate('Company Creator') ?></a></th>
	      <th><?php echo $this->translate('Industry') ?></th>
	      <th><?php echo $this->translate('Status') ?></th>
	      <th><?php echo $this->translate('Sponsored') ?></th>
	    </tr>
	  </thead>
	  <tbody>
	    <?php foreach ($this->paginator as $item): ?>
	      <tr>
	      	<td>
	      	<?php if($item -> status != 'deleted') :?>
	       		<input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>"/>
	        <?php endif;?>
	        </td>
	        <td><?php echo $item -> getIdentity();?></td>
	        <td><a href='<?php echo $item->getHref() ?>'><?php echo $item->name ?></a></td>
	        <?php $user = Engine_Api::_() -> getItem('user', $item -> user_id); ?>
	        <?php if($user): ?>
	       	 <td><a href='<?php echo $user -> getHref() ?>'><?php echo $user -> getTitle(); ?></a></td>
	        <?php else:?>
	        	<td><?php echo $this->translate('Unknown')?></td>	
	        <?php endif;?>
	        <?php $list_industries = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting') -> getIndustriesByCompanyId($item -> getIdentity());?>
	        <td>
	        	<?php $i = 1; foreach($list_industries as $industry_row) :?>
	        		<?php $industry = Engine_Api::_() -> getItem('ynjobposting_industry', $industry_row -> industry_id); ?>
	        		<?php if($industry) :?>
		        		<a href='<?php echo $industry -> getHref(); ?>'><?php echo $industry -> title; ?></a>
		        		<?php if($i < count($list_industries)) :?>
		        			|
		        		<?php endif;?>
	        		<?php endif;?>
	        	<?php $i++; endforeach;?>
	        </td>
	        <td><?php echo $item->status ?></td>
	        <td><input type="checkbox" class="sponsor_checkbox" value="1" onclick="sponsorCompany(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->checkSponsor()) echo 'checked'?> <?php if (!$item->isSponsorable()) echo 'disabled'?>/></td>
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
	<button type='button' onclick="multiSelected('Publish')"><?php echo $this->translate('Publish Selected') ?></button>
	<button type='button' onclick="multiSelected('Close')"><?php echo $this->translate('Close Selected') ?></button>
    <button type='button' onclick="multiSelected('Delete')"><?php echo $this->translate('Delete Selected') ?></button>
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
      <?php echo $this->translate('There are no companies.') ?>
    </span>
  </div>
<?php endif; ?>
