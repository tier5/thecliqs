<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _productsMenu.tpl 2011-09-21 17:53 mirlan $
 * @author     Mirlan
 */
?>
<script type="text/javascript">

function confirmDelete(product_id)
{
  if (confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this product?")) ?>'))
  {
    window.location.href = '<?php echo $this->url(array('module'=>'store', 'controller'=>'products', 'action' => 'delete'),
      'admin_default', true); ?>/product_id/'+product_id;
  }else{
	  return false;
  }
}
</script>

<div class="admin_home_right" style="width:200px">
  <ul class="admin_home_dashboard_links">
  <li style="width:200px">
    <ul >

      <li class="hecore-menu-tab products <?php if ($this->menu == 'edit'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'store', 'controller'=>'products', 'action'=>'edit', 'product_id'=> $this->product->getIdentity()), 'admin_default', true),
          $this->translate('Edit main settings'),
          array('class'=>'icon_main_edit', 'style'=>'float: none')
        );?>
      </li>

      <br/>

      <?php if ($this->product->isDigital()) : ?>
        <li class="hecore-menu-tab products <?php if (in_array($this->menu, array('create-file', 'edit-file'))): ?>active-menu-tab<?php endif; ?>">
          <?php echo $this->htmlLink(
            $this->url(array('module'=>'store', 'controller'=>'digital', 'action'=>'edit-file', 'product_id'=> $this->product->getIdentity()), 'admin_default', true),
            $this->translate('STORE_Manage File'),
            array('class'=>'icon_file_edit', 'style'=>'float: none')
          );?>
        </li>
      <?php else: ?>
      <li class="hecore-menu-tab products <?php if (in_array($this->menu, array('product-locations'))): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'store', 'controller'=>'product-locations', 'product_id'=> $this->product->getIdentity()), 'admin_default', true),
          $this->translate('STORE_Manage Shipping Locations'),
          array('class'=>'icon_shipping_edit', 'style'=>'float: none')
        );?>
      </li>
      <?php endif; ?>

      <li class="hecore-menu-tab products <?php if (in_array($this->menu, array('addphotos', 'editphotos'))): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
            $this->url(array('module'=>'store', 'controller'=>'products', 'action'=>'editphotos', 'product_id'=> $this->product->getIdentity()), 'admin_default', true),
            $this->translate('STORE_Manage photos'),
            array('class'=>'icon_manage_photos', 'style'=>'float: none')
            );?>
      </li>

      <li class="hecore-menu-tab products <?php if (in_array($this->menu, array('edit-video', 'add-video'))): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'store', 'controller'=>'videos', 'action'=>'edit-video', 'product_id'=> $this->product->getIdentity()), 'admin_default', true),
          $this->translate('STORE_Manage video'),
          array('class'=>'icon_manage_video', 'style'=>'float: none')
        );?>
      </li>

      <li class="hecore-menu-tab products <?php if (in_array($this->menu, array('edit-audio', 'create-audio'))): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'store', 'controller'=>'audios', 'action'=>'edit-audio', 'product_id'=> $this->product->getIdentity()), 'admin_default', true),
          $this->translate('STORE_Manage audios'),
          array('class'=>'icon_manage_audios', 'style'=>'float: none')
        );?>
      </li>

      <br/>

      <li>
        <?php echo $this->htmlLink(
          $this->product->getHref(),
          $this->translate('STORE_View Product'),
          array('target' => '_blank', 'class' => 'icon_view', 'style'=>'float: none')
        ); ?>
      </li>

      <li>
        <?php echo $this->htmlLink(
          'javascript://',
          $this->translate('Delete Product'),
          array('class'=>'icon_delete', 'style'=>'float: none', 'onclick'=>'confirmDelete(' . $this->product->getIdentity() . ')')
        ); ?>
      </li>

      <br/>
      <li>
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'store', 'controller'=>'products', 'action'=>'create'), 'admin_default', true),
          $this->translate('STORE_Add New Product'),
          array('class'=>'icon_product_add', 'style'=>'float: none')
        ); ?>
      </li>
      <li class="hecore-menu-tab products <?php if ($this->menu == 'level'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'store', 'controller'=>'products', 'action'=>'index'), 'admin_default', true),
          $this->translate('List of products'),
          array('class'=>'icon_products', 'style'=>'float: none')
        );?>
      </li>

    </ul>
  </li>
</ul>
</div>