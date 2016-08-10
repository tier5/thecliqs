
<script type="text/javascript">
	en4.core.runonce.add(function(){
		$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ 
			$$('td.checksub input[type=checkbox]').each(function(i){
	 			i.checked = $$('th.admin_table_short input[type=checkbox]')[0].checked;
			});
		});
		$$('td.checksub input[type=checkbox]').addEvent('click', function(){
			var checks = $$('td.checksub input[type=checkbox]');
			var flag = true;
			for (i = 0; i < checks.length; i++) {
				if (checks[i].checked == false) {
					flag = false;
				}
			}
			if (flag) {
				$$('th.admin_table_short input[type=checkbox]')[0].checked = true;
			}
			else {
				$$('th.admin_table_short input[type=checkbox]')[0].checked = false;
			}
		});
	});
	
	
    
    function multiDelete()
    {
      return confirm("<?php echo $this->translate('Are you sure you want to delete the selected entry?');?>");
    }
    
   function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['orderby'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      // Just change direction
      if( listby == currentOrder ) {
        $('direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('orderby').value = listby;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }  
    
    
    
</script>
<h2>
  <?php echo $this->translate('Contest Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>

<?php if( count($this->paginator)>0 ): ?>


<br />
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<div style="overflow: visible">  
<table class='yncontest_admin_table admin_table'>
  <thead>
    <tr>    
      <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>  
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('entry_id', 'DESC');"><?php echo $this->translate("ID");?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('entry_name', 'DESC');"><?php echo $this->translate("Title") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');"><?php echo $this->translate("Submitted date") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('modified_date', 'DESC');"><?php echo $this->translate("Approved date") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('entry_type', 'DESC');"><?php echo $this->translate("Type") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('entry_status', 'DESC');"><?php echo $this->translate("Status") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('contest_id', 'DESC');"><?php echo $this->translate("Contest") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'DESC');"><?php echo $this->translate("Views") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'DESC');"><?php echo $this->translate("Like") ?></a></th>
      
      <th><?php echo $this->translate("Award") ?></th> 
      <th><?php echo $this->translate("Actions") ?></th>

    </tr>
  </thead>
  <tbody>
  <?php $viewer = Engine_Api::_()->user()->getViewer(); ;?>
    <?php foreach ($this->paginator as $item): 
   
    ?>
      <tr>  
      	<td class="checksub">
        	<input type='checkbox' class='checkbox' name='delete[]' value="<?php echo $item->entry_id ?>"/>
        </td>  		
        <td><?php echo $item->getIdentity();  ?></td>
        <td><?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?></td>
        <td><?php echo $this->locale()->toDate( $item->start_date, array('size' => 'short')); ?></td>
        <td><?php echo $this->locale()->toDate( $item->modified_date, array('size' => 'short')); ?></td>
        <td><?php echo $this->arrPlugins[$item->entry_type]; ?></td>       
        <td><?php echo $item->entry_status ?></td>
       
        <td><?php 
	        $contest = Engine_Api::_()->getItemTable('contest')->find($item->contest_id)->current();       
	        echo $this->htmlLink($contest->getHref(), $contest->getTitle());         
	    	?>
    	</td>       
      
        <td><?php echo $item->view_count ?></td> 
        <td><?php echo $item->like_count ?></td> 
       	<td>
          <div class = "award_type">
          <?php 
            if($item->award_id != 0){
              $award = Engine_Api::_()->getItemTable('yncontest_awards')->find($item->award_id)->current();       
              echo $award->getTitle(); 
              echo '<span>' . $award->award_name . '</span>';
            }        
            else echo $this->translate('N/A');
          ?>
        </div>
    	</td>    
               
                      
        <td>          
           <?php echo $this->htmlLink(
					  array('route' => 'yncontest_myentries', 'action' => 'delete', 'id' => $item->entry_id),
					  $this->translate('Delete'),
					  array('class' => 'smoothbox')) ?>			
			|
			<?php


echo $this->htmlLink(array(
		'route' => 'yncontest_myentries',
		'action' => 'edit',
		'id' => $item->entry_id,
		'format' => 'smoothbox'),
		$this->translate('Edit'),
		array('class' => 'smoothbox'));
?>
			
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<br />   
<div class='buttons'>
  <button type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
</div> 
</form> 
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
      <?php echo $this->translate("There are no entries.") ?>
    </span>
  </div>
<?php endif; ?>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
  height: 65px;
}
table.admin_table thead tr th {
  padding: 7px;
}
</style> 