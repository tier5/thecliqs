<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  5/16/12 4:58 PM mt.uulu $
 * @author     Mirlan
 */
?>
<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline store">
  <h2><?php echo $this->translate('Request Money');?></h2>
</div>
<div class="clr"></div>

<div class="layout_right store-short-desc">
  <h4><?php echo $this->translate('Short Information'); ?></h4>
  <div class="profile_fields">
    <table>
      <tr>
        <td style="padding: 3px"><?php echo $this->translate('Current Balance'); ?>:</td>
        <td style="padding: 3px" class="store-price">
          <?php echo $this->toCurrency($this->balances->getBalance()) ?>
        </td>
      </tr>
      <tr>
        <td style="padding: 3px"><?php echo $this->translate('Requested Amount'); ?>:</td>
        <td style="padding: 3px" class="store-price">
          <?php echo $this->toCurrency($this->balances->getRequested()) ?>
        </td>
      </tr>
      <tr>
        <td style="padding: 3px"><?php echo $this->translate('Last Request Date'); ?>:</td>
        <td style="padding: 3px">
          <?php echo (($this->balances->requested_date)? $this->timestamp($this->balances->requested_date):$this->translate('Never')); ?>
        </td>
      </tr>
      <tr>
        <td style="padding: 3px"><?php echo $this->translate('Pending Amount'); ?>:</td>
        <td style="padding: 3px" class="store-price">
          <?php echo $this->toCurrency($this->balances->getPending()) ?>
        </td>
      </tr>
      <tr>
        <td style="padding: 3px"><?php echo $this->translate('Transferred Amount'); ?>:</td>
        <td style="padding: 3px" class="store-price">
          <?php echo $this->toCurrency($this->balances->getTransfer()) ?>
        </td>
      </tr>
      <tr>
        <td style="padding: 3px"><?php echo $this->translate('Last Transfer Date'); ?>:</td>
        <td style="padding: 3px">
          <?php echo (($this->balances->transferred_date)? $this->timestamp($this->balances->transferred_date):$this->translate('Never')); ?>
        </td>
      </tr>
      <tr>
        <td style="padding: 3px" colspan="2">
          <a href="<?php echo $this->url(array(
            'controller' => 'requests',
            'action' => 'request',
            'page_id' => $this->page->getIdentity(),
            'balance_id' => $this->balances->getIdentity()),
            'store_extended', true); ?>" class="smoothbox" style="text-decoration: none;">
            <button><?php echo $this->translate('Request Money'); ?></button>
          </a>
        </td>
      </tr>
    </table>
  </div>
</div>

<div class="layout_middle" style="clear: none;">
  <p>
    <?php echo $this->translate("STORE_VIEWS_SCRIPTS_REQUEST_DESCRIPTION") ?>
  </p>

  <br />

  <div><?php echo $this->filterForm->render($this);?></div>
</div>

<?php if ($this->paginator->getTotalItemCount() <= 0): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('STORE_There no request has been done yet.');?>
    </span>
  </div>
<?php return; endif; ?>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';

  var changeOrder = function(order, default_direction)
  {
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }
</script>
<br/>
<div class="layout_middle">

  <div class="store-list-result">
    <div>
      <?php $count = $this->paginator->getTotalItemCount() ?>
      <?php echo $this->translate(array("%s request found", "%s requests found", $count), $this->locale()->toNumber($count)) ?>
    </div>
    <div>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'query'       => $this->formValues,
        'pageAsQuery' => true
      )); ?>
    </div>
  </div>

  <br/>

  <?php if ($this->paginator->getTotalItemCount() > 0): ?>

  <table class='table store-product-list' style="width:100%">
    <thead>
    <tr>
      <th class='table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('request_id', 'DESC');">ID</a>
      </th>
      <th class='table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('amt', 'ASC');">
          <?php echo $this->translate("Amount") ?>
        </a>
      </th>
      <th class='table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'ASC');">
          <?php echo $this->translate("Status") ?>
        </a>
      </th>
      <th>
        <?php echo $this->translate("Request Message") ?>
      </th>
      <th>
        <?php echo $this->translate("Response Message") ?>
      </th>
      <th>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('request_date', 'DESC');">
          <?php echo $this->translate("Request Date") ?>
        </a>
      </th>
      <th>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('response_date', 'DESC');">
          <?php echo $this->translate("Response Date") ?>
        </a>
      </th>
      <th class='table_short'>
        <?php echo $this->translate("Options") ?>
      </th>
    </tr>
    </thead>
    <tbody>
      <?php
      /**
       * @var $request Store_Model_Request
       */
      foreach ($this->paginator as $request):
        ?>
      <tr>
        <td><?php echo $request->getIdentity() ?></td>
        <td>
          <?php echo $this->toCurrency($request->amt); ?>
        </td>
        <td>
          <?php echo $this->translate(ucfirst($request->status)); ?>
        </td>
        <td>
          <?php echo $this->viewMore(Engine_String::strip_tags($request->request_message)); ?>
        </td>
        <td>
          <?php echo $this->viewMore(Engine_String::strip_tags($request->response_message)); ?>
        </td>
        <td>
          <?php echo $this->timestamp($request->request_date) ?>
        </td>
        <td>
          <?php echo ($request->response_date) ? $this->timestamp($request->request_date) : ''; ?>
        </td>
        <td>
          <?php if ($request->status == 'waiting'): ?>
            <a class="smoothbox" href='<?php echo $this->url(array(
              'controller'     => 'requests',
              'action'         => 'cancel',
              'request_id'     => $request->request_id,
              'page_id'        => $this->page->getIdentity(),
            ), 'store_extended', true);?>'>
              <?php echo $this->translate("cancel"); ?>
            </a>
          <?php else : ?>
            <a class="smoothbox" href='<?php echo $this->url(array(
                'controller'     => 'requests',
                'action'         => 'detail',
                'request_id'     => $request->request_id,
                'page_id'        => $this->page->getIdentity(),
              ), 'store_extended', true);?>'>
              <?php echo $this->translate("details"); ?>
            </a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>