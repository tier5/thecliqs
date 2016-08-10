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
	
	function myservice(contest_id,type,obj){
    	
            var element = document.getElementById('yncontest_content_'+contest_id+'_'+type);
            var checkbox = document.getElementById('yncontest_'+contest_id+'_'+type);
                        
            var status = 0;
            
            if(obj.checked==true) status = 1;
            else status = 0;
            var content = element.innerHTML;
            element.innerHTML= "<img style='margin-top:4px;' src='application/modules/Yncontest/externals/images/loading.gif'></img>";
            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('module' => 'yncontest', 'controller' => 'manage', 'action' => 'service'), 'admin_default') ?>',
              'data' : {
                'format' : 'json',
                'contest_id' : contest_id,
                'type': type,
                'status' : status
              },
              'onRequest' : function(){
              },
              'onSuccess' : function(responseJSON, responseText)
              {
                element.innerHTML = content;
                checkbox = document.getElementById('yncontest_'+contest_id+'_'+type);
                if( status == 1) checkbox.checked=true;
                else checkbox.checked=false;
              }
            }).send();
            
    }  
    
    function multiDelete()
    {
      return confirm("<?php echo $this->translate('Are you sure you want to delete the selected pages?');?>");
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
    
    var denySelected =function(){
    var checkboxes = $$('td.checksub input[type=checkbox]');
    var selecteditems = [];
    $$("td.checksub input[type=checkbox]:checked").each(function(i)
	{
    	selecteditems.push(i.value);
	});
    $('ids').value = selecteditems;
    $('deny_selected').submit();
  }
  var approveSelected =function(){
    var checkboxes = $$('td.checksub input[type=checkbox]');
    var selecteditems = [];
    $$("td.checksub input[type=checkbox]:checked").each(function(i){
    	selecteditems.push(i.value);
    });

    $('ids1').value = selecteditems;
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

<div class="admin_search" style="display: none;">
    <?php echo $this->form->render($this);?>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>">
<div style="overflow: auto">  
<table class='admin_table'>
  <thead>
    <tr>
      <!--<th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>-->
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('contest_id', 'DESC');"><?php echo $this->translate("ID");?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('contest_name', 'DESC');"><?php echo $this->translate("Title") ?></a></th>
      <th><?php echo $this->translate("Owner") ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');"><?php echo $this->translate("Start date") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('end_date', 'DESC');"><?php echo $this->translate("End date") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('contest_type', 'DESC');"><?php echo $this->translate("Type") ?></a></th>
      
      
      <th><?php echo $this->translate("Service") ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('serviceFee', 'DESC');"><?php echo $this->translate("Paid Fee") ?></a></th>
      
           
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <!--<td class="checksub"><input type='checkbox' class='checkbox' name='delete_<?php //echo $item->contest_id; ?>' value="<?php //echo $item->contest_id ?>"/></td>-->
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $this->HtmlLink($item->getHref(),$item->contest_name); ?></td>
        <td><?php echo $item->getOwner(); ?></td>
        <td><?php echo $this->locale()->toDate( $item->start_date, array('size' => 'short')); ?></td>
        <td><?php echo $this->locale()->toDate( $item->end_date, array('size' => 'short')); ?></td>
        <td><?php echo Engine_Api::_()->yncontest()->arrPlugins[$item->contest_type]; ?></td>
       
        
        <td width="100px">
        	<div id='yncontest_content_<?php echo $item->contest_id.'_2'; ?>' style ="text-align: left;" >
               <input type="checkbox" disabled="disabled" id='yncontest_<?php echo $item->contest_id.'_2'; ?>'  <?php if(Engine_Api::_()->yncontest()->checkLiveService($item->contest_id,2)==true) echo "checked";?>  /> Feature              
           </div>    
           <div id='yncontest_content_<?php echo $item->contest_id.'_3'; ?>' style ="text-align: left;" >
               <input type="checkbox" disabled="disabled" id='yncontest_<?php echo $item->contest_id.'_3'; ?>'  <?php if(Engine_Api::_()->yncontest()->checkLiveService($item->contest_id,3)==true) echo "checked"; ?>  /> Premium              
           </div> 
           <div id='yncontest_content_<?php echo $item->contest_id.'_4'; ?>' style ="text-align: left;" >
               <input type="checkbox" disabled="disabled" id='yncontest_<?php echo $item->contest_id.'_4'; ?>'  <?php if(Engine_Api::_()->yncontest()->checkLiveService($item->contest_id,4)==true) echo "checked"; ?> /> Ending Soon            
           </div>     	
        </td>
        <td><?php echo Engine_Api::_()->yncontest()->getCurrencySymbol().$item->serviceFee;  ?></td>
               
                      
        <td>
          
          <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'yncontest', 'controller' => 'admin-service', 'action' => 'deny', 'id' => $item->contest_id),
                  $this->translate('Deny'),
                  array('class' => 'smoothbox')) ?>
          |
          <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'yncontest', 'controller' => 'admin-service', 'action' => 'approve', 'id' => $item->contest_id),
                  $this->translate('Approve'),
                  array('class' => 'smoothbox')) ?>        
         
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<br />  
<input type="hidden" id="orderby" value="" name="orderby">
<input id="direction" type="hidden" value="" name="direction"> 
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
      <?php echo $this->translate("There are no contest.") ?>
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
</style> 