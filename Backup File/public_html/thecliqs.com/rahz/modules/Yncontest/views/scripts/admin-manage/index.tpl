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
	function contest_activated(contest_id){
            var element = document.getElementById('yncontest_activated_'+contest_id);
            var checkbox = document.getElementById('activatedcontest_'+contest_id);
            var status = 0;

            if(checkbox.checked==true) status = 1;
            else status = 0;
            var content = element.innerHTML;
            new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('module' => 'yncontest', 'controller' => 'manage', 'action' => 'activated'), 'admin_default') ?>',
              'data' : {
                'format' : 'json',
                'contest_id' : contest_id,
                'status' : status
              },
              'onRequest' : function(){
                  element.innerHTML= "<img style='margin-top:4px;' src='application/modules/Yncontest/externals/images/loading.gif'></img>";
              },
              'onSuccess' : function(responseJSON, responseText)
              {
                element.innerHTML = content;
                checkbox = document.getElementById('activatedcontest_'+contest_id);
                if( status == 1) checkbox.checked=true;
                else checkbox.checked=false;
              }
            }).send();

    }
	
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
      return confirm("<?php echo $this->translate('Are you sure you want to delete the selected contest?');?>");
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

<div class="ynContest_adminSearch admin_search">
	<div class="search">
		<?php echo $this->form->render($this);?>
	</div>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<form class="ynContest_adminForm" id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<div>  
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short' align="center"><input type='checkbox' class='checkbox' /></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('contest_id', 'DESC');"><?php echo $this->translate("ID");?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('contest_name', 'DESC');"><?php echo $this->translate("Title") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');"><?php echo $this->translate("Start date") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('end_date', 'DESC');"><?php echo $this->translate("End date") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('contest_type', 'DESC');"><?php echo $this->translate("Type") ?></a></th>
      
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('approve_status', 'DESC');"><?php echo $this->translate("Approve") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('contest_status', 'DESC');"><?php echo $this->translate("Status") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('participants', 'DESC');"><?php echo $this->translate("Members") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('entries', 'DESC');"><?php echo $this->translate("Entries") ?></a></th>
      <th><?php echo $this->translate("Winning") ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('activated', 'DESC')"><?php echo $this->translate("Activated") ?></a></th>
      <th style = "padding: 0 31px;"><?php echo $this->translate("Service") ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('paidFee', 'DESC');"><?php echo $this->translate("Paid Fee") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'DESC');"><?php echo $this->translate("Creator") ?></a></th>
           
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td class="checksub" align="center">
        	<input type='checkbox' class='checkbox' name='delete_<?php echo $item->contest_id; ?>' value="<?php echo $item->contest_id ?>"/>
        </td>
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $this->HtmlLink($item->getHref(),$item->contest_name); ?></td>
        <td><?php echo $this->locale()->toDate( $item->start_date); ?></td>
        <td><?php echo $this->locale()->toDate( $item->end_date); ?></td>
        <td><?php echo Engine_Api::_()->yncontest()->arrPlugins[$item->contest_type]; ?></td>
        <td>
          <?php 
          if( $item->approve_status=='pending'){
          	echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'yncontest', 'controller' => 'admin-manage', 'action' => 'deny', 'id' => $item->contest_id),
                  $this->translate('Deny'),
                  array('class' => 'smoothbox')); ?>
          |
          <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'yncontest', 'controller' => 'admin-manage', 'action' => 'approve', 'id' => $item->contest_id),
                  $this->translate('Approve'),
                  array('class' => 'smoothbox'));
		   }
		   elseif($item->approve_status == 'approved'){
		   		echo "Approved";       
		   } 
		   elseif($item->approve_status == 'denied'){
		   		echo "Denied";   
		   }
		   else{
				//This is "New Contest" ;
			}
		   ?>
        </td>
        <td><?php echo $item->contest_status; ?></td>
        <td><?php echo $item->participants ?></td>
        <td><?php echo $item->entries ?></td>       
        
        <td><?php echo $item->entries ?></td> 
        <td>
          <div id='yncontest_activated_<?php echo $item->getIdentity(); ?>'>
              <?php
               if($item->activated): ?>
                <input type="checkbox" id='activatedcontest_<?php echo $item->getIdentity(); ?>' onclick="contest_activated(<?php echo $item->getIdentity(); ?>,this)" checked />
              <?php else: ?>
                <input type="checkbox" id='activatedcontest_<?php echo $item->getIdentity(); ?>' onclick="contest_activated(<?php echo $item->getIdentity(); ?>,this)" />
              <?php endif; ?>
          </div>
        </td>
        <td width="100px">
        	<div id='yncontest_content_<?php echo $item->contest_id.'_2'; ?>' style ="text-align: left;" ><input type="checkbox" id='yncontest_<?php echo $item->contest_id.'_2'; ?>' onclick="myservice(<?php echo $item->contest_id; ?>,2,this)" <?php if($item->featured_id==1) echo "checked";?>  />Feature</div>    
        	<div id='yncontest_content_<?php echo $item->contest_id.'_3'; ?>' style ="text-align: left;" ><input type="checkbox" id='yncontest_<?php echo $item->contest_id.'_3'; ?>' onclick="myservice(<?php echo $item->contest_id; ?>,3,this)" <?php if($item->premium_id==1) echo "checked"; ?>  />Premium</div> 
           	<div id='yncontest_content_<?php echo $item->contest_id.'_4'; ?>' style ="text-align: left;" ><input type="checkbox" id='yncontest_<?php echo $item->contest_id.'_4'; ?>' onclick="myservice(<?php echo $item->contest_id; ?>,4,this)" <?php if($item->endingsoon_id==1) echo "checked"; ?> />Ending Soon</div>     	
        </td>
        
        <td><?php 
        		$fee = ($item->paidFee == "") ? '0.00' : $item->paidFee;
        		echo Engine_Api::_()->yncontest()->getCurrencySymbol().$fee;  ?></td>
        <td><?php echo $item->getOwner(); ?></td>
        
                      
        <td>
          <?php echo $this->htmlLink($item->getHref(), $this->translate('View')) ?>
          |
          <?php echo $this->htmlLink(array('route' => 'yncontest_mycontest', 'action' => 'edit-contest', 'contest' => $item->contest_id),$this->translate('Edit')); ?>
          |
          <?php echo $this->htmlLink(
                  array('route' => 'default', 'module' => 'yncontest', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->contest_id),
                  $this->translate('Delete'),
                  array('class' => 'smoothbox')) ?>
         <?php      
          	 if($item->contest_status == 'draft' && $item->approve_status == 'new'):
         		if($item->checkPublish()):   
         			echo " | ";
         			echo $this->htmlLink(
       					array('route' => 'yncontest_mycontest', 'action' => 'publish-admin', 'contest' => $item->contest_id, 'view'=>1),
       					$this->translate('Publish'),array('class' => 'smoothbox'));        
   			 	endif;         
			endif;
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
      <?php echo $this->translate("There are no contest.") ?>
    </span>
  </div>
<?php endif; ?>
