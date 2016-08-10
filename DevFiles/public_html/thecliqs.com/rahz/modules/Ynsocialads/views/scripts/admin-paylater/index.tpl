<script>
function submitConfirm() {
    $('submit_type').value = 'confirm';
    $('submit_btn').click();
}

function submitCancel() {
    $('submit_type').value = 'cancel';
    $('submit_btn').click();
}

function selectAll() {
    var i;
    var multiselect_form = $('multiselect_form');
    var inputs = multiselect_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled) {
            inputs[i].checked = inputs[0].checked;
        }
    }
}

function multiSelect() {
    if ($('submit_type').value == 'confirm') {
        return confirm("<?php echo $this->translate('Are you sure you want to confirm the selected Pay Later Request(s)?');?>");
    }
    else {
        return confirm("<?php echo $this->translate('Are you sure you want to cancel the selected Pay Later Request(s)?');?>");
    }
}
</script>
<h2>
    <?php echo $this->translate('YouNet Social Ads Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('YNSOCIALADS_MANAGE_PAY_LATER_TITLE') ?></h3>

<p>
	<?php echo $this->translate("YNSOCIALADS_MANAGE_PAY_LATER_DESCRIPTION") ?>
</p>

<br />

<?php if( count($this->paginator) ): ?>
<form id='multiselect_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiSelect()">
<table class='admin_table ynsocial_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th><?php echo $this->translate('Payment Method') ?></th>
      <th><?php echo $this->translate('Transaction ID') ?></th>
      <th><?php echo $this->translate('Start Date') ?></th>
      <th><?php echo $this->translate('Status') ?></th>
      <th><?php echo $this->translate('Amount') ?></th>
      <th><?php echo $this->translate('Ad') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><input type='checkbox' class='checkbox' name='select_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
        <td><?php echo $this->translate('Pay Later') ?></td>
        <td><?php echo $item->transaction_id ?></td>
        <td><?php echo $this->locale()->toDate($item->start_date) ?></td>
        <td><?php echo $item->status ?></td>
        <td><?php echo ($item->amount.' '.$item->currency)?></td>
        <td><?php echo $this->htmlLink($item->getAdHref(), $item->ad_id, array()) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<!--<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $curPage = $this->paginator->getCurrentPageNumber();
    $curItem = $this->paginator->getCurrentItemCount();
    $total = $this->paginator->getTotalItemCount();
    $itemPerPage = $this->paginator->getItemCountPerPage();
    echo (((($curPage-1)*$itemPerPage)+1).'-'.($curItem+(($curPage-1)*$itemPerPage)).' '.$this->translate('of').' '.$total.' '.$this->translate('Result(s)').'.');
    echo '</p>';
}?> -->
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo ($this->translate('Total').' '.$total.' '.$this->translate('result(s)'));
    echo '</p>';
}?>
<div class="buttons">
  <button type="button" onclick="submitConfirm()"><?php echo $this->translate("Confirm Selected") ?></button>
  <button type="button" onclick="submitCancel()"><?php echo $this->translate("Cancel Selected") ?></button>
  <button type="submit" id="submit_btn" style="display:none"></button>
</div>

<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
<input type="hidden" id="submit_type" name="submit_type" value="confirm" />
</form>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no Pay Later Requests.') ?>
    </span>
  </div>
<?php endif; ?>
