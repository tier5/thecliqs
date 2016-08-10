<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit-file.tpl  21.09.11 17:50 TeaJay $
 * @author     Taalay
 */
?>

<?php echo $this->getGatewayState(0); ?>

<?php echo $this->render('admin/_productsMenu.tpl'); ?>

<ul class="admin_store_product_file">
  <li>
    <div class="admin_store_product_file_options">
      <ul>
        <li>
          <?php echo $this->htmlLink(
            $this->url(array('module' => 'store', 'controller' => 'digital', 'action' => 'delete-file', 'product_id' => $this->product->getIdentity()), 'admin_default', true),
            '<img title="'.$this->translate('Delete').'" src="application/modules/Store/externals/images/delete_file.png">',
            array('class' => 'smoothbox')
          );?>
        </li>
      </ul>
    </div>
    <div class="admin_store_product_file_title">
      <h3>
        <?php echo $this->file->name;?>
      </h3>
    </div>
  </li>
</ul>