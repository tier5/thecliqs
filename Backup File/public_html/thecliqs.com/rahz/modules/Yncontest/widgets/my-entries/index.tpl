<script type="text/javascript">
	en4.core.runonce.add(function(){
		$$('div.admin_table_short input[type=checkbox]').addEvent('click', function(){ 
			$$('div.checksub input[type=checkbox]').each(function(i){
	 			i.checked = $$('div.admin_table_short input[type=checkbox]')[0].checked;
			});
		});
		$$('div.checksub input[type=checkbox]').addEvent('click', function(){
			var checks = $$('div.checksub input[type=checkbox]');
			var flag = true;
			for (i = 0; i < checks.length; i++) {
				if (checks[i].checked == false) {
					flag = false;
				}
			}
			if (flag) {
				$$('div.admin_table_short input[type=checkbox]')[0].checked = true;
			}
			else {
				$$('div.admin_table_short input[type=checkbox]')[0].checked = false;
			}
		});
	});
    
    function multiDelete()
    {
      return confirm("<?php echo $this->translate('Are you sure you want to hide the selected entries?');?>");
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
<div class="mycontest_search clearfix">
    <?php echo $this->form->render($this);?>
</div>




<?php 

if( count($this->paginator)>0 ): ?>
	<form id='multidelete_form' method="post" action="<?php //echo $this->url();?>" onSubmit="return multiDelete()">
		<div class='admin_table_short'><input type='checkbox' class='checkbox' /></div>  	
		<div id= "ynContest_entries_type">
		<ul id="ynContest_entries_listing" class="ynContest_listCompare thumbs">
			<?php foreach ($this->paginator as $entry): ?>
			<li style="width:<?php echo $this->arrTemp[$entry->entry_type]['width']?>px; height: <?php echo $this->arrTemp[$entry->entry_type]['height']?>px;">
					
					<?php echo $this->partial('_formItem.tpl','yncontest' ,		
							array(									
									'item' => $entry,
									'my_entries' => true,
								)) 
					?> 
					
			</li>
			<?php endforeach;?>	
		</ul>
	</div>	
		<div class='buttons'>
		  <button type='submit'>
			<?php echo $this->translate("Hide Selected") ?>
		  </button>
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
      <?php echo $this->translate("There are no entries.") ?>
    </span>
  </div>
<?php endif; ?> 