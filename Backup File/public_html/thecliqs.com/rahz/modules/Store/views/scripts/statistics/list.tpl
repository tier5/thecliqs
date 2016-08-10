<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline store">
  <h2><?php echo $this->translate('List Statistic');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>


<div class="layout_middle" style="clear: none;">

  <p>
    <?php echo $this->translate("STORE_VIEWS_SCRIPTS_STATISTICS_LIST_DESCRIPTION") ?>
  </p>

  <br />

  <div><?php echo $this->searchForm->render($this); ?></div>

</div>

<?php if (!isset($this->paginator) || $this->paginator->getTotalItemCount() <= 0 ) : ?>

<div class="tip">
  <span>
    <?php echo $this->translate('STORE_There no products has been sold yet.');?>
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
    $('search_form').submit();
  }
</script>

<br/>
<div class="layout_middle">

  <div class="store-list-result">
      <div>
        <?php $count = $this->paginator->getTotalItemCount() ?>
        <?php echo $this->translate(array("%s product found.", "%s products found.", $count),
            $this->locale()->toNumber($count)) ?>
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

  <table class='table store-product-list'>
    <thead>
    <tr>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('item_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('name', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
      <th class="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('total_amount', 'ASC');"><?php echo $this->translate("Gross Amount") ?></a></th>
      <th class="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('quantity', 'ASC');"><?php echo $this->translate("Sell Count") ?></a></th>
    </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>
        <tr>
          <td><?php echo $item->item_id ?></td>
          <td style="font-weight: bold">
            <?php if(null != ($product = $item->getItem())): ?>
            <?php echo $this->htmlLink($product->getHref(),
                $this->string()->truncate($product->getTitle(), 100),
                array('target' => '_blank'))?>
            <?php else: ?>
              <?php echo $item->getTitle(); ?>
            <?php endif; ?>
          </td>
          <td class="center">
            <span class="store-price"><?php echo $this->toCurrency($item->total_amount); ?></span>
          </td>
          <td class="center"><?php echo $item->quantity; ?></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>