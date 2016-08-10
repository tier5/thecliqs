<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl  19.09.11 14:09 TeaJay $
 * @author     Taalay
 */
?>

<?php
	$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js')
?>

<script type="text/javascript">
var product_id = <?php echo $this->product->getIdentity() ?>;

if (product_id > 0) {
  $$('#product_id option').each(function(el, index) {
    if (el.value == product_id)
      $('product_id').selectedIndex = index;
  });
}

en4.core.runonce.add(function(){
  $('form-upload-audio').setStyle('clear', 'none');
});
</script>

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
        <?php if (count($this->audios)) : ?>
          <?php echo $this->htmlLink($this->url(array('controller' => 'audios', 'action' => 'edit', 'product_id' => $this->product->getIdentity()), 'store_extended'), $this->translate('Edit Audios'), array(
              'class' => 'buttonlink product_audios_edit',
              'id' => 'store_product_editaudios',
            )) ?>
            <br>
        <?php endif; ?>
      </div>
    </li>
  </ul>
</div>

<?php echo $this->form->render($this) ?>