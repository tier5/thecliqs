<script src="<?php $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/picker/Locale.en-US.DatePicker.js" type="text/javascript"></script> 
<script src="<?php $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/picker/Picker.js" type="text/javascript"></script> 
<script src="<?php $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/picker/Picker.Attach.js" type="text/javascript"></script> 
<script src="<?php $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/picker/Picker.Date.js" type="text/javascript"></script> 
<link href="<?php $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">

<script type="text/javascript">
	
	 window.addEvent('domready', function() {
        new Picker.Date($$('.date_picker'), { 
            positionOffset: {x: 5, y: 0}, 
            pickerClass: 'datepicker_dashboard', 
            useFadeInOut: !Browser.ie,
            onSelect: function(date){
            }
        });
    });
	
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
	
	function featureBusiness(obj, id) {
	    var value = (obj.checked) ? 1 : 0;
	    var url = en4.core.baseUrl+'admin/ynbusinesspages/businesses/feature';
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
	
	function neverExpireBusiness(obj, id) {
	    var value = (obj.checked) ? 1 : 0;
	    var url = en4.core.baseUrl+'admin/ynbusinesspages/businesses/never-expire';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'id': id,
	            'value': value
	        },
	        'onSuccess' : function(responseJSON, responseText)
	        {
	          location.reload();
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
	    $('multiselect').action = en4.core.baseUrl +'admin/ynbusinesspages/businesses/multiselected';
	    $('ids').value = selecteditems;
	    $('select_action').value = action;
	    $('multiselect').submit();
	}
</script>
<h2><?php echo $this->translate("YouNet Business Pages Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Businesses') ?></h3>

<br />
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="select_action" name="select_action" value=""/>
</form>	
<form id='multiselect_form' method="post" action="<?php echo $this->url();?>" style="overflow: auto">
	<table class='admin_table' style="width: 100%">
	  <thead>
	    <tr>
	      <th><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('business.business_id', 'ASC');"><?php echo $this->translate('ID') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('business.name', 'ASC');"><?php echo $this->translate('Business') ?></a></th>
	      <th><?php echo $this->translate('Category') ?></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user.displayname', 'ASC');"><?php echo $this->translate('Business Owner') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('business.status', 'ASC');"><?php echo $this->translate('Status') ?></a></th>
	      <th><?php echo $this->translate('Main Location') ?></th>
	      <th><?php echo $this->translate('Featured') ?></th>
	      <th><?php echo $this->translate('Never Expire') ?></th>
	      <th><?php echo $this->translate('Options') ?></th>
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
	        <td><?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?></td>
	        <?php $list_categories = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages') -> getCategoriesByBusinessId($item -> getIdentity());?>
	        <td>
	        	<?php $i = 1; foreach($list_categories as $category_row) :?>
	        		<?php $category = Engine_Api::_() -> getItem('ynbusinesspages_category', $category_row -> category_id); ?>
	        		<?php if($category) :?>
		        		<?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
		        		<?php if($i < count($list_categories)) :?>
		        			|
		        		<?php endif;?>
	        		<?php endif;?>
	        	<?php $i++; endforeach;?>
	        </td>
	        <?php $user = Engine_Api::_() -> getItem('user', $item -> user_id); ?>
	        <?php if($item -> is_claimed) :?>
	        		<td><?php echo $this->translate('Unknown')?></td>
	        <?php else:?>
		        <?php if($user -> getIdentity() > 0): ?>
		       	 	<td><?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?></td>
		        <?php else:?>
		        	<td><?php echo $this->translate('Unknown')?></td>	
		        <?php endif;?>
	        <?php endif;?>
	        <td class="status"><?php echo $item->status ?></td>
	        <td><?php echo $item->getMainLocation()?></td>
	        <td>
        	<?php if($item -> status == 'published') :?>
	        	<input type="checkbox" class="feature_checkbox" value="1" onclick="featureBusiness(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->featured) echo 'checked'?>/>
    		<?php endif;?>
        	</td>
        	<td>
        	<?php if(in_array($item -> status, array('published','expired'))) :?>
	      	<input type="checkbox" class="never-expire_checkbox" onclick="neverExpireBusiness(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->never_expire) echo 'checked disabled'?>/>
	      	<?php endif;?>
	      	</td>
	      	
	      	<td>
      		<?php if($item -> status != 'deleted') :?>
	      		<!-- View -->
	      		<?php if ($item->isViewable()) : ?>
	      		<?php echo $this->htmlLink(
                array('route' => 'ynbusinesspages_profile', 'id' => $item->getIdentity(), 'slug' => $item -> getSlug()), 
               		  $this->translate('View'), 
               		   array('class' => '')) ?>
	      		<?php endif;?>
	      		<!-- Edit -->
	      		<?php if ($item->isEditable()) : ?>
	      		|	
	      		<?php echo $this->htmlLink(
                array('route' => 'ynbusinesspages_specific', 'action' => 'edit', 'business_id' => $item->getIdentity(), 'admin' => '1'), 
               		  $this->translate('Edit'), 
               		   array('class' => '')) ?>
                <?php endif;?>
                <!-- Delete -->
                <?php if ($item->isDeletable()) : ?>
                |	
                <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynbusinesspages', 'controller' => 'businesses', 'action' => 'delete', 'business_id' => $item->getIdentity()), 
               		  $this->translate('Delete'), 
               		   array('class' => 'smoothbox')) ?>
               	<?php endif;?>
           		<!-- View Statistic --> 
               	<?php if(in_array($item -> status, array('published', 'expired'))) :?>	  
                |
               	<?php echo $this->htmlLink(
                array('route' => 'ynbusinesspages_dashboard', 'business_id' => $item->getIdentity()), 
               		  $this->translate('View Statistic'), 
               		   array('class' => '')) ?>
                <?php endif;?>
                <!-- Transfer Owner -->
                <?php if(!in_array($item -> status, array('draft', 'expired', 'denied', 'deleted'))) :?>	
                |
               	<?php echo $this->htmlLink(
                array('route' => 'ynbusinesspages_specific', 'action' => 'transfer', 'business_id' => $item->getIdentity(), 'admin' => 1), 
               		  $this->translate('Transfer Owner'), 
               		   array('class' => 'smoothbox')) ?>	    
               	<?php endif;?>
               	
               	<!-- Expire now -->
                <?php if(in_array($item -> status, array('published', 'closed'))) :?>	
                |
               	<?php echo $this->htmlLink(
                 array('route' => 'admin_default', 'module' => 'ynbusinesspages', 'controller' => 'businesses', 'action' => 'expire-now', 'business_id' => $item->getIdentity()), 
               		  $this->translate('Expire Now'), 
               		   array('class' => 'smoothbox')) ?>	    
               	<?php endif;?>
            <?php endif;?>   		     
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
	<button type='button' onclick="multiSelected('Approve')"><?php echo $this->translate('Approve Selected') ?></button>
	<button type='button' onclick="multiSelected('Deny')"><?php echo $this->translate('Deny Selected') ?></button>
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
      <?php echo $this->translate('There are no businesses.') ?>
    </span>
  </div>
<?php endif; ?>
