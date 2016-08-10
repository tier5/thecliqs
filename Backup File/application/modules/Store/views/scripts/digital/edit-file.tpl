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

<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline store">
  <h2><?php echo $this->translate('STORE_Manage Products');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>

<div class="he-items" style="float: right; margin: 30px 0">
  <ul class="he-item-list">
    <li>
      <div class="he-item-options">
        <?php echo $this->htmlLink($this->url(array('action' => 'edit', 'page_id' => $this->page->getIdentity(),'product_id' => $this->product->getIdentity()), 'store_products'), $this->translate('Back'), array(
          'class' => 'buttonlink product_back',
          'id' => 'store_product_editsettings',
        )) ?>
        <br>
      </div>
    </li>
  </ul>
</div>

<h2>
  <?php echo $this->translate('Edit File of Product') . ' ' . $this->htmlLink($this->product->getHref(), $this->product->getTitle()); ?>
</h2>

<ul class="store_product_file">
  <li>
    <div class="store_product_file_options">
      <ul>
        <li>
          <?php echo $this->htmlLink(
            $this->url(array('controller' => 'digital', 'action' => 'delete-file', 'product_id' => $this->product->getIdentity()), 'store_extended', true),
            '<img title="'.$this->translate('Delete').'" src="application/modules/Store/externals/images/delete_file.png">',
            array('class' => 'smoothbox')
          );?>
        </li>
      </ul>
    </div>
    <div class="store_product_file_title">
      <h3>
        <?php echo $this->file->name;?>
      </h3>
    </div>
  </li>
</ul>