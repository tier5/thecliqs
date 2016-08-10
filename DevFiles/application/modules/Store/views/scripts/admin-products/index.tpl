<?php

/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
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
      escapeJavascript($this->translate("Are you sure you want to delete the selected pages?")) ?>');
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
  function confirmDelete(product_id) {
    if (confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this product?")) ?>')) {
      window.location.href = '<?php echo $this->url(array('module' => 'store', 'controller' => 'products', 'action' => 'delete'),
        'admin_default', true); ?>/product_id/' + product_id;
    } else {
      return false;
    }
  }

</script>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if (count($this->navigation)): ?>
<div class='store_admin_tabs'>
  <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<p>
  <?php echo $this->translate("STORE_VIEWS_SCRIPTS_ADMINPINDEX_PRODUCTS_DESCRIPTION") ?>
</p>

<br />

<div class='admin_search'>
  <?php  echo $this->filterForm->render($this);  ?>
</div>

<br />

<div class='admin_results'>
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

  <?php echo $this->htmlLink(
    $this->url(array('module' => 'store', 'controller' => 'products', 'action' => 'create'), 'admin_default', true),
    $this->translate('STORE_Add New Product'),
    array('class' => 'buttonlink icon_add')
  ); ?>
</div>
<br />

<form id='multimodify_form' method="post" action="<?php echo $this->url(array('action' => 'multi-modify'));?>" onSubmit="return multiModify()">
  <table class='admin_table page_packages'>
    <thead>
    <tr>
      <th><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
      <th class='admin_table_short'><a href="javascript:void(0);"
        onclick="javascript:changeOrder('p.product_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
      <th><a href="javascript:void(0);"
        onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
      <th><a href="javascript:void(0);"
        onclick="javascript:changeOrder('category', 'ASC');"><?php echo $this->translate("STORE_Category") ?></a></th>
      <?php if ($this->storeEnabled): ?>
      <th><a href="javascript:void(0);"
        onclick="javascript:changeOrder('s.title', 'ASC');"><?php echo $this->translate("Store") ?></a></th>
      <?php endif; ?>
      <th><a href="javascript:void(0);"
        onclick="javascript:changeOrder('u.username', 'ASC');"><?php echo $this->translate("Owner") ?></a></th>
      <th class="admin_table_centered"><a href="javascript:void(0);"
        onclick="javascript:changeOrder('p.price', 'ASC');"><?php echo $this->translate("Price") ?></a></th>
      <th class="admin_table_centered"><a href="javascript:void(0);"
        onclick="javascript:changeOrder('p.quantity', 'ASC');"><?php echo $this->translate("Amount") ?></a></th>
      <th class="center"><a href="javascript:void(0);"
        onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>
      <th class='center admin_table_options'><?php echo $this->translate("Options") ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($this->paginator)): ?>
      <?php foreach ($this->paginator as $item): ?>
        <tr class="<?php if (($item->hasStore() && !$item->getStore()->isStore()) || !$item->getQuantity())
          echo 'disabled-product' ?>">
          <td>
            <input
              name="modify[]"
              value=<?php echo $item->getIdentity();?>
                type='checkbox' class='checkbox'>
          </td>
          <td><?php echo $item->product_id ?></td>
          <td class='admin_table_bold'>
            <?php echo $this->htmlLink($item->getHref(),
            $this->string()->truncate($item->getTitle(), 15),
            array('title' => $item->getTitle(), 'target' => '_blank'))?>
          </td>
          <td><?php echo ($item->category ? $item->category : ("<i>" . $this->translate("Uncategorized") . "</i>")); ?></td>
          <?php if ($this->storeEnabled): ?>
          <td
            class='admin_table_store'><?php if ($item->hasStore()): ?><?php echo $this->htmlLink($item->getStore()->getHref(), $item->getStore()->getTitle(), array('target' => '_blank')) ?><?php endif; ?></td>
          <?php endif; ?>
          <td
            class='admin_table_owner'><?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target' => '_blank')) ?></td>
          <td class="center"><?php echo $this->getPrice($item); ?></td>
          <td
            class='center'><?php echo ($item->getQuantity() === true) ? $this->translate('STORE_Digital') : $item->getQuantity(); ?></td>
          <td class="center"><?php echo $item->creation_date ?></td>
          <td class='center admin_table_options'>
            <?php
            echo $this->htmlLink(
              array(
                'module' => 'store',
                'controller' => 'products',
                'action' => 'sponsor',
                'product_id' => $item->getIdentity(),
                'value' => 1 - $item->sponsored
              ),
              '<img title="' . $this->translate('STORE_sponsored' . $item->sponsored) . '" class="product-icon" src="application/modules/Store/externals/images/admin/sponsored' . $item->sponsored . '.png">'
            );

            echo $this->htmlLink(
              array(
                'module' => 'store',
                'controller' => 'products',
                'action' => 'feature',
                'product_id' => $item->getIdentity(),
                'value' => 1 - $item->featured
              ),
              '<img title="' . $this->translate('STORE_featured' . $item->featured) . '" class="product-icon" src="application/modules/Store/externals/images/admin/featured' . $item->featured . '.png">'
            );
            ?>
            <?php if ($this->viewer->getIdentity() == $item->owner_id): ?>
            <?php if ($item->hasStore()): ?>
              <a
                href='<?php echo $this->url(array('action' => 'edit', 'page_id' => $item->getStore()->getIdentity(), 'product_id' => $item->product_id), 'store_products');?>'
                target="_blank">
                <img title="<?php echo $this->translate('Edit') ?>" class="product-icon"
                  src="application/modules/User/externals/images/edit.png"></a>
              <?php
              echo $this->htmlLink(
                $this->url(array('action' => 'copy', 'page_id' => $item->getStore()->getIdentity(), 'product_id' => $item->getIdentity()), 'store_products'),
                '<img title="' . $this->translate('STORE_Copy Product') . '" class="product-icon" src="application/modules/Store/externals/images/copy_product.png">',
                array('target' => '_blank')
              );
              ?>
              <?php else : ?>
              <a href='<?php echo $this->url(array('action' => 'edit', 'product_id' => $item->product_id));?>'>
                <img title="<?php echo $this->translate('Edit') ?>" class="product-icon"
                  src="application/modules/User/externals/images/edit.png"></a>
              <?php
              echo $this->htmlLink(
                $this->url(array('action' => 'copy', 'product_id' => $item->getIdentity())),
                '<img title="' . $this->translate('STORE_Copy Product') . '" class="product-icon" src="application/modules/Store/externals/images/copy_product.png">'
              );
              ?>
              <?php endif; ?>
            <?php endif; ?>
            <?php echo $this->htmlLink(
              'javascript:void(0)',
              '<img title="' . $this->translate('Delete') . '" class="product-icon" src="application/modules/Core/externals/images/delete.png">',
              array('onClick' => "confirmDelete({$item->getIdentity()})"))
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
  <br />

  <div class='buttons'>
    <button type='submit' name="submit_button" value="delete"><?php echo $this->translate("Delete Selected") ?></button>
  </div>
</form>