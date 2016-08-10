<script>   
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
<?php if( count($this->paginator) ): ?>
<form id='multidelete_form' class="vertical_form" method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<div>  
<table class='mycontest_table'>
  <thead>
    <tr>    
			<th><?php echo $this->translate("ID");?></th>
			<th><?php echo $this->translate("Contest Name");?></th>
			<th><?php echo $this->translate("Contest Type");?></th>
			<th><?php echo $this->translate("End Date");?></th>
			<th><?php echo $this->translate("Participants");?></th>
			<th><?php echo $this->translate("Organizers");?></th>     
			<th><?php echo $this->translate("Action");?></th> 	
    </tr>
  </thead>
  <tbody>
  	<?php foreach ($this->paginator as $item): ?>
  	<tr>		
        <td><?php echo $item->getIdentity();  ?></td>
     	<td><?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?></td>
     	<td><?php echo Engine_Api::_()->yncontest()->arrPlugins[$item->contest_type]; ?></td>
     	<td><?php echo $this->locale()->toDate( $item->end_date, array('size' => 'short')); ?></td>
     	<td>
        	<?php        	
					if($item->participants=="") echo "0"; else echo $item->participants;                  			 		   
        	?>       	
       
        </td>
        <td>
        	<?php
					if($item->organizers=="") echo "0"; else echo $item->organizers;  		   
        	?>       	
       
        </td>  
        <td>
        	  <?php        	
					echo $this->htmlLink(
                  		array('route' => 'yncontest_members' ,'action' => 'participate', 'contest_id' => $item->contest_id),
                  			"Participants",
                  			array());    		   
        	?>   
        	|
        	<?php
					echo $this->htmlLink(
                  		array('route' => 'yncontest_members', 'action' => 'organizer', 'contest_id' => $item->contest_id),
                  			"Organizers",
                  			array());    		   
        	?>       	
        </td>      
    </tr>   
    <?php endforeach; ?>
  </tbody>
</table>
</div>  
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