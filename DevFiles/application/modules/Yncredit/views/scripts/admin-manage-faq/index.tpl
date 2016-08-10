<h2><?php echo $this->translate("User Credits Plugin") ?></h2>
 <?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div>
	<a href="<?php echo $this->url(array('action'=>'create')) ?>"><?php echo $this->translate('+ Add FAQ'); ?></a>
</div>
<br />
<?php if( count($this->paginator) ): ?>    
	<table class="admin_table">
		<thead>
			<tr>
				<th width = "60%"><?php echo $this->translate("Question") ?></th>
				<th><?php echo $this->translate("Status") ?></th>
				<th><?php echo $this->translate("Order") ?></th>
				<th><?php echo $this->translate("Created") ?></th>
				<th><?php echo $this->translate("Options") ?></th>
			</tr>
		</thead> 
		<tbody>  
			<?php foreach($this->paginator as $item) :?>
			<tr>
				<td>
					<?php echo $item->question ?>
				</td>
				<td>
					<?php echo $this->translate(ucfirst($item->status)) ?>
				</td>
				<td>
					<?php echo $item->ordering ?>
				</td>
				<td>
					<?php echo $item->creation_date ?>
				</td>
				<td>
					<a href="<?php echo $this->url(array('action'=>'edit','id'=>$item->getIdentity())) ?>">
						<?php echo $this->translate("edit") ?>
					</a>
					 | 
					 <a href="<?php echo $this->url(array('action'=>'delete','id'=>$item->getIdentity())) ?>" class="smoothbox">
						<?php echo $this->translate("delete") ?>
					</a>
				</td>
			</tr>
			<?php endforeach; ?>   
		</tbody>
	</table>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("No faq has been added.") ?>
    </span>
  </div>
<?php endif; ?>
<br/>
 <!-- Page Paginator -->
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array());?>
</div>