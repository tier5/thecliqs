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
	
	function chk(){
		
		var checks = $$('td.checksub input[type=checkbox]');
		
		var flag = false;
		for (i = 0; i < checks.length; i++) {
			if (checks[i].checked == true) {				
					flag = true;
				}
			}
		if(flag == false){
			alert("<?php  echo $this->translate("Please choose at least a item");?>");
			return flag;
		}				
		return flag;
	}
</script>

<div class="mycontest_search clearfix">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<br />
<form id='multidelete_form' class="vertical_form" method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<div style="overflow: auto">  
<table class='mycontest_table'>
  <thead>
    <tr>    
    	<th align="center" class='admin_table_short'><input type='checkbox' class='checkbox' /></th>  
      	<th><?php echo $this->translate("ID");?></th>
      	<th><?php echo $this->translate("User");?></th>
      	<th><?php echo $this->translate("Full Name");?></th>
  		<th><?php echo $this->translate("Email Address");?></th>	
  		<th><?php echo $this->translate("Phone Number");?></th>	
      	<th><?php echo $this->translate("Gender");?></th>
      	<th><?php echo $this->translate("Location");?></th>
      	<th><?php echo $this->translate("Birthday");?></th>
      	<th><?php echo $this->translate("Participated Contest");?></th>
      	<th><?php echo $this->translate("Participated Date");?></th>
      	
      	<th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
  	<?php foreach ($this->paginator as $item): ?>
  		<?php
  			//echo "<pre>";
			//print_r($item);
			//echo "</pre>";die;
  		?>
  	<tr>		
  		<td align="center" class="checksub"><input <?php if($item->member_status == 'approved'):?> disabled="disabled" <?php endif;?> type='checkbox' class='checkbox' name='delete[]' value="<?php echo $item->member_id ?>"/></td>
        <td><?php echo $item->getIdentity();  ?></td> 
        <td><?php $user = Engine_Api::_()->getItem('user',$item->user_id);						
        			echo $user;
        	?></td>       
        <td><?php echo $item->full_name;  ?></td>
        <td><?php echo $item->email;  ?></td>
        <td><?php echo $item->phone;  ?></td>
        <td><?php if($item->sex==0) echo $this->translate("Male"); else echo $this->translate("Female");  ?></td>
        <td><?php echo $item->name;  ?></td>
        <td><?php echo $this->locale()->toDate( $item->birth, array('size' => 'short')) ;//echo $item->birth;  ?></td>
        <td><?php echo $item->contest_name;  ?></td>
        <td><?php echo $this->locale()->toDate( $item->approve_date, array('size' => 'short')) ;//echo $item->approve_date;  ?></td>
        <td>
        	<?php
        	if($item->member_status != 'banned'):
        		if($item->member_status == 'approved')
        			echo $this->translate("Approved"); 
				else
					echo $this->htmlLink(
                  		array('route' => 'default', 'module' => 'yncontest', 'controller' => 'my-members', 'action' => 'approve-member', 'id' => $item->member_id,'contest_id'=>$item->contest_id),
                  			$this->translate('Approved'),
                  			array('class' => 'smoothbox'));    		   
        	?>
        	|
        	<?php
        		 if($item->member_status == 'denied')
        			echo $this->translate("Deny"); 
				else
					echo $this->htmlLink(
                  		array('route' => 'default', 'module' => 'yncontest', 'controller' => 'my-members', 'action' => 'deny-member', 'id' => $item->member_id,'contest_id'=>$item->contest_id),
                  			$this->translate('Deny'),
                  			array('class' => 'smoothbox',                  			
							));         		   
        	?>
        	|
        	<?php
				endif;
        		 if($item->member_status == 'banned')
        			echo $this->translate("Ban"); 
				else
					
					//echo "<a href='javascript:checkban();'>".$this->translate('Ban')."</a>";
					echo $this->htmlLink(
                  		array('route' => 'default', 'module' => 'yncontest', 'controller' => 'my-members', 'action' => 'ban-member', 'id' => $item->member_id,'contest_id'=>$item->contest_id),
                  			$this->translate('Ban'),
                  			array(
                  				'class' => 'smoothbox', 
                  				'id'=> 'member_ban',								
							));       		   
        	?>
       
        </td>
    </tr>   
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<br />   

<br/>
 <button type='submit' name="btnapprove" onclick="return chk();">
    <?php echo $this->translate("Approve Selected") ?>
  </button>
  	</form> 
<div>

   <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
</div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no participants.") ?>
    </span>
  </div>
<?php endif; ?>
<style type="text/css">

</style> 