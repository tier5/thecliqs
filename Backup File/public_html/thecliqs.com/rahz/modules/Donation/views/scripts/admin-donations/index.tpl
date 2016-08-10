<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';

  var changeOrder = function (order, default_direction) {
    if (order == currentOrder) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

  function multiModify() {
    return confirm('<?php echo $this->string()->
      escapeJavascript($this->translate("Are you sure you want to delete the selected donations?")) ?>');
  }
  function selectAll() {
    var i;
    var multimodify_form = $('multimodify_form');
    var inputs = multimodify_form.elements;
    for (i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }

  function confirmDelete(donation_id) {
    if (confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this donation?")) ?>')) {
      window.location.href = '<?php echo $this->url(array('module' => 'donation', 'controller' => 'donations', 'action' => 'delete'),
        'admin_default', true); ?>/donation_id/' + donation_id;
    } else {
      return false;
    }
  }

</script>


<h2><?php echo $this->translate("Donation Plugin") ?></h2>
<?php if (count($this->navigation)): ?>
<div class='donation_admin_tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<br/>

<div class='admin_search'>
  <?php echo $this->filterForm->render($this); ?>
</div>


<div class='admin_results'>
  <div>
    <?php
    echo $this->translate(array("%s donation found.", "%s donations found.", $count = $this->paginator->getTotalItemCount()), $this->locale()->toNumber($count))
    ?>
  </div>

  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
    'params' => $this->formValues
  )); ?>
  </div>
</div>
<br/>

<form id='multimodify_form' method="post" action="<?php echo $this->url(array('action' => 'multi-modify')); ?>"
      onSubmit="return multiModify()">
  <table class='admin_table page_packages'>
    <thead>
    <tr>
      <th>
        <input onclick="selectAll()" type='checkbox' class='checkbox'>
      </th>
      <th class='admin_table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('donation_id', 'DESC');">
          <?php echo $this->translate("ID") ?>
        </a>
      </th>
      <th>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');">
          <?php echo $this->translate("DONATION_title") ?>
        </a>
      </th>

      <th>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');">
          <?php echo $this->translate("DONATION_owner") ?>
        </a>
      </th>

      <th class="admin_table_centered">
        <a href="javascript:void(0);" onclick="javascript:changeOrder('target_sum', 'ASC');">
          <?php echo $this->translate("DONATION_target_sum") ?>
        </a>
      </th>

      <th class="admin_table_centered">
        <a href="javascript:void(0);" onclick="javascript:changeOrder('raised_sum', 'ASC');">
          <?php echo $this->translate("DONATION_raised") ?>
        </a>
      </th>

      <th class="center">
        <a href="javascript:void(0);" onclick="javascript:changeOrder('expiry_date', 'DESC');">
          <?php echo $this->translate("DONATION_expiry_date") ?>
        </a>
      </th>

      <th class="center">
        <a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');">
          <?php echo $this->translate("DONATION_date") ?>
        </a>
      </th>

      <th class='center admin_table_options'>
        <?php echo $this->translate("DONATION_Status") ?>
      </th>

      <th class='center admin_table_options'>
        <?php echo $this->translate("DONATION_options") ?>
      </th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($this->paginator)): ?>
      <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td>
          <input <?php //name='modify_<?php echo $item->getIdentity();?>
            name="modify[]"
            value=<?php echo $item->getIdentity(); ?>
              type='checkbox' class='checkbox'>
        </td>

        <td>
          <?php echo $item->donation_id ?>
        </td>
        <?php
        /**
         * @var $item Donation_Model_Donation
         */
        ?>
        <td class='admin_table_bold'>
          <?php
          echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->getTitle(), 15), array('target' => '_blank'))
          ?>
        </td>

        <td class='admin_table_owner'>
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target' => '_blank'))?>
        </td>

        <td class="center">
          <?php echo $this->locale()->toCurrency((double)$item->target_sum, $this->currency) ?>
        </td>

        <td class="center">
          <?php echo $this->locale()->toCurrency((double)$item->raised_sum, $this->currency) ?>
        </td>

        <td class="center">
          <?php echo $item->expiry_date ?>
        </td>

        <td class="center">
          <?php echo $item->creation_date ?>
        </td>

        <td>
          <?php if ($item->status == 'active') :?>
            <img title="<?php $this->translate('Active') ?>" src="application/modules/Donation/externals/images/active.png">
            <?php echo $this->translate("Active"); ?>
          <?php elseif($item->status == 'initial') : ?>
            <img title="<?php $this->translate('Initialization') ?>" src="application/modules/Donation/externals/images/initial.png">
            <?php echo $this->translate("Initialization"); ?>
          <?php else :?>
            <img title="<?php $this->translate('Complete') ?>" src="application/modules/Donation/externals/images/complete.png">
            <?php echo $this->translate("Complete"); ?>
          <?php endif?>
        </td>

        <td class='center admin_table_options'>
          <?php
              //approved
            echo $this->htmlLink(
                array(
                  'route' => 'admin_default',
                  'module' => 'donation',
                  'controller' => 'donations',
                  'action' => 'approve',
                  'donation_id' => $item->donation_id,
                  'value' => 1-$item->approved
                ),
                '<img title="'.$this->translate('DONATION_approved'.$item->approved).'" class="donation-icon" src="application/modules/Donation/externals/images/approved'.$item->approved.'.png">'
              );
            ?>
          <?php
            echo $this->htmlLink(
             'javascript:void(0)',
              '<img title="' . $this->translate('Delete') . '" class="donation-icon" src="application/modules/Core/externals/images/delete.png">',
              array('onClick' => "confirmDelete({$item->getIdentity()})")
            )
          ?>
        </td>
      </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br/>

  <div class='buttons'>
    <button type='submit' name="submit_button" value="delete">
      <?php echo $this->translate("Delete Selected") ?>
    </button>
  </div>
</form>

