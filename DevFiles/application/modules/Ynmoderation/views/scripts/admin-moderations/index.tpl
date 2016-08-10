<style>
div.admin_search {
	margin-bottom: 10px;
}
table.admin_table {
	margin-bottom: 10px;
	margin-top: 10px;
}
</style>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    $$('th.admin_table_short input[type=checkbox]').addEvent('click', function(event) {
      var el = $(event.target);
      $$('input[type=checkbox]').set('checked', el.get('checked'));
    });
  });
</script>

<h2>
  <?php echo $this->translate('Moderation Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      /*---- Render the menu ----*/
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    
  </div>
</div>
<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<?php if( count($this->paginator) ): ?>
		<form id='ynmoderation_form' method="post" action="<?php echo $this->url();?>" onSubmit="return confirm('<?php echo $this->translate("Are you sure you want to delete the selected items?"); ?>');">
		<button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
		<button type='button' onclick="window.location=window.location;"><?php echo $this->translate("Refesh Data") ?></button>
		<table class='admin_table'>
		  <thead>
		    <tr>
		      <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
		      <th><?php echo $this->translate("Module Name") ?></th>
		      <th><?php echo $this->translate("Title") ?></th>
		      <th><?php echo $this->translate("Creator") ?></th>
		      <th><?php echo $this->translate("Creation Date") ?></th>
		      <th><?php echo $this->translate("Action") ?></th>
		    </tr>
		  </thead>
		  
		  <tbody>
		    <?php foreach ($this->paginator as $item): ?>
		      <tr>
		        <td><input type='checkbox' class='checkbox' name='<?php echo $item['type']; ?>[]' value="<?php echo $item['id']; ?>" /></td>
		        <td><?php echo $item['module_name'] ?></td>
		        <td><?php echo $item['title'] ?></td>
		        <td><?php echo ($item['creator']) ? Engine_Api::_()->user()->getUser($item['creator']) : "" ?></td>
		        <td><?php echo $item['creation_date'] ?></td>
		        <td>
		          <?php echo $this->htmlLink(
		          		array('route' => 'default', 'module' => 'ynmoderation', 'controller' => 'admin-moderations', 'action' => 'view','type' => $item['type'], 'id' => $item['id']),
		          		$this->translate('view content')) ?>
		          |
		          <?php echo $this->htmlLink(
		                array('route' => 'default', 'module' => 'ynmoderation', 'controller' => 'admin-moderations', 'action' => 'delete','type' => $item['type'], 'id' => $item['id']),
		                $this->translate("delete"),
		                array('class' => 'smoothbox')) ?>
		        </td>
		      </tr>
		    <?php endforeach; ?>
		  </tbody>
		</table>
		<button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
		<button type='button' onclick="window.location=window.location;"><?php echo $this->translate("Refesh Data") ?></button>
		</form>
		
		<br/>
		<div>
		  <?php echo $this->paginationControl($this->paginator); ?>
		</div>
		
		<?php else: ?>
		  <div class="tip">
		    <span>
		      <?php echo $this->translate("There are no content yet.") ?>
		    </span>
		  </div>
		<?php endif; ?>