<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  5/10/12 12:01 PM mt.uulu $
 * @author     Mirlan
 */
?>
<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if (count($this->navigation)): ?>
<div class='store_admin_tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>


<div class="admin_home_middle" style="clear: none;">
  <h3><?php echo $this->translate("STORE_Money Requests") ?></h3>
  <p>
    <?php echo $this->translate("STORE_ADMIN_REQUESTS_DESCRIPTION") ?>
  </p>

  <br/>

  <div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
  </div>
</div>

<br/>


<?php if (count($this->paginator)): ?>
<table class='admin_table'>
  <thead>
  <tr>
    <th class='admin_table_short'>
      <a href="javascript:void(0);" onclick="javascript:changeOrder('request_id', 'DESC');">ID</a>
    </th>
    <th>
      <?php echo $this->translate("Store") ?>
    </th>
    <th class='admin_table_short'>
      <a href="javascript:void(0);" onclick="javascript:changeOrder('amt', 'ASC');">
        <?php echo $this->translate("Amount") ?>
      </a>
    </th>
    <th class='admin_table_short'>
      <a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'ASC');">
        <?php echo $this->translate("Status") ?>
      </a>
    </th>
    <th><?php echo $this->translate("Request Message") ?></th>
    <th><?php echo $this->translate("Response Message") ?></th>
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
    <th class='admin_table_short'><?php echo $this->translate("Options") ?></th>
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
        <?php if ($this->pages[$request->page_id]): ?>
        <?php echo $this->pages[$request->page_id]->__toString(); ?>
        <?php endif; ?>
      </td>
      <td>
        <span class="store-price"><?php echo $this->toCurrency($request->amt); ?></span>
      </td>
      <td>
        <?php echo $this->translate(ucfirst($request->status)); ?>
      </td>
      <td>
        <?php echo $this->viewMore(Engine_String::strip_tags($request->request_message), 100); ?>
      </td>
      <td>
        <?php echo $this->viewMore(Engine_String::strip_tags($request->response_message), 100); ?>
      </td>
      <td>
        <?php echo $this->timestamp($request->request_date) ?>
      </td>
      <td>
        <?php echo ($request->response_date)? $this->timestamp($request->response_date) : ''; ?>
      </td>
      <td>
        <a href='<?php echo $this->url(array('action'     => 'response',
                                             'request_id' => $request->request_id));?>'>
          <?php if ($request->status == 'waiting'): ?>
          <?php echo $this->translate("response"); ?>
          <?php else: ?>
          <?php echo $this->translate("details"); ?>
          <?php endif; ?>
        </a>
      </td>
    </tr>
      <?php endforeach; ?>
  </tbody>
</table>

<br/>
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>

<?php else: ?>
<div class="tip">
    <span>
      <?php echo $this->translate("There are no requests by your store owners yet.") ?>
    </span>
</div>
<?php endif; ?>
