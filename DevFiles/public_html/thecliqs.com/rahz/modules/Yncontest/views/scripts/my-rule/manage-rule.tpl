<?php echo $this->content()->renderWidget('yncontest.main-menu') ?>
<div class="contestcreate_layout_right">
<?php echo $this->partial('_contest_menu.tpl', array(
		'contest'=>$this->contest		
		));?>
</div>
<?php echo $this->form->render($this);?>

<?php 
// echo $this->htmlLink(
// 	                  array('route' => 'yncontest_myrule', 'action' => 'create-rule', 'contest' => $this->contest),
// 	                  $this->translate('Add new rule'),
// 	                  array('class' => 'smoothbox')) 
?>

<h3 class = "yncontest_list_heading"><?php echo $this->translate("List of Rules") ?></h3>

<script type="text/javascript">
	function optionRule(rule_id,checbox){
	    
	   // if(checbox.checked==true) status =1;
	   // else status =0;
	  //  new Request.JSON({
	  //    'format': 'json',
	  //    'url' : '<?php //echo $this->url(array('module' => 'yncontest', 'controller' => 'my-contest', 'action' => $type), 'yncontest_mycontest') ?>',
	  //    'data' : {
	   //     'format' : 'json',
	   //     'socialstore' : rule_id,
	   //     'good' : status
	   //   }
	  //  }).send();
	    
	}
	function getSelectRule(id,contest_id){		
		window.location.href = en4.core.baseUrl+'contest/my-rule/manage-rule/contest/'+contest_id+'/rule/'+id; 
		
	}
	
	
  </script>
  <script type="text/javascript">
  var cal_start_date_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_end_date.calendars[0].start = new Date( $('start_date-date').value );
    // redraw calendar
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', 1);
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', -1);
  }
  var cal_end_date_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_start_date.calendars[0].end = new Date( $('end_date-date').value );
    // redraw calendar
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', 1);
    cal_start_date.navigate(cal_start_date.calendars[0], 'm', -1);
  }
</script>
  
  <?php if( count($this->paginator) ): ?>
<table class='admin_table'>
  <thead>
    <tr>
     
      <th><?php echo $this->translate("Rule") ?></th>
      <th><?php echo $this->translate("Description") ?></th> 
      <th><?php echo $this->translate("Start Date") ?></th> 
      <th><?php echo $this->translate("End Date") ?></th>
    
      <th><?php echo $this->translate("Allow to Submit") ?></th>
      <th><?php echo $this->translate("Allow to Vote") ?></th>
      <th><?php echo $this->translate("Actions") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
	      <td class="ynContest_table_alignLeft"><?php echo $item->rule_name; ?></td> 
	      <td class="ynContest_table_alignLeft"><?php echo $item->description; ?></td> 
	      <td><?php echo date('Y-m-d H:i:s',strtotime($item->start_date));  ?></td> 
	      <td><?php echo date('Y-m-d H:i:s',strtotime($item->end_date)); ?></td>  
	     
	      <td class="ynContest_table_alignCenter">
	         <?php if($item->submitentries == 1): ?>
	       	 	<input type="checkbox" id='submitentries_<?php echo $item->rule_id; ?>' disabled="disabled"  onclick="optionRule(<?php echo $item->rule_id; ?>,this)" checked />
	         <?php else: ?>
	            <input type="checkbox" id='submitentries_<?php echo $item->rule_id; ?>' disabled="disabled"  onclick="optionRule(<?php echo $item->rule_id; ?>,this)" />
	         <?php endif; ?> 
	      </td>   
	      <td class="ynContest_table_alignCenter">
	         <?php if($item->voteentries == 1): ?>
	       	 	<input type="checkbox" id='voteentries_<?php echo $item->rule_id; ?>' disabled="disabled"  onclick="optionRule(<?php echo $item->rule_id; ?>,this)" checked />
	         <?php else: ?>
	            <input type="checkbox" id='voteentries_<?php echo $item->rule_id; ?>' disabled="disabled"  onclick="optionRule(<?php echo $item->rule_id; ?>,this)" />
	         <?php endif; ?> 
	      </td> 
	      <td>
	          <?php echo $this->htmlLink(
	                  array('route' => 'yncontest_myrule', 'action' => 'edit-rule','contest'=>$this->contest, 'rule' => $item->rule_id),
	                  $this->translate('edit'),
	                  array('class' => 'smoothbox')) ?>
	          |
	          <?php echo $this->htmlLink(
	                  array('route' => 'yncontest_myrule', 'action' => 'delete-rule','contest'=>$this->contest, 'rule' => $item->rule_id),
	                  $this->translate('delete'),
	                  array('class' => 'smoothbox')) ?>
	          
	      </td>    
        
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>
<form class="global_form" method="" action="<?php echo $this->url(array('action' => 'manage-award', 'contest'=>$this->contest), 'yncontest_myaward', true)?>" enctype="multipart/form-data">
<div class='buttons'>
	<button type="submit" id="submit" >Continue</button>
</div>

</form>

<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("Rule has not created yet.") ?>
    </span>
  </div>
<?php endif; ?>