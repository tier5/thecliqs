<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: commission-list.tpl  16.05.12 18:36 TeaJay $
 * @author     Taalay
 */
?>
<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if( count($this->navigation) ): ?>
  <div class='store_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

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
    $('search_form').submit();
  }
</script>

<?php echo $this->render('admin/_statisticsMenu.tpl'); ?>

<br />

<div class="settings admin_home_middle" style="clear: none;">

  <h3><?php echo $this->translate("STORE_Commission List Statistics by Product and by Store") ?></h3>
  <p>
    <?php echo $this->translate("STORE_Commission List Statistics by Product and by Store") ?>
  </p>

  <br />

  <div class="admin_search">
    <div class="search store">
      <?php echo $this->searchForm->render($this) ?>
    </div>
  </div>

  <br />

  <div class='admin_results'>
    <div>
      <?php $count = $this->paginator->getTotalItemCount() ?>
      <?php if ($this->type == 'item_id') : ?>
        <?php echo $this->translate(array("%s product found.", "%s products found.", $count),
            $this->locale()->toNumber($count)) ?>
      <?php else : ?>
        <?php echo $this->translate(array("%s store found.", "%s stores found.", $count),
            $this->locale()->toNumber($count)) ?>
      <?php endif; ?>
    </div>
    <div>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
        'params' => $this->formValues
      )); ?>
    </div>
  </div>

  <br />

  <form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'commission-list'), null, true);?>" onSubmit="return multiModify()" style="width: 720px; background: none; padding: 0">
    <table class='admin_table page_packages'>
      <thead>
        <tr>
          <?php if ($this->type == 'item_id') : ?>
            <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('item_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
            <th><a href="javascript:void(0);" onclick="javascript:changeOrder('name', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
          <?php else : ?>
            <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('page_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
          <?php endif; ?>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('store_title', 'ASC');"><?php echo $this->translate("Store") ?></a></th>
          <th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('total_amount', 'ASC');"><?php echo $this->translate("Gross Amount") ?></a></th>
          <th class="admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('quantity', 'ASC');"><?php echo $this->translate("Sell Count") ?></a></th>
        </tr>
      </thead>
      <tbody>
        <?php if( count($this->paginator) ): ?>
          <?php foreach( $this->paginator as $item ): ?>
            <tr>
              <?php if ($this->type == 'item_id') : ?>
                <td><?php echo $item->item_id ?></td>
                <td class='admin_table_bold'>
                  <?php if(null != ($product = $item->getItem())): ?>
                  <?php echo $this->htmlLink($product->getHref(),
                      $this->string()->truncate($product->getTitle(), 100),
                      array('target' => '_blank'))?>
                  <?php else: ?>
                    <?php echo $item->getTitle(); ?>
                  <?php endif; ?>
                </td>
              <?php else : ?>
                <td><?php echo $item->page_id ?></td>
              <?php endif; ?>
              <td>
                <?php
                /**
                 * @var $page Page_Model_Page
                 */
                if(isset($this->pages[$item->page_id])): $page = $this->pages[$item->page_id]; ?>
                  <?php echo $this->htmlLink($page->getHref(),
                      $this->string()->truncate($page->getTitle(), 100),
                      array('target' => '_blank'))?>
                <?php endif; ?>
              </td>
              <td class="center">
                <span class="store-price">
                  <?php echo $this->toCurrency($item->total_amount); ?>
                </span>
              </td>
              <td class="center"><?php echo $item->quantity; ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </form>
</div>