<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
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

<br/>

<?php if ($this->isPageEnabled) : ?>
  <script type="text/javascript">
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';

    var changeOrder = function(order, default_direction){
      if( order == currentOrder ) {
        $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('order').value = order;
        $('order_direction').value = default_direction;
      }
      $('filter_form').submit();
    }
  </script>

  <p>
    <?php echo $this->translate("This is a list all stores created by your users. Use form to filter stores.") ?>
  </p>
  <br />

  <div class='admin_search'>
    <?php echo $this->filterForm->render($this); ?>
  </div>
  <br />

  <div class='admin_results'>
    <div>
      <?php $storeCount = $this->paginator->getTotalItemCount() ?>
      <?php echo $this->translate(array("%s store found.", "%s stores found.", $storeCount), $this->locale()->toNumber($storeCount)) ?>
    </div>
    <div>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
        //'params' => $this->formValues,
      )); ?>
    </div>
  </div>
  <br />

  <?php if ($storeCount) : ?>
    <table class='admin_table'>
      <thead>
        <tr>
          <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('page_id', 'DESC');">ID</a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('category', 'ASC');"><?php echo $this->translate("STORE_Category") ?></a></th>
          <th><?php echo $this->translate("Owner") ?></th>
          <th class="center"><?php echo $this->translate("Current Amount") ?></th>
          <th class="center"><?php echo $this->translate("STORE_Products") ?></th>
          <th class="center"><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($this->paginator as $item):	 ?>
        <?php
          if($item->name == 'default') continue;
          $products_count = $this->products[$item->page_id];
          $balances = $this->balances[$item->page_id];
        ?>
          <tr class="<?php if ($item->sponsored) echo "admin_featured_page"; ?>">
            <td><?php echo $item->getIdentity() ?></td>
            <td><?php echo $this->htmlLink($item->getHref(), ($item->getTitle() ? $item->getTitle() : "<i>".$this->translate("Untitled")."</i>" )); ?></td>
            <td><?php echo ($item->category ? $item->category : ("<i>".$this->translate("Uncategorized")."</i>")); ?></td>
            <td><?php echo $this->htmlLink($this->user($item->user_id)->getHref(), $this->user($item->user_id)->getTitle()); ?></td>
            <td class="center">
              <span class="store-price">
                <?php if (!_ENGINE_ADMIN_NEUTER) : ?>
                  <?php echo $this->toCurrency($balances->current_amt); ?>
                <?php else : ?>
                  (hidden)
                <?php endif; ?>
              </span>
            </td>
            <td class="center">
              <?php
                if ($products_count) {
                  echo $this->htmlLink(
                    array(
                      'route' => 'admin_default',
                      'module' => 'store',
                      'controller' => 'products',
                      'action' => 'index',
                      'page_id' => $item->page_id
                    ),
                    $products_count
                  );
                } else {
                  echo 0;
                }
              ?>
            </td>
            <td class="center">
              <?php
                //sponsored
                echo $this->htmlLink(
                   array(
                     'route' => 'admin_default',
                     'module' => 'store',
                     'controller' => 'stores',
                     'action' => 'sponsor',
                     'page_id' => $item->page_id,
                     'value' => 1-$item->sponsored
                   ),
                   '<img title="'.$this->translate('PAGE_sponsored'.$item->sponsored).'" class="page-icon" src="application/modules/Page/externals/images/sponsored'.$item->sponsored.'.png">'
                );
                //featured
                echo $this->htmlLink(
                 array(
                   'route' => 'admin_default',
                   'module' => 'store',
                   'controller' => 'stores',
                   'action' => 'feature',
                   'page_id' => $item->page_id,
                   'value' => 1-$item->featured
                 ),
                 '<img title="'.$this->translate('PAGE_featured'.$item->featured).'" class="page-icon" src="application/modules/Page/externals/images/featured'.$item->featured.'.png">'
                );
              ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate("STORE_There are no stores") ?>
      </span>
    </div>
  <?php endif; ?>
<?php else:?>
  <?php echo $this->translate('STORE_You have to enable or install HE Page Plugin to display stores'); ?>
<?php endif; ?>