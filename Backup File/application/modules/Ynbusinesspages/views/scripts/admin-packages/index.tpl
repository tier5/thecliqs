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
	    $('multiselect').action = en4.core.baseUrl +'admin/ynbusinesspages/packages/multiselected';
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

<h3><?php echo $this->translate('Manage Packages') ?></h3>
<br />
<div class="add_link">
<?php echo $this->htmlLink(
array('route' => 'admin_default', 'module' => 'ynbusinesspages', 'controller' => 'packages', 'action' => 'create'),
$this->translate('Add Package'), 
array(
    'class' => 'buttonlink add_faq',
)) ?>
</div>

<?php if( count($this->paginator) ): ?>
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>	
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="select_action" name="select_action" value=""/>
</form>	
<form id='multiselect_form' method="post" action="<?php echo $this->url();?>">
	 <table style="position: relative; width: 100%" class='admin_table'>
	  <thead>
	    <tr>
	      <th><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Package Name') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('price', 'ASC');"><?php echo $this->translate('Package Price') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('valid_amount', 'ASC');"><?php echo $this->translate('Valid Period') ?></a></th>
	      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('show', 'ASC');"><?php echo $this->translate('Show') ?></a></th>
	      <th><?php echo $this->translate('Options') ?></th>
	    </tr>
	  </thead>
	  <tbody id='demo-list'>
	    <?php foreach ($this->paginator as $item): ?>
	      <tr id='package_item_<?php echo $item->getIdentity() ?>'>
	      	<td>
	       		<input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>"/>
	        </td>
	        <td><?php echo $item->title ?></td>
	        <td><?php echo $this -> locale()->toCurrency($item->price, $this->currency) ?></td>
	        <?php if ($item->valid_amount == 0) :?>
	        <td><?php echo $this->translate('Never expire');?></td>
	        <?php else:?>
	        <?php 
	        	$str_one = '%s '. $item -> valid_period;
				$str_more = '%s '. $item -> valid_period.'s';
	        ?>
	        <td><?php echo $this->translate(array($str_one, $str_more, $item -> valid_amount), $item -> valid_amount);?></td>
	        <?php endif;?>
	        <td><?php if($item -> show == true) echo $this->translate('Show'); else echo $this->translate('Hide'); ?></td>
	      	<td>
	      		<?php 
	            echo $this->htmlLink(
			            array('route' => 'admin_default', 
			                'module' => 'ynbusinesspages',
				            'controller' => 'packages' ,
				            'action' => 'edit', 
				            'id' => $item->getIdentity()), 
				            $this->translate('Edit'), 
			            array('class' => ''));
		   		 ?>
		   		 |
		   		 <?php 
		            echo $this->htmlLink(
			            array('route' => 'admin_default', 
			                'module' => 'ynbusinesspages',
				            'controller' => 'packages' ,
				            'action' => 'delete',
				            'id' => $item->getIdentity()), 
				            $this->translate('Delete'), 
			            array('class' => 'smoothbox'));
		   		 ?>
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
      <?php echo $this->translate('There are no packages.') ?>
    </span>
  </div>
<?php endif; ?>
<script type="text/javascript">
en4.core.runonce.add(function(){

    new Sortables('demo-list', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('controller'=>'packages','action'=>'sort'), 'admin_default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'order': this.serialize().toString(),
          }
        }).send();
      }
    });
    
});

</script>
