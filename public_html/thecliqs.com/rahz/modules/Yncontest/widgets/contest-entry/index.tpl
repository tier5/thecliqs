
<div class="mycontest_search clearfix">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator)>0 ): ?>
<br />
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<div style="overflow: visible">  
<table class='mycontest_table'>
  <thead>
    <tr>      
      <th><?php echo $this->translate("ID");?></th>
      <th><?php echo $this->translate("Title") ?></th>
      <th><?php echo $this->translate("Submitted date") ?></th>
      <th><?php echo $this->translate("Approved date") ?></th>
      <th><?php echo $this->translate("Type") ?></th>
      <th><?php echo $this->translate("Status") ?></th>     
      <th><?php echo $this->translate("Likes") ?></th>
       <th><?php echo $this->translate("Votes") ?></th>
      <th><?php echo $this->translate("Comments") ?></th>
      <th><?php echo $this->translate("Actions") ?></th>

    </tr>
  </thead>
  <tbody>
  <?php $viewer = Engine_Api::_()->user()->getViewer(); ;?>
    <?php foreach ($this->paginator as $item): 
   
    ?>
      <tr>    		
        <td><?php echo $item->getIdentity();  ?></td>
        <td><?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?></td>
        <td><?php echo $this->locale()->toDate( $item->start_date, array('size' => 'short')); ?></td>
        <td><?php echo $this->locale()->toDate( $item->modified_date, array('size' => 'short')); ?></td>
        <td><?php echo $this->arrPlugins[$item->entry_type]; ?></td>       
        <td><?php echo $item->entry_status ?></td>
        <td><?php echo $item->like_count ?></td>     
        <td><?php echo $item->vote_count ?></td> 
        <td><?php echo $item->comment_count ?></td> 
          
                      
        <td>          
          <?php if($item->entry_status == 'draft'):?>			 
			  <?php echo $this->htmlLink(
					  array('route' => 'yncontest_myentries', 'action' => 'publish', 'id' => $item->contest_id),
					  $this->translate('Publish')) ?>
					   |
				 <?php echo $this->htmlLink(
					  array('route' => 'yncontest_myentries', 'action' => 'edit', 'id' => $item->contest_id),
					  $this->translate('Edit')) ?>
					 
			<?php elseif($item->entry_status == 'published'):?>
			
			 <?php echo $this->htmlLink(
					  array('route' => 'yncontest_myentries', 'action' => 'delete', 'contestId' => $item->contest_id),
					  $this->translate('Delete'),
					  array('class' => 'smoothbox')) ?>			
					 

					  
			<?php endif; ?>
			
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
      <?php echo $this->translate("There are no entries.") ?>
    </span>
  </div>
<?php endif; ?>
<style type="text/css">

</style> 