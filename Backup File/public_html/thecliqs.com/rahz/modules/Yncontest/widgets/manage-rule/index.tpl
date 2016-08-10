<?php echo $this->htmlLink(
			array(			 
			'route' => 'yncontest_rules',
			'action' => 'create',
			), 
			$this->translate('+ Add new Rule'), 
			array('class' => 'smoothbox yncontest_add_link',)
		  ) ;

if( count($this->paginator) ): ?>
<form id='multidelete_form' class="vertical_form" method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<div>  
<table class='mycontest_table'>
  <thead>
    <tr>      	
      	<th><?php echo $this->translate("Rule Title");?></th>      	
      	<th><?php echo $this->translate("Actions") ?></th>
    </tr>
  </thead>
  <tbody>
  	<?php foreach ($this->paginator as $item): ?>
  		<?php
  			//echo "<pre>";
			//print_r($item);
			//echo "</pre>";
  		?>
  	<tr>		  		
             
        <td><?php echo $item->rule_name;  ?></td>
     
        <td>
        	<?php        		
					echo $this->htmlLink(
						array(			 
						'route' => 'yncontest_rules', 	
						'action' => 'edit',
						'rule_id' =>$item->managerule_id,
						), 
						$this->translate('Edit'), 
						array('class' => 'smoothbox',)
					  ) ;  		   
        	?>
        	|
        	<?php
        		 
					echo $this->htmlLink(
						array(			 
						'route' => 'yncontest_rules', 	
						'action' => 'delete',
						'rule_id' =>$item->managerule_id,
						), 
						$this->translate('Delete'), 
						array('class' => 'smoothbox',)
					  ) ;  	       		   
        	?>
        	
       
        </td>
    </tr>   
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<br />   
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
      <?php echo $this->translate("There are no rules.") ?>
    </span>
  </div>
<?php endif; ?>