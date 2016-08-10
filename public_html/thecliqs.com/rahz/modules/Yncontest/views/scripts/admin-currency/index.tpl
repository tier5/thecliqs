<h2><?php echo $this->translate("Contest Plugin") ?></h2>
<!-- admin menu -->
<?php echo $this->content()->renderWidget('yncontest.admin-main-menu') ?>

<h3><?php echo $this->translate("Currencies") ?></h3>
<br />
<?php if(count($this->paginator)>0):?>
<div>
	<table class="yncontest_admin_table admin_table">
		<thead>
			<tr>
              <th style = "text-align: left;"><?php echo $this->translate("Name") ?></th>
              <th><?php echo $this->translate("Currency Code") ?></th>
              <th><?php echo $this->translate("Symbol") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
			</tr>
		</thead>
	<tbody>
		<?php foreach($this->paginator as $item): ?>
		<tr>
			<td><?php echo $item->name?></td>
			<td><?php echo $item->code?></td>
			<td><?php echo $item->symbol?></td>
			<td style = "text-align: center;">
				<?php echo $this->htmlLink(array('route'       => 'admin_default',
                                                                 'module'      => 'yncontest',
                                                                 'controller'  => 'currency',
                                                                 'action'      => 'edit-currency',
                                                                 'code_id'        => $item -> code),
                                                           $this->translate('Edit'),
                                                           array('class' => 'smoothbox',))
                                ?>
			</td>
		</tr>
		<?php endforeach; ?>	    			
	</tbody>
</table>
</div>
<br/>
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>
<?php else:?>
<br/>
<div class="tip">
     <span><?php echo $this->translate("There are currently no currencies.") ?></span>
</div>
<?php endif;?>
