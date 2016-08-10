<?php if( count($this->navigation) ): ?>
	<div class='tabs'>
		<?php
	  // Render the menu
		echo $this->navigation()->menu()->setContainer($this->navigation)->render()
		?>
	</div>
<?php endif; ?>
<br/>
<p style="margin-top: 10px">
	<?php echo $this->translate("YNSOCIALADS_MY_ACCOUNT_DESCRIPTION") ?>
</p>
<br/>

<?php if ($this->remaining >= $this->min_amount) { ?>
<div class="add_link">
	<?php echo $this->htmlLink(
		array(
			'route' => 'ynsocialads_account',
			'module' => 'ynsocialads',
			'controller' => 'account',
			'action' => 'add-request',
			)
		, $this->translate('Add Request'), 
		array(
			'class' => 'smoothbox buttonlink add_placement'
			)) ?>
</div>
<?php } ?>

<div class="yn_filter frontend_filter">
	<?php echo $this->form->render($this);?>
</div>
		
<?php if( count($this->paginator) ): ?>
	<div class="fixed-scrolling">
		<table class='ynsocial_table frontend_table'>
			<tr>
				<th><?php echo $this->translate('Request Date') ?></th>
				<th><?php echo $this->translate('Status') ?></th>
				<th><?php echo $this->translate('Amount') ?></th>
				<th><?php echo $this->translate('Request Message') ?></th>
				<th><?php echo $this->translate('Reponse Date') ?></th>
				<th><?php echo $this->translate('Reponse Message') ?></th>
			</tr>
			<?php foreach ($this->paginator as $item): ?>
				<tr>
					<td><?php echo $this->locale()->toDate($item->request_date) ?></td>
					<td><?php echo $item->status ?></td>
					<td><?php echo $this->locale() -> toCurrency($item->amount, $item->currency) ?></td>
					<td><?php echo $item->request_message ?></td>
					<td><?php echo $this->locale()->toDate($item->response_date) ?></td>
					<td><?php echo $item->response_message ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>

	<?php if (count($this->paginator)) {
				echo '<p class=result_count>';
				$total = $this->paginator->getTotalItemCount();
				echo ($this->translate('Total').' '.$total.' '.$this->translate('result(s)'));
				echo '</p>';
	}?>
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
			<?php echo $this->translate('You don\'t have any money requests.') ?>
		</span>
	</div>
<?php endif; ?>
<script type="text/javascript">
	$$('.ynsocialads_main_account').getParent().addClass('active');
	$$('.core_main_ynsocialads').getParent().addClass('active');
</script>